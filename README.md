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
- **Target file size**: get images under 20, 50, 100, 200 or 500 KB or 1 MB for
  forms and upload limits (best quality that fits, then smaller dimensions)
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
php -S localhost:8000 dev-router.php
# then open http://localhost:8000/
```

PHP renders the pages (titles, help text and FAQ for each tool page); the
compressor itself is plain JavaScript.

## Pages and SEO

Each tool page (`/compress-png`, `/png-to-jpg`, `/compress-image-to-100kb` …)
is defined in `inc/pages.php` with its own title, description, help text, FAQ
and preset. Add a page there, then run `php inc/build-sitemap.php > sitemap.xml`.
On the server, `.htaccess` routes the clean URLs to `default.php`.

## Languages

English lives at `/`, and Spanish, Portuguese, French, German, Italian,
Russian, Turkish, Arabic, Urdu, Hindi, Indonesian and Japanese live under
`/es/`, `/pt/` … `/ja/`. Each language is one file in `inc/lang/` that
translates the interface and the tool pages; anything it leaves out falls back
to English. Every page lists its translations with hreflang tags, and Arabic
and Urdu are shown right to left. To add a language, copy `inc/lang/es.php`,
translate it, add its code to `site_languages()` in `inc/i18n.php` and to the
language rule in `.htaccess`, then rebuild the sitemap.

## Ads

Ads are off until an AdSense publisher ID is set in `inc/config.php`. Then the
AdSense script loads (for Auto ads), `/ads.txt` is generated, and any slot IDs
you add show ads between the help sections. Ads are never placed inside the
compressor.

## Project structure

```
default.php          all pages (tool pages and about/privacy/terms)
inc/config.php       analytics and AdSense settings
inc/pages.php        tool pages: titles, help text, FAQ, presets
inc/info-pages.php   about, privacy and terms
inc/i18n.php         languages and URL helpers
inc/lang/*.php       translations (en.php is the source)
inc/build-sitemap.php  writes sitemap.xml
.htaccess            clean URLs, caching (Apache/LiteSpeed)
dev-router.php       clean URLs for `php -S` during development
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
