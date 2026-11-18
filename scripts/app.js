/*
 * COMPRESS — free, private, in-browser image compressor by Asresoft.
 * https://github.com/talhaasre/compress-app
 *
 * Everything runs locally: images are decoded, re-encoded and zipped in the
 * browser. Nothing is uploaded.
 *
 * One function, compressImage(), produces the result that is previewed AND
 * downloaded, so the file you download is always the one you compared.
 */
(function () {
  'use strict';

  // ---------------------------------------------------------------- config
  const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB per image
  const MAX_FILES = 10;                   // images per batch
  const INPUT_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/bmp'];
  const DEFAULT_QUALITY = { 'image/jpeg': 60, 'image/webp': 65, 'image/png': 85 };
  const FORMAT_NAMES = { 'image/jpeg': 'JPEG', 'image/png': 'PNG', 'image/webp': 'WebP', 'image/bmp': 'BMP' };
  const FORMAT_EXT = { 'image/jpeg': 'jpg', 'image/png': 'png', 'image/webp': 'webp' };

  // ------------------------------------------------------------------- DOM
  const $ = (id) => document.getElementById(id);
  const canvas = $('comparisonCanvas');
  const ctx = canvas.getContext('2d');
  const fileInput = $('fileInput');
  const dropZone = $('dropZone');
  const comparisonSlider = $('comparisonSlider');
  const tagline = $('tagline');
  const tagline2 = $('tagline2');
  const dragNotice = $('dragNotice');
  const zoomControls = $('zoomControls');
  const formatSelect = $('formatSelect');
  const resizeSelect = $('resizeSelect');
  const qualitySlider = $('qualitySlider');
  const qualityValue = $('qualityValue');
  const qualityDisplay = $('qualityDisplay');
  const qualityLabel = $('qualityLabel');
  const originalSize = $('originalSize');
  const originalMeta = $('originalMeta');
  const optimizedSize = $('optimizedSize');
  const optimizedMeta = $('optimizedMeta');
  const downloadBtn = $('downloadBtn');
  const downloadBtnText = $('downloadBtnText');
  const controlPanel = $('controlPanel');
  const imageGallery = $('imageGallery');
  const galleryGrid = $('galleryGrid');
  const galleryTitle = $('galleryTitle');
  const applyToAllBtn = $('applyToAllBtn');
  const floatingDownloadBtn = $('floatingDownloadBtn');
  const floatingBtn = floatingDownloadBtn.querySelector('button');
  const errorModal = $('errorModal');

  // ----------------------------------------------------------------- state
  /** @type {Array<{id:number,file:File,name:string,type:string,thumbUrl:string,img:HTMLImageElement|null,format:string,quality:number,result:object|null}>} */
  let images = [];
  let selected = -1;
  let nextId = 1;
  let renderToken = 0;
  let pendingFiles = null;
  let busy = false;

  // Comparison view state
  let originalImage = null;
  let optimizedImage = null;
  let sliderPosition = 0.5;
  let scale = 1, offsetX = 0, offsetY = 0;
  let isDraggingSlider = false, isPanning = false, startX = 0, startY = 0;

  const webpSupported = (() => {
    try {
      const c = document.createElement('canvas');
      c.width = c.height = 1;
      return c.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    } catch (e) { return false; }
  })();

  // --------------------------------------------------------------- helpers
  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }

  function formatFileSize(bytes) {
    if (bytes < 1024 * 1024) return Math.max(0.1, bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
  }

  function normaliseType(type) {
    return type === 'image/jpg' || type === 'image/pjpeg' ? 'image/jpeg' : type;
  }

  /** Output format for an input type: BMP can't be encoded by browsers, so it becomes PNG. */
  function defaultOutputFormat(type) {
    if (type === 'image/bmp') return 'image/png';
    if (type === 'image/webp' && !webpSupported) return 'image/jpeg';
    return type;
  }

  function baseName(name) {
    const dot = name.lastIndexOf('.');
    return dot > 0 ? name.slice(0, dot) : name;
  }

  function outputName(item) {
    return `${baseName(item.name)}-optimized.${FORMAT_EXT[item.result.format] || 'img'}`;
  }

  function settingsKey(item) {
    return `${item.format}|${item.quality}|${resizeSelect.value}`;
  }

  function canvasToBlob(c, type, quality) {
    return new Promise((resolve, reject) => {
      c.toBlob((b) => (b ? resolve(b) : reject(new Error('Your browser could not encode this image.'))), type, quality);
    });
  }

  function loadImage(src) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.onload = () => resolve(img);
      img.onerror = () => reject(new Error('This image could not be read. It may be damaged or in an unsupported format.'));
      img.src = src;
    });
  }

  function track(event, params) {
    try { if (typeof window.gtag === 'function') window.gtag('event', event, params || {}); } catch (e) { /* analytics is optional */ }
  }

  // ------------------------------------------------- PNG quantisation worker
  let pngWorker = null;
  let pngJobId = 0;
  const pngJobs = new Map();

  let workerBroken = false;
  function getPngWorker() {
    if (pngWorker || workerBroken) return pngWorker;
    try {
      pngWorker = new Worker('scripts/png-worker.js');
      pngWorker.onmessage = (e) => {
        const job = pngJobs.get(e.data.id);
        if (!job) return;
        pngJobs.delete(e.data.id);
        if (e.data.error) job.reject(new Error(e.data.error));
        else job.resolve(new Blob([e.data.png], { type: 'image/png' }));
      };
      pngWorker.onerror = (e) => {
        // The worker failed to start or crashed: fail pending jobs and stop using it.
        if (e && e.preventDefault) e.preventDefault();
        pngJobs.forEach((job) => job.reject(new Error('PNG encoder failed. Please refresh and try again.')));
        pngJobs.clear();
        pngWorker = null;
        workerBroken = true;
      };
    } catch (e) {
      pngWorker = null;
    }
    return pngWorker;
  }

  /** Lossy PNG: reduce to a colour palette (like TinyPNG). Falls back to the main thread if workers are unavailable. */
  function encodePalettePng(imageData, colors) {
    const worker = getPngWorker();
    if (!worker) {
      if (!window.UPNG) return Promise.reject(new Error('PNG encoder not loaded'));
      const png = window.UPNG.encode([imageData.data.buffer], imageData.width, imageData.height, colors);
      return Promise.resolve(new Blob([png], { type: 'image/png' }));
    }
    return new Promise((resolve, reject) => {
      const id = ++pngJobId;
      pngJobs.set(id, { resolve, reject });
      const buffer = imageData.data.buffer;
      worker.postMessage({ id, buffer, width: imageData.width, height: imageData.height, colors }, [buffer]);
    });
  }

  /** PNG "quality" maps to palette size; 100 means lossless. */
  function pngColors(quality) {
    if (quality >= 100) return 0;
    if (quality >= 85) return 256;
    if (quality >= 70) return 128;
    if (quality >= 55) return 64;
    return 32;
  }

  // -------------------------------------------------------- the compressor
  async function getDecoded(item) {
    if (!item.img) item.img = await loadImage(item.thumbUrl);
    return item.img;
  }

  /**
   * Compresses one image with its own format/quality and the shared resize
   * setting. Results are cached per settings, so preview, single download and
   * ZIP all reuse the same bytes.
   */
  async function compressImage(item) {
    const key = settingsKey(item);
    if (item.result && item.result.key === key) return item.result;

    const img = await getDecoded(item);
    const srcW = img.naturalWidth, srcH = img.naturalHeight;
    let w = srcW, h = srcH;
    const maxDim = parseInt(resizeSelect.value, 10) || 0;
    if (maxDim && (w > maxDim || h > maxDim)) {
      const ratio = Math.min(maxDim / w, maxDim / h);
      w = Math.max(1, Math.round(w * ratio));
      h = Math.max(1, Math.round(h * ratio));
    }

    const c = document.createElement('canvas');
    c.width = w;
    c.height = h;
    const cx = c.getContext('2d');
    cx.imageSmoothingEnabled = true;
    cx.imageSmoothingQuality = 'high';
    if (item.format === 'image/jpeg') {
      // JPEG has no transparency: put transparent areas on white instead of black.
      cx.fillStyle = '#ffffff';
      cx.fillRect(0, 0, w, h);
    }
    cx.drawImage(img, 0, 0, w, h);

    let blob;
    if (item.format === 'image/png') {
      const colors = pngColors(item.quality);
      blob = colors === 0
        ? await canvasToBlob(c, 'image/png')
        : await encodePalettePng(cx.getImageData(0, 0, w, h), colors);
    } else {
      blob = await canvasToBlob(c, item.format, item.quality / 100);
    }

    let format = normaliseType(blob.type) || item.format;
    let keptOriginal = false;
    const sameFormat = format === normaliseType(item.type);
    if (sameFormat && w === srcW && h === srcH && blob.size >= item.file.size) {
      // Re-encoding made it bigger: the original is already well optimised.
      blob = item.file;
      keptOriginal = true;
    }

    item.result = { key, blob, format, width: w, height: h, srcWidth: srcW, srcHeight: srcH, keptOriginal };
    return item.result;
  }

  // ------------------------------------------------------------ comparison
  function resizeCanvas() {
    const parent = canvas.parentElement;
    canvas.width = parent.clientWidth;
    canvas.height = parent.clientHeight;
    drawImages();
  }

  function drawImages() {
    if (!originalImage || !optimizedImage) return;
    const cw = canvas.width, ch = canvas.height;
    ctx.clearRect(0, 0, cw, ch);

    const imgRatio = originalImage.naturalWidth / originalImage.naturalHeight;
    let dw, dh;
    if (imgRatio > cw / ch) { dw = cw * scale; dh = dw / imgRatio; }
    else { dh = ch * scale; dw = dh * imgRatio; }
    const dx = offsetX + (cw - dw) / 2;
    const dy = offsetY + (ch - dh) / 2;
    const split = cw * sliderPosition;

    ctx.save();
    ctx.beginPath();
    ctx.rect(0, 0, split, ch);
    ctx.clip();
    ctx.drawImage(originalImage, dx, dy, dw, dh);
    ctx.restore();

    ctx.save();
    ctx.beginPath();
    ctx.rect(split, 0, cw - split, ch);
    ctx.clip();
    ctx.drawImage(optimizedImage, dx, dy, dw, dh);
    ctx.restore();

    comparisonSlider.style.left = `${split}px`;
  }

  function showUploadPage() {
    dropZone.classList.remove('hidden');
    canvas.style.display = 'none';
    comparisonSlider.style.display = 'none';
    zoomControls.style.display = 'none';
    dragNotice.style.display = 'none';
    tagline.style.display = 'block';
    tagline2.style.display = 'block';
    controlPanel.classList.add('hidden');
    imageGallery.style.display = 'none';
    floatingDownloadBtn.style.display = 'none';
  }

  function showCanvasPage() {
    dropZone.classList.add('hidden');
    canvas.style.display = 'block';
    comparisonSlider.style.display = 'block';
    zoomControls.style.display = 'flex';
    tagline.style.display = 'none';
    tagline2.style.display = 'none';
    dragNotice.style.display = 'block';
    controlPanel.classList.remove('hidden');
    updateFloatingButton();
  }

  function updateFloatingButton() {
    const show = window.innerWidth < 768 && images.length > 0;
    floatingDownloadBtn.style.display = show ? 'flex' : 'none';
  }

  // ------------------------------------------------------------- settings UI
  function populateFormatSelect(item) {
    const original = normaliseType(item.type);
    const options = ['image/jpeg', 'image/png'];
    if (webpSupported) options.push('image/webp');
    formatSelect.innerHTML = '';
    options.forEach((value) => {
      const opt = document.createElement('option');
      opt.value = value;
      opt.textContent = FORMAT_NAMES[value] + (value === original ? ' (original)' : '');
      formatSelect.appendChild(opt);
    });
    formatSelect.value = item.format;
  }

  function updateQualityUI(item) {
    qualitySlider.value = item.quality;
    qualityValue.textContent = item.quality;
    if (item.format === 'image/png') {
      const colors = pngColors(item.quality);
      qualityLabel.textContent = 'Quality';
      qualityDisplay.textContent = colors === 0 ? 'Lossless' : `${colors} colours`;
      qualityDisplay.title = 'PNG is compressed by reducing the colour palette. 100% keeps every colour.';
    } else {
      qualityLabel.textContent = 'Quality';
      qualityDisplay.textContent = `${item.quality}%`;
      qualityDisplay.title = '';
    }
  }

  function showStats(item) {
    originalSize.textContent = formatFileSize(item.file.size);
    const r = item.result;
    if (!r) {
      optimizedSize.textContent = '…';
      optimizedMeta.textContent = 'Compressing';
      originalMeta.textContent = item.img ? `${item.img.naturalWidth}×${item.img.naturalHeight}` : '';
      return;
    }
    originalMeta.textContent = `${r.srcWidth}×${r.srcHeight} · ${FORMAT_NAMES[normaliseType(item.type)] || ''}`;
    optimizedSize.textContent = formatFileSize(r.blob.size);
    const saved = 1 - r.blob.size / item.file.size;
    let meta = `${r.width}×${r.height} · ${FORMAT_NAMES[r.format] || ''}`;
    if (r.keptOriginal) meta = 'Already optimised, original kept';
    else if (saved > 0) meta = `−${(saved * 100).toFixed(0)}% · ` + meta;
    else meta = `+${(-saved * 100).toFixed(0)}% larger · ` + meta;
    optimizedMeta.textContent = meta;
    optimizedMeta.classList.toggle('is-worse', !r.keptOriginal && saved <= 0);
  }

  function updateDownloadLabel() {
    downloadBtnText.textContent = images.length > 1 ? `Download ZIP (${images.length} images)` : 'Download';
  }

  function setDownloadEnabled(enabled) {
    downloadBtn.disabled = !enabled;
    floatingBtn.disabled = !enabled;
  }

  // ---------------------------------------------------------------- gallery
  function updateGallery() {
    if (images.length === 0) {
      imageGallery.style.display = 'none';
      return;
    }
    imageGallery.style.display = 'flex';
    applyToAllBtn.style.display = images.length > 1 ? 'inline-flex' : 'none';

    let totalIn = 0, totalOut = 0, done = 0;
    images.forEach((it) => {
      if (it.result && it.result.key === settingsKey(it)) {
        totalIn += it.file.size; totalOut += it.result.blob.size; done++;
      }
    });
    galleryTitle.textContent = images.length > 1 && done === images.length && totalIn > totalOut
      ? `Saved ${formatFileSize(totalIn - totalOut)}`
      : (images.length > 1 ? `${images.length} images` : 'Your image');
    galleryTitle.title = `${images.length} image${images.length > 1 ? 's' : ''}`;

    const fragment = document.createDocumentFragment();
    images.forEach((it, index) => {
      const fresh = it.result && it.result.key === settingsKey(it);
      const saved = fresh ? Math.round((1 - it.result.blob.size / it.file.size) * 100) : null;
      const el = document.createElement('div');
      el.className = 'gallery-item' + (index === selected ? ' selected' : '');
      el.dataset.index = index;
      el.innerHTML = `
        <div class="gallery-image">
          <img src="${it.thumbUrl}" alt="${escapeHtml(it.name)}" loading="lazy" />
          <div class="gallery-overlay">
            <button type="button" class="download-individual-btn" data-action="download" title="Download this image" aria-label="Download ${escapeHtml(it.name)}">
              <i class="fas fa-download"></i>
            </button>
            <button type="button" class="remove-btn" data-action="remove" title="Remove this image" aria-label="Remove ${escapeHtml(it.name)}">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="gallery-info">
          <div class="gallery-name" title="${escapeHtml(it.name)}">${escapeHtml(it.name)}</div>
          <div class="gallery-settings">
            <div class="quality-indicator">${saved === null ? '…' : (saved > 0 ? '−' + saved + '%' : '0%')}</div>
            <div class="format-indicator">${FORMAT_NAMES[it.format]}</div>
          </div>
        </div>`;
      fragment.appendChild(el);
    });
    galleryGrid.innerHTML = '';
    galleryGrid.appendChild(fragment);
    updateDownloadLabel();
  }

  galleryGrid.addEventListener('click', (e) => {
    const itemEl = e.target.closest('.gallery-item');
    if (!itemEl) return;
    const index = parseInt(itemEl.dataset.index, 10);
    const action = e.target.closest('[data-action]');
    if (action && action.dataset.action === 'remove') { removeImage(index); return; }
    if (action && action.dataset.action === 'download') { downloadOne(index, action); return; }
    selectImage(index);
  });

  // --------------------------------------------------------- render cycle
  async function render() {
    const item = images[selected];
    if (!item) return;
    const token = ++renderToken;
    showStats(item);
    try {
      const img = await getDecoded(item);
      if (token !== renderToken) return;
      originalImage = img;
      const result = await compressImage(item);
      if (token !== renderToken) return;
      if (optimizedImage && optimizedImage._url) URL.revokeObjectURL(optimizedImage._url);
      const url = URL.createObjectURL(result.blob);
      const optimized = await loadImage(url);
      optimized._url = url;
      if (token !== renderToken) { URL.revokeObjectURL(url); return; }
      optimizedImage = optimized;
      showCanvasPage();
      drawImages();
      showStats(item);
      setDownloadEnabled(true);
      updateGallery();
      precomputeAll();
    } catch (err) {
      if (token !== renderToken) return;
      console.error(err);
      showMessage('Could not compress this image', err.message || String(err));
    }
  }

  let renderTimer = null;
  function scheduleRender(delay) {
    clearTimeout(renderTimer);
    renderTimer = setTimeout(render, delay);
  }

  /** Compress the other images in the background so savings show in the gallery. */
  let precomputing = false;
  async function precomputeAll() {
    if (precomputing) return;
    precomputing = true;
    try {
      for (let i = 0; i < images.length; i++) {
        const it = images[i];
        if (i === selected || !needsWork(it)) continue;
        try { await compressImage(it); } catch (e) { it.failedKey = settingsKey(it); /* shown when the image is opened */ }
        updateGallery();
      }
    } finally {
      precomputing = false;
    }
    // Settings may have changed while we were busy.
    if (images.some((it, i) => i !== selected && needsWork(it))) setTimeout(precomputeAll, 50);
  }

  function needsWork(it) {
    const key = settingsKey(it);
    return (!it.result || it.result.key !== key) && it.failedKey !== key;
  }

  function selectImage(index) {
    if (index < 0 || index >= images.length) return;
    selected = index;
    const item = images[index];
    populateFormatSelect(item);
    updateQualityUI(item);
    scale = 1; offsetX = 0; offsetY = 0;
    updateGallery();
    render();
  }

  function removeImage(index) {
    const [removed] = images.splice(index, 1);
    if (removed) URL.revokeObjectURL(removed.thumbUrl);
    if (images.length === 0) { clearAll(); return; }
    if (selected >= images.length) selected = images.length - 1;
    else if (index < selected) selected--;
    selectImage(selected);
  }

  function clearAll() {
    images.forEach((it) => URL.revokeObjectURL(it.thumbUrl));
    images = [];
    selected = -1;
    renderToken++;
    originalImage = null;
    optimizedImage = null;
    sliderPosition = 0.5;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    originalSize.textContent = '0 KB';
    optimizedSize.textContent = '0 KB';
    originalMeta.textContent = '';
    optimizedMeta.textContent = '';
    setDownloadEnabled(false);
    showUploadPage();
  }

  // ---------------------------------------------------------------- upload
  function addFiles(fileList) {
    const files = Array.from(fileList || []);
    if (!files.length) return;

    const room = MAX_FILES - images.length;
    const unsupported = files.filter((f) => !INPUT_TYPES.includes(normaliseType(f.type)));
    const supported = files.filter((f) => INPUT_TYPES.includes(normaliseType(f.type)));
    const oversized = supported.filter((f) => f.size > MAX_FILE_SIZE);
    const sized = supported.filter((f) => f.size <= MAX_FILE_SIZE);
    const accepted = sized.slice(0, Math.max(0, room));
    const excess = sized.slice(Math.max(0, room));

    if (unsupported.length || oversized.length || excess.length) {
      const sections = [];
      if (excess.length) sections.push({ icon: 'fa-minus-circle text-secondary', title: `Too many images (max ${MAX_FILES} at a time). These will be skipped:`, files: excess.map((f) => f.name), ok: false });
      if (oversized.length) sections.push({ icon: 'fa-exclamation-triangle text-warning', title: 'Larger than 10 MB:', files: oversized.map((f) => `${f.name} (${formatFileSize(f.size)})`), ok: false });
      if (unsupported.length) sections.push({ icon: 'fa-times-circle text-danger', title: 'Not a supported image (JPG, PNG, WebP, BMP):', files: unsupported.map((f) => f.name), ok: false });
      if (accepted.length) sections.push({ icon: 'fa-check-circle text-success', title: 'Ready to compress:', files: accepted.map((f) => `${f.name} (${formatFileSize(f.size)})`), ok: true });
      pendingFiles = accepted;
      showFileProblems(sections, accepted.length > 0);
      return;
    }
    ingest(accepted);
  }

  function ingest(files) {
    if (!files.length) return;
    const firstNew = images.length;
    files.forEach((file) => {
      const type = normaliseType(file.type);
      const format = defaultOutputFormat(type);
      images.push({
        id: nextId++,
        file,
        name: file.name || `pasted-image.${FORMAT_EXT[type] || 'png'}`,
        type,
        thumbUrl: URL.createObjectURL(file),
        img: null,
        format,
        quality: DEFAULT_QUALITY[format],
        result: null,
      });
    });
    track('images_added', { count: files.length });
    selectImage(firstNew);
  }

  // ----------------------------------------------------------------- modal
  function showFileProblems(sections, canContinue) {
    const body = sections.map((s) => `
      <div class="error-section">
        <h4><i class="fas ${s.icon}"></i> ${escapeHtml(s.title)}</h4>
        ${s.files.map((f) => `<div class="file-item ${s.ok ? 'valid' : 'invalid'}"><i class="fas ${s.ok ? 'fa-check text-success' : 'fa-times text-danger'}"></i> ${escapeHtml(f)}</div>`).join('')}
      </div>`).join('');
    $('errorMessage').innerHTML = body;
    $('errorModalFooter').innerHTML = canContinue
      ? '<button type="button" class="btn btn-danger" data-modal="cancel">Cancel</button><button type="button" class="btn btn-success" data-modal="continue">Continue</button>'
      : '<button type="button" class="btn btn-danger" data-modal="cancel" style="max-width: fit-content;">OK</button>';
    errorModal.style.display = 'flex';
  }

  function showMessage(title, text) {
    $('errorMessage').innerHTML = `<p><strong>${escapeHtml(title)}</strong></p><p>${escapeHtml(text)}</p>`;
    $('errorModalFooter').innerHTML = '<button type="button" class="btn btn-danger" data-modal="cancel" style="max-width: fit-content;">OK</button>';
    errorModal.style.display = 'flex';
  }

  function closeModal() {
    errorModal.style.display = 'none';
    pendingFiles = null;
  }

  errorModal.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-modal]');
    if (e.target === errorModal || (btn && btn.dataset.modal === 'cancel') || e.target.closest('.error-modal-close')) {
      closeModal();
    } else if (btn && btn.dataset.modal === 'continue') {
      const files = pendingFiles || [];
      closeModal();
      ingest(files);
    }
  });

  // -------------------------------------------------------------- download
  function saveBlob(blob, name) {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = name;
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
  }

  async function downloadOne(index, button) {
    const item = images[index];
    if (!item) return;
    const icon = button && button.querySelector('i');
    if (icon) icon.className = 'fas fa-spinner fa-spin';
    if (button) button.disabled = true;
    try {
      await compressImage(item);
      saveBlob(item.result.blob, outputName(item));
      track('download', { count: 1 });
    } catch (err) {
      showMessage('Download failed', err.message || String(err));
    } finally {
      if (icon) icon.className = 'fas fa-download';
      if (button) button.disabled = false;
      updateGallery();
    }
  }

  async function downloadAll() {
    if (!images.length || busy) return;
    if (images.length === 1) {
      const icon = floatingBtn.querySelector('i');
      icon.className = 'fas fa-spinner fa-spin';
      await downloadOne(0, null);
      icon.className = 'fas fa-download';
      return;
    }
    if (typeof JSZip === 'undefined') {
      showMessage('Download failed', 'The ZIP library did not load. Please refresh the page and try again.');
      return;
    }
    busy = true;
    setDownloadEnabled(false);
    const floatingIcon = floatingBtn.querySelector('i');
    floatingIcon.className = 'fas fa-spinner fa-spin';
    try {
      const zip = new JSZip();
      const used = new Set();
      let added = 0;
      for (let i = 0; i < images.length; i++) {
        downloadBtnText.textContent = `Compressing ${i + 1}/${images.length}…`;
        try {
          const it = images[i];
          await compressImage(it);
          let name = outputName(it);
          for (let n = 2; used.has(name.toLowerCase()); n++) {
            name = `${baseName(it.name)}-optimized-${n}.${FORMAT_EXT[it.result.format]}`;
          }
          used.add(name.toLowerCase());
          zip.file(name, it.result.blob);
          added++;
        } catch (err) {
          console.error(`Skipped ${images[i].name}:`, err);
        }
      }
      if (!added) throw new Error('None of the images could be compressed.');
      downloadBtnText.textContent = 'Creating ZIP…';
      // Images are already compressed, so storing is faster than deflating again.
      const blob = await zip.generateAsync({ type: 'blob', compression: 'STORE' });
      const stamp = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-');
      saveBlob(blob, `compressed-images-${stamp}.zip`);
      track('download', { count: added });
    } catch (err) {
      showMessage('Download failed', err.message || String(err));
    } finally {
      busy = false;
      floatingIcon.className = 'fas fa-download';
      setDownloadEnabled(true);
      updateDownloadLabel();
      updateGallery();
    }
  }

  // ------------------------------------------------------------ settings
  qualitySlider.addEventListener('input', () => {
    const item = images[selected];
    if (!item) return;
    item.quality = parseInt(qualitySlider.value, 10);
    updateQualityUI(item);
    optimizedMeta.textContent = 'Compressing';
    scheduleRender(180);
  });

  formatSelect.addEventListener('change', () => {
    const item = images[selected];
    if (!item) return;
    item.format = formatSelect.value;
    item.quality = DEFAULT_QUALITY[item.format];
    updateQualityUI(item);
    updateGallery();
    scheduleRender(0);
  });

  resizeSelect.addEventListener('change', () => {
    updateGallery();
    scheduleRender(0);
  });

  applyToAllBtn.addEventListener('click', () => {
    const item = images[selected];
    if (!item) return;
    images.forEach((it) => { it.format = item.format; it.quality = item.quality; });
    updateGallery();
    precomputeAll();
    const label = applyToAllBtn.textContent;
    applyToAllBtn.textContent = 'Applied!';
    applyToAllBtn.disabled = true;
    setTimeout(() => { applyToAllBtn.textContent = label; applyToAllBtn.disabled = false; }, 1500);
  });

  // --------------------------------------------------------------- inputs
  $('browseBtn').addEventListener('click', () => fileInput.click());
  $('addMoreBtn').addEventListener('click', () => fileInput.click());
  fileInput.addEventListener('change', (e) => {
    addFiles(e.target.files);
    fileInput.value = ''; // allow picking the same file again
  });
  $('clearAllBtn').addEventListener('click', clearAll);
  downloadBtn.addEventListener('click', downloadAll);
  floatingBtn.addEventListener('click', downloadAll);

  // Drop files anywhere on the page, before or after the first upload.
  let dragDepth = 0;
  document.addEventListener('dragenter', (e) => {
    if (!e.dataTransfer || !Array.from(e.dataTransfer.types || []).includes('Files')) return;
    e.preventDefault();
    dragDepth++;
    dropZone.classList.add('drag-over');
  });
  document.addEventListener('dragover', (e) => { e.preventDefault(); });
  document.addEventListener('dragleave', () => {
    dragDepth = Math.max(0, dragDepth - 1);
    if (!dragDepth) dropZone.classList.remove('drag-over');
  });
  document.addEventListener('drop', (e) => {
    e.preventDefault();
    dragDepth = 0;
    dropZone.classList.remove('drag-over');
    if (e.dataTransfer && e.dataTransfer.files.length) addFiles(e.dataTransfer.files);
  });

  // Paste a screenshot or copied image with Ctrl/Cmd + V.
  document.addEventListener('paste', (e) => {
    const items = Array.from((e.clipboardData && e.clipboardData.items) || []);
    const files = items.filter((i) => i.kind === 'file').map((i) => i.getAsFile()).filter(Boolean);
    if (files.length) { e.preventDefault(); addFiles(files); }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && errorModal.style.display === 'flex') closeModal();
  });

  // ---------------------------------------------------- slider, zoom, pan
  function setSliderFromClientX(clientX) {
    const rect = canvas.getBoundingClientRect();
    sliderPosition = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
    drawImages();
  }

  function zoomBy(delta) {
    scale = Math.min(Math.max(0.1, scale + delta * 0.1), 8);
    drawImages();
  }

  comparisonSlider.addEventListener('mousedown', (e) => {
    e.preventDefault();
    isDraggingSlider = true;
    document.body.style.cursor = 'ew-resize';
  });
  comparisonSlider.addEventListener('touchstart', (e) => {
    e.preventDefault();
    isDraggingSlider = true;
  }, { passive: false });

  canvas.addEventListener('mousedown', (e) => {
    if (e.button !== 0) return;
    isPanning = true;
    startX = e.clientX - offsetX;
    startY = e.clientY - offsetY;
    canvas.style.cursor = 'grabbing';
  });

  document.addEventListener('mousemove', (e) => {
    if (isDraggingSlider) setSliderFromClientX(e.clientX);
    else if (isPanning) { offsetX = e.clientX - startX; offsetY = e.clientY - startY; drawImages(); }
  });
  document.addEventListener('mouseup', () => {
    isDraggingSlider = false;
    isPanning = false;
    document.body.style.cursor = '';
    canvas.style.cursor = 'grab';
  });

  let pinchStart = 0, pinchScale = 1;
  canvas.addEventListener('touchstart', (e) => {
    if (e.touches.length === 2) {
      e.preventDefault();
      pinchStart = Math.hypot(e.touches[1].clientX - e.touches[0].clientX, e.touches[1].clientY - e.touches[0].clientY);
      pinchScale = scale;
    } else if (e.touches.length === 1) {
      isPanning = true;
      startX = e.touches[0].clientX - offsetX;
      startY = e.touches[0].clientY - offsetY;
    }
  }, { passive: false });

  document.addEventListener('touchmove', (e) => {
    if (isDraggingSlider) {
      e.preventDefault();
      setSliderFromClientX(e.touches[0].clientX);
    } else if (e.target === canvas && e.touches.length === 2 && pinchStart > 0) {
      e.preventDefault();
      const d = Math.hypot(e.touches[1].clientX - e.touches[0].clientX, e.touches[1].clientY - e.touches[0].clientY);
      scale = Math.min(Math.max(0.1, pinchScale * d / pinchStart), 8);
      drawImages();
    } else if (e.target === canvas && isPanning && e.touches.length === 1) {
      e.preventDefault();
      offsetX = e.touches[0].clientX - startX;
      offsetY = e.touches[0].clientY - startY;
      drawImages();
    }
  }, { passive: false });

  document.addEventListener('touchend', () => {
    isDraggingSlider = false;
    isPanning = false;
    pinchStart = 0;
  });

  canvas.addEventListener('wheel', (e) => {
    e.preventDefault();
    zoomBy(e.deltaY < 0 ? 1 : -1);
  }, { passive: false });

  $('zoomIn').addEventListener('click', () => zoomBy(1));
  $('zoomOut').addEventListener('click', () => zoomBy(-1));
  $('resetZoom').addEventListener('click', () => { scale = 1; offsetX = 0; offsetY = 0; drawImages(); });

  // Keyboard users can move the comparison line with the arrow keys.
  comparisonSlider.setAttribute('tabindex', '0');
  comparisonSlider.setAttribute('role', 'slider');
  comparisonSlider.setAttribute('aria-label', 'Compare original and compressed image');
  comparisonSlider.addEventListener('keydown', (e) => {
    if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') return;
    e.preventDefault();
    sliderPosition = Math.max(0, Math.min(1, sliderPosition + (e.key === 'ArrowLeft' ? -0.05 : 0.05)));
    drawImages();
  });

  // ------------------------------------------------------------------ init
  window.addEventListener('resize', () => { resizeCanvas(); updateFloatingButton(); });
  canvas.style.cursor = 'grab';
  setDownloadEnabled(false);
  resizeCanvas();
  showUploadPage();
})();
