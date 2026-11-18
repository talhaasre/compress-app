# COMPRESS — free, private image compressor

**Live:** https://compress-app.com · Made by [Asresoft](https://asresoft.com/tools.php), Mumbai

COMPRESS shrinks JPG, PNG, WebP and BMP images right in your browser. Drop in up
to 10 images, drag the slider to compare the original and the compressed version
side by side, then download one image or all of them as a ZIP.

Your images never leave your device: there is no upload and no server-side
processing.

## Features

- **Bulk compression**: up to 10 images at a time, 10 MB each
- **Before/after slider** with zoom and pan, so you can check quality before you download
- **Real PNG compression**: PNGs are reduced to a smaller colour palette (like
  TinyPNG) and keep their transparency and dimensions; 100% quality is lossless
- **Convert formats**: JPEG, PNG and WebP output (BMP is converted to PNG)
- **Optional resize**: cap the longest side at 3840, 2560, 1920, 1280 or 800 px
- **Per-image settings**, or apply one format and quality to every image
- **Never bigger**: if re-encoding would make a file larger, the original is kept
- Drag and drop, file picker, or paste a screenshot with Ctrl/Cmd + V
- Works on phones, tablets and desktops

## How it works

Everything runs in the browser:

- JPEG and WebP use the browser's own encoder (`canvas.toBlob`).
- PNG uses [UPNG.js](https://github.com/photopea/UPNG.js) in a Web Worker to
  quantise colours without freezing the page.
- ZIP files are built with [JSZip](https://stuk.github.io/jszip/).

The libraries are bundled in `vendor/`, so the app has no build step and does
not depend on a CDN for compression.

## Run it locally

```bash
php -S localhost:8000
# then open http://localhost:8000/default.php
```

PHP is only used to print the current year in the footer.

## Project structure

```
default.php          the page (HTML + meta tags)
scripts/app.js       all app logic
scripts/png-worker.js  PNG palette encoder (runs in a Web Worker)
styles/index.css     styles
vendor/              UPNG.js, pako, JSZip (MIT), see vendor/LICENSES.md
```

## Contributing

Issues and pull requests are welcome. Please test changes on a phone-sized
screen as well as desktop.

## License

[MIT](LICENSE) © Asresoft
