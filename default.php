<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-LFBJ872LJ4"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'G-LFBJ872LJ4');
  </script>
  <!-- Google Tag Manager -->
  <script>(function (w, d, s, l, i) {
      w[l] = w[l] || []; w[l].push({
        'gtm.start':
          new Date().getTime(), event: 'gtm.js'
      }); var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
          'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-TCWTHLTF');</script>
  <!-- End Google Tag Manager -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#4361ee" />
  <title>Free Online Image Compressor | COMPRESS – Compress & Compare Images</title>
  <meta name="description"
    content="COMPRESS is a fast, free online image compressor that reduces image size in bulk with real-time quality comparison. PNG, JPG, WebP supported.">
  <link rel="canonical" href="https://compress-app.com/" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="192x192" href="android-chrome-192x192.png">
  <link rel="icon" type="image/png" sizes="512x512" href="android-chrome-512x512.png">
  <link rel="manifest" href="site.webmanifest">

  <!-- Facebook Meta Tags -->
  <meta property="og:url" content="https://compress-app.com">
  <meta property="og:type" content="website">
  <meta property="og:title" content="Free Online Image Compressor | COMPRESS – Compress & Compare Images">
  <meta property="og:description"
    content="COMPRESS is a fast, free online image compressor that reduces image size in bulk with real-time quality comparison. PNG, JPG, WebP supported.">
  <meta property="og:image" content="https://compress-app.com/android-chrome-512x512.png">
  <meta property="og:image:width" content="512">
  <meta property="og:image:height" content="512">

  <!-- Twitter Meta Tags -->
  <meta name="twitter:card" content="summary">
  <meta property="twitter:domain" content="compress-app.com">
  <meta property="twitter:url" content="https://compress-app.com">
  <meta name="twitter:title" content="Free Online Image Compressor | COMPRESS – Compress & Compare Images">
  <meta name="twitter:description"
    content="COMPRESS is a fast, free online image compressor that reduces image size in bulk with real-time quality comparison. PNG, JPG, WebP supported.">
  <meta name="twitter:image" content="https://compress-app.com/android-chrome-512x512.png">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="styles/index.css?v=2" />
</head>

<body>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TCWTHLTF" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <!-- Error Popup Modal -->
  <div id="errorModal" class="error-modal">
    <div class="error-modal-content">
      <div class="error-modal-header">
        <h3><i class="fas fa-exclamation-triangle"></i> Attention</h3>
        <button type="button" class="error-modal-close" aria-label="Close">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="error-modal-body">
        <div id="errorMessage"></div>
      </div>
      <div class="error-modal-footer" id="errorModalFooter"></div>
    </div>
  </div>

  <div class="app-container">
    <div class="app-title">
      <div class="app-title-text"><i class="fas fa-images"></i> COMPRESS</div>
      <!-- <img width="80" height="80" src="android-chrome-512x512.png" alt="compress logo" style="border-radius: 5px;"> -->
      <h1 id="tagline" style="font-size: 1rem; font-weight: 400;">
        compress images in bulk and compare quality
      </h1>
      <h2 id="tagline2" style="font-size: 1rem; font-weight: 400;">
        Best png to jpg converter to reduce image size
      </h2>
      <p id="dragNotice" style="display: none; font-size: 1rem; font-weight: 400;">
        compare image compression
      </p>
    </div>

    <div class="zoom-controls" id="zoomControls" style="display: none">
      <button type="button" class="zoom-btn" id="zoomIn" aria-label="Zoom in" title="Zoom in">
        <i class="fas fa-search-plus"></i>
      </button>
      <button type="button" class="zoom-btn" id="zoomOut" aria-label="Zoom out" title="Zoom out">
        <i class="fas fa-search-minus"></i>
      </button>
      <button type="button" class="zoom-btn" id="resetZoom" aria-label="Reset zoom" title="Fit to screen">
        <i class="fas fa-expand"></i>
      </button>
    </div>

    <div class="canvas-container">
      <canvas id="comparisonCanvas" style="display: none;"></canvas>
      <div class="comparison-slider" id="comparisonSlider" style="display: none"></div>

      <div class="upload-section" id="dropZone">
        <h2><i class="fas fa-cloud-upload-alt"></i> Upload Image</h2>
        <div class="upload-icon"><i class="fas fa-images"></i></div>
        <p>Drag & drop your image here or</p>
        <input type="file" id="fileInput" class="file-input"
          accept="image/jpeg,image/jpg,image/png,image/webp,image/bmp" multiple />
        <button class="browse-btn" id="browseBtn">
          <i class="fas fa-folder-open"></i> Browse Files
        </button>
        <p>Supports JPG, PNG, WebP, BMP (Static images only)</p>
        <p class="upload-hint">Up to 10 images, 10 MB each. You can also paste an image with Ctrl/Cmd + V.</p>
      </div>
    </div>

    <!-- Multiple Image Gallery Section -->
    <div class="image-gallery" id="imageGallery" style="display: none;">
      <div class="gallery-header">
        <h3><i class="fas fa-images"></i> <span id="galleryTitle">Uploaded Images</span></h3>
        <div class="gallery-actions">
          <button type="button" class="add-more-btn" id="addMoreBtn" title="Add more images">
            <i class="fas fa-plus"></i> Add
          </button>
          <button type="button" class="clear-all-btn" id="clearAllBtn">
            <i class="fas fa-trash"></i> Clear All
          </button>
        </div>
      </div>
      <div class="gallery-scroll-container">
        <div class="gallery-grid" id="galleryGrid">
          <!-- Images will be added here dynamically -->
        </div>
      </div>
    </div>

    <div class="control-panel hidden" id="controlPanel">
      <div class="panel-section">
        <div class="setting-group">
          <label><i class="fas fa-image"></i> Format</label>
          <div class="format-controls">
            <select id="formatSelect" aria-label="Output format"></select>
            <button type="button" class="btn btn-secondary" id="applyToAllBtn" style="display: none; background: #4cc9f0;"
              title="Use this format and quality for all images">
              Apply All
            </button>
          </div>
        </div>

        <div class="setting-group resize-group">
          <label for="resizeSelect"><i class="fas fa-expand-arrows-alt"></i> Max size</label>
          <select id="resizeSelect">
            <option value="0">Keep original dimensions</option>
            <option value="3840">Max 3840 px (4K)</option>
            <option value="2560">Max 2560 px</option>
            <option value="1920">Max 1920 px (Full HD)</option>
            <option value="1280">Max 1280 px (web)</option>
            <option value="800">Max 800 px (thumbnail)</option>
          </select>
        </div>

        <div class="setting-group quality-group">
          <label for="qualitySlider"><i class="fas fa-bullseye"></i> <span id="qualityLabel">Quality</span>:
            <span id="qualityValue">60</span>%</label>
          <div class="slider-container">
            <input type="range" id="qualitySlider" min="40" max="100" value="60" />
            <div class="value-display" id="qualityDisplay">60%</div>
          </div>
        </div>
      </div>

      <div class="panel-section">
        <div class="stats">
          <div class="stat-item stat-item1">
            <div class="stat-label">
              <i class="fas fa-image"></i> Original
            </div>
            <div class="stat-value-large" id="originalSize">0 KB</div>
            <div class="stat-sub" id="originalMeta"></div>
          </div>
          <div class="stat-item stat-item2">
            <div class="stat-label">
              <i class="fas fa-compress-alt"></i> Optimized
            </div>
            <div class="stat-value-large" id="optimizedSize">0 KB</div>
            <div class="stat-sub" id="optimizedMeta"></div>
          </div>
        </div>
      </div>

      <div class="panel-section">
        <div class="actions">
          <button class="btn download-btn" id="downloadBtn" disabled aria-label="Download compressed image">
            <i class="fas fa-download"></i> <span id="downloadBtnText">Download</span>
          </button>
        </div>
      </div>
    </div>

    <div class="info-box">
      <i class="fas fa-info-circle"></i> All processing happens locally in
      your browser. Your images are never uploaded. <br /><br /> &copy;
      <?php echo date("Y"); ?> COMPRESS by
      <a href="https://asresoft.com/tools.php" target="_blank" rel="noopener" style="color: inherit;">Asresoft</a>
      &middot; <a href="https://github.com/talhaasre/compress-app" target="_blank" rel="noopener" style="color: inherit;">Open source (MIT)</a>
    </div>
  </div>

  <!-- Floating Download Button -->
  <div class="floating-download-btn" id="floatingDownloadBtn" style="display: none;">
    <button type="button" class="floating-btn" title="Download optimized image" aria-label="Download optimized image" disabled>
      <i class="fas fa-download"></i>
    </button>
  </div>

  <script src="vendor/jszip.min.js" defer></script>
  <script src="vendor/pako.min.js" defer></script>
  <script src="vendor/UPNG.js" defer></script>
  <script src="scripts/app.js?v=2" defer></script>

  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebApplication",
      "name": "COMPRESS – Free Online Image Compressor",
      "url": "https://compress-app.com/",
      "description": "Compress JPG, PNG, WebP and BMP images in bulk, right in your browser. Compare quality side by side and download one image or a ZIP. Nothing is uploaded.",
      "operatingSystem": "All",
      "applicationCategory": "MultimediaApplication",
      "browserRequirements": "Requires JavaScript and a modern browser",
      "isAccessibleForFree": true,
      "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" },
      "creator": { "@type": "Organization", "name": "Asresoft", "url": "https://asresoft.com" },
      "license": "https://opensource.org/licenses/MIT",
      "codeRepository": "https://github.com/talhaasre/compress-app"
    }
  </script>
</body>

</html>