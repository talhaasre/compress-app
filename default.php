<?php
/**
 * COMPRESS — every page is served from here.
 * Pretty URLs (/compress-png, /privacy …) are routed by .htaccess on the
 * server and by dev-router.php when running `php -S` locally.
 */
$config = require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/i18n.php';
$englishPages = require __DIR__ . '/inc/pages.php';
$infoPages = require __DIR__ . '/inc/info-pages.php';

function e($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

// Work out the language and page from the URL: /, /compress-png, /es/, /es/compress-png
$path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$parts = array_values(array_filter(explode('/', trim(preg_replace('#\.php$#', '', $path), '/')), 'strlen'));
$lang = 'en';
if ($parts && $parts[0] !== 'en' && isset(site_languages()[$parts[0]])) {
    $lang = array_shift($parts);
}
$slug = implode('/', $parts);
if ($slug === 'default' || $slug === 'index') {
    $slug = '';
}

$ui = language_ui($lang);
$langInfo = load_language($lang);
$dir = $langInfo['dir'] ?? 'ltr';
$toolPages = language_tool_pages($lang, $englishPages);

$isTool = isset($toolPages[$slug]);
$isInfo = !$isTool && $lang === 'en' && isset($infoPages[$slug]);
$notFound = !$isTool && !$isInfo;
if ($notFound) {
    http_response_code(404);
    $slug = '';
    $isTool = true;
}
$page = $isTool ? $toolPages[$slug] : $infoPages[$slug];

$base = rtrim($config['site_url'], '/');
$canonical = $base . page_path($lang, $slug);
$ogImage = $base . '/og-image.jpg';
$adClient = trim($config['adsense_client']);
$year = date('Y');

// The same page in every language that has it, for hreflang and the language links.
$alternates = [];
if ($isTool && !$notFound) {
    foreach (site_languages() as $code => $hreflang) {
        if (isset(language_tool_pages($code, $englishPages)[$slug])) {
            $alternates[$code] = $base . page_path($code, $slug);
        }
    }
}

/** An in-content AdSense unit. Prints nothing until a client and slot ID are configured. */
function ad_slot($name)
{
    global $config, $adClient;
    $slot = $config['adsense_slots'][$name] ?? '';
    if ($adClient === '' || $slot === '') {
        return '';
    }
    global $ui;
    return '<aside class="ad-slot" aria-label="' . e($ui['ad']) . '"><span class="ad-label">' . e($ui['ad']) . '</span>'
        . '<ins class="adsbygoogle" style="display:block" data-ad-client="' . e($adClient) . '" data-ad-slot="' . e($slot) . '" data-ad-format="auto" data-full-width-responsive="true"></ins>'
        . '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script></aside>';
}

// Structured data
$schema = [];
if ($isTool) {
    $schema[] = [
        '@context' => 'https://schema.org',
        '@type' => 'WebApplication',
        'name' => $slug === '' && $lang === 'en' ? 'COMPRESS – Free Online Image Compressor' : $page['h1'] . ' – COMPRESS',
        'inLanguage' => $lang,
        'url' => $canonical,
        'description' => $page['description'],
        'operatingSystem' => 'All',
        'applicationCategory' => 'MultimediaApplication',
        'browserRequirements' => 'Requires JavaScript and a modern browser',
        'isAccessibleForFree' => true,
        'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
        'creator' => ['@type' => 'Organization', 'name' => 'Asresoft', 'url' => 'https://asresoft.com'],
    ];
    if (!empty($page['faqs'])) {
        $schema[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function ($f) {
                return ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f['a'])]];
            }, $page['faqs']),
        ];
    }
}
if ($slug !== '') {
    $schema[] = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'COMPRESS', 'item' => $base . page_path($lang, '')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $page['h1'], 'item' => $canonical],
        ],
    ];
}
?>
<!DOCTYPE html>
<html lang="<?php echo e($lang); ?>" dir="<?php echo e($dir); ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title><?php echo e($page['title']); ?></title>
  <meta name="description" content="<?php echo e($page['description']); ?>">
  <?php if ($notFound): ?>
    <meta name="robots" content="noindex">
  <?php else: ?>
    <link rel="canonical" href="<?php echo e($canonical); ?>" />
    <?php foreach ($alternates as $code => $url): ?>
      <link rel="alternate" hreflang="<?php echo e(site_languages()[$code]); ?>" href="<?php echo e($url); ?>" />
    <?php endforeach; ?>
    <?php if (isset($alternates['en'])): ?>
      <link rel="alternate" hreflang="x-default" href="<?php echo e($alternates['en']); ?>" />
    <?php endif; ?>
  <?php endif; ?>
  <meta name="theme-color" content="#4361ee" />

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($config['ga_id']); ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', '<?php echo e($config['ga_id']); ?>');
  </script>
  <!-- Google Tag Manager -->
  <script>(function (w, d, s, l, i) {
      w[l] = w[l] || []; w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
      var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true; j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', '<?php echo e($config['gtm_id']); ?>');</script>
  <?php if ($adClient !== ''): ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo e($adClient); ?>" crossorigin="anonymous"></script>
  <?php endif; ?>

  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">

  <meta property="og:site_name" content="COMPRESS">
  <meta property="og:url" content="<?php echo e($canonical); ?>">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="<?php echo e($lang); ?>">
  <meta property="og:title" content="<?php echo e($page['title']); ?>">
  <meta property="og:description" content="<?php echo e($page['description']); ?>">
  <meta property="og:image" content="<?php echo e($ogImage); ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="COMPRESS – free online image compressor">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo e($page['title']); ?>">
  <meta name="twitter:description" content="<?php echo e($page['description']); ?>">
  <meta name="twitter:image" content="<?php echo e($ogImage); ?>">

  <!-- Icons load without blocking the first paint -->
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
  <link rel="stylesheet" href="/styles/index.css?v=3" />
  <?php foreach ($schema as $s): ?>
    <script type="application/ld+json"><?php echo json_encode($s, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?></script>
  <?php endforeach; ?>
</head>

<body class="<?php echo $isTool ? 'is-tool' : 'is-info'; ?>">
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo e($config['gtm_id']); ?>" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>

<?php if ($isTool): ?>
  <div id="errorModal" class="error-modal" role="dialog" aria-modal="true" aria-labelledby="errorModalTitle">
    <div class="error-modal-content">
      <div class="error-modal-header">
        <h3 id="errorModalTitle"><i class="fas fa-exclamation-triangle"></i> <?php echo e($ui['attention']); ?></h3>
        <button type="button" class="error-modal-close" aria-label="<?php echo e($ui['close']); ?>">
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
      <a class="app-title-text" href="<?php echo e(page_path($lang, '')); ?>"><i class="fas fa-images"></i> COMPRESS</a>
      <h1 id="tagline" class="app-h1"><?php echo e($notFound ? $ui['not_found'] : $page['h1']); ?></h1>
      <p id="tagline2" class="app-sub"><?php echo e($page['tagline']); ?></p>
      <p id="dragNotice" class="app-sub" style="display: none;"><?php echo e($ui['drag_compare']); ?></p>
    </div>

    <div class="zoom-controls" id="zoomControls" style="display: none">
      <button type="button" class="zoom-btn" id="zoomIn" aria-label="<?php echo e($ui['zoom_in']); ?>" title="<?php echo e($ui['zoom_in']); ?>">
        <i class="fas fa-search-plus"></i>
      </button>
      <button type="button" class="zoom-btn" id="zoomOut" aria-label="<?php echo e($ui['zoom_out']); ?>" title="<?php echo e($ui['zoom_out']); ?>">
        <i class="fas fa-search-minus"></i>
      </button>
      <button type="button" class="zoom-btn" id="resetZoom" aria-label="<?php echo e($ui['fit']); ?>" title="<?php echo e($ui['fit']); ?>">
        <i class="fas fa-expand"></i>
      </button>
    </div>

    <div class="canvas-container">
      <canvas id="comparisonCanvas" style="display: none;"></canvas>
      <div class="comparison-slider" id="comparisonSlider" style="display: none"></div>

      <div class="upload-section" id="dropZone">
        <h2><i class="fas fa-cloud-upload-alt"></i> <?php echo e($ui['upload_title']); ?></h2>
        <div class="upload-icon"><i class="fas fa-images"></i></div>
        <p><?php echo e($ui['drag_drop']); ?></p>
        <input type="file" id="fileInput" class="file-input"
          accept="image/jpeg,image/jpg,image/png,image/webp,image/bmp" multiple />
        <button type="button" class="browse-btn" id="browseBtn">
          <i class="fas fa-folder-open"></i> <?php echo e($ui['browse']); ?>
        </button>
        <p><?php echo e($ui['supports']); ?></p>
        <p class="upload-hint"><?php echo e($ui['hint']); ?></p>
      </div>
    </div>

    <div class="image-gallery" id="imageGallery" style="display: none;">
      <div class="gallery-header">
        <h3><i class="fas fa-images"></i> <span id="galleryTitle"><?php echo e($ui['uploaded']); ?></span></h3>
        <div class="gallery-actions">
          <button type="button" class="add-more-btn" id="addMoreBtn" title="<?php echo e($ui['add_title']); ?>">
            <i class="fas fa-plus"></i> <?php echo e($ui['add']); ?>
          </button>
          <button type="button" class="clear-all-btn" id="clearAllBtn">
            <i class="fas fa-trash"></i> <?php echo e($ui['clear_all']); ?>
          </button>
        </div>
      </div>
      <div class="gallery-scroll-container">
        <div class="gallery-grid" id="galleryGrid"></div>
      </div>
    </div>

    <div class="control-panel hidden" id="controlPanel">
      <div class="panel-section">
        <div class="setting-group">
          <label for="formatSelect"><i class="fas fa-image"></i> <?php echo e($ui['format']); ?></label>
          <div class="format-controls">
            <select id="formatSelect"></select>
            <button type="button" class="btn btn-secondary" id="applyToAllBtn" style="display: none; background: #4cc9f0;"
              title="<?php echo e($ui['apply_all_title']); ?>">
              <?php echo e($ui['apply_all']); ?>
            </button>
          </div>
        </div>

        <div class="setting-group size-group">
          <div>
            <label for="resizeSelect"><i class="fas fa-expand-arrows-alt"></i> <?php echo e($ui['max_size']); ?></label>
            <select id="resizeSelect">
              <option value="0"><?php echo e($ui['size_original']); ?></option>
              <option value="3840">3840 px (4K)</option>
              <option value="2560">2560 px</option>
              <option value="1920">1920 px (Full HD)</option>
              <option value="1280">1280 px (web)</option>
              <option value="800">800 px</option>
            </select>
          </div>
          <div>
            <label for="targetSelect"><i class="fas fa-weight-hanging"></i> <?php echo e($ui['target_size']); ?></label>
            <select id="targetSelect">
              <option value="0"><?php echo e($ui['off']); ?></option>
              <option value="20"><?php echo e(fill($ui['under'], ['size' => '20 KB'])); ?></option>
              <option value="50"><?php echo e(fill($ui['under'], ['size' => '50 KB'])); ?></option>
              <option value="100"><?php echo e(fill($ui['under'], ['size' => '100 KB'])); ?></option>
              <option value="200"><?php echo e(fill($ui['under'], ['size' => '200 KB'])); ?></option>
              <option value="500"><?php echo e(fill($ui['under'], ['size' => '500 KB'])); ?></option>
              <option value="1024"><?php echo e(fill($ui['under'], ['size' => '1 MB'])); ?></option>
            </select>
          </div>
        </div>

        <div class="setting-group quality-group">
          <label for="qualitySlider"><i class="fas fa-bullseye"></i> <span id="qualityLabel"><?php echo e($ui['quality']); ?></span>:
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
            <div class="stat-label"><i class="fas fa-image"></i> <?php echo e($ui['original']); ?></div>
            <div class="stat-value-large" id="originalSize">0 KB</div>
            <div class="stat-sub" id="originalMeta"></div>
          </div>
          <div class="stat-item stat-item2">
            <div class="stat-label"><i class="fas fa-compress-alt"></i> <?php echo e($ui['optimized']); ?></div>
            <div class="stat-value-large" id="optimizedSize">0 KB</div>
            <div class="stat-sub" id="optimizedMeta"></div>
          </div>
        </div>
      </div>

      <div class="panel-section">
        <div class="actions">
          <button type="button" class="btn download-btn" id="downloadBtn" disabled aria-label="<?php echo e($ui['download']); ?>">
            <i class="fas fa-download"></i> <span id="downloadBtnText"><?php echo e($ui['download']); ?></span>
          </button>
        </div>
      </div>
    </div>

    <div class="info-box">
      <i class="fas fa-lock"></i> <?php echo e($ui['private_note']); ?>
      <a href="#learn" class="learn-link"><?php echo e($ui['how_it_works']); ?> &darr;</a>
    </div>
  </div>

  <div class="floating-download-btn" id="floatingDownloadBtn" style="display: none;">
    <button type="button" class="floating-btn" title="<?php echo e($ui['download']); ?>" aria-label="<?php echo e($ui['download']); ?>" disabled>
      <i class="fas fa-download"></i>
    </button>
  </div>

  <main class="page-content" id="learn">
    <?php if (!empty($page['intro'])): ?>
      <section class="content-card">
        <h2><?php echo e($slug === '' && $lang === 'en' ? 'Compress images online, privately' : $page['h1']); ?></h2>
        <p><?php echo e($page['intro']); ?></p>
        <h3><?php echo e($page['howto'] ?? ('How to ' . lcfirst($page['h1']))); ?></h3>
        <ol class="steps">
          <?php foreach ($page['steps'] as $step): ?>
            <li><?php echo e($step); ?></li>
          <?php endforeach; ?>
        </ol>
        <p><a href="#" class="btn-start" data-scroll-top><?php echo e($ui['start']); ?> &uarr;</a></p>
      </section>
    <?php endif; ?>

    <?php echo ad_slot('content_top'); ?>

    <?php foreach ($page['sections'] ?? [] as $section): ?>
      <section class="content-card">
        <h2><?php echo e($section['h']); ?></h2>
        <?php foreach ($section['p'] as $para): ?>
          <p><?php echo $para; /* trusted copy from inc/pages.php, may contain links */ ?></p>
        <?php endforeach; ?>
      </section>
    <?php endforeach; ?>

    <?php echo ad_slot('content_bottom'); ?>

    <?php if (!empty($page['faqs'])): ?>
      <section class="content-card faq">
        <h2><?php echo e($ui['faq_title']); ?></h2>
        <?php foreach ($page['faqs'] as $faq): ?>
          <details>
            <summary><?php echo e($faq['q']); ?></summary>
            <p><?php echo $faq['a']; /* trusted copy */ ?></p>
          </details>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>

    <nav class="content-card related" aria-label="<?php echo e($ui['more_tools']); ?>">
      <h2><?php echo e($ui['more_tools']); ?></h2>
      <ul>
        <?php foreach ($toolPages as $key => $tp): if ($key === $slug) continue; ?>
          <li><a href="<?php echo e(page_path($lang, $key)); ?>"><?php echo e($tp['nav']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </main>
<?php else: ?>
  <header class="info-header">
    <a class="app-title-text" href="/"><i class="fas fa-images"></i> COMPRESS</a>
    <a class="btn-start" href="/">Compress images</a>
  </header>
  <main class="page-content info-page">
    <article class="content-card">
      <h1><?php echo e($page['h1']); ?></h1>
      <?php echo $page['html']; ?>
    </article>
  </main>
<?php endif; ?>

  <footer class="site-footer">
    <nav aria-label="Footer">
      <a href="<?php echo e(page_path($lang, '')); ?>"><?php echo e($ui['footer_home']); ?></a>
      <a href="/about"><?php echo e($ui['about']); ?></a>
      <a href="/privacy"><?php echo e($ui['privacy']); ?></a>
      <a href="/terms"><?php echo e($ui['terms']); ?></a>
      <a href="<?php echo e($config['repo_url']); ?>" target="_blank" rel="noopener"><?php echo e($ui['open_source']); ?></a>
    </nav>
    <nav class="lang-links" aria-label="<?php echo e($ui['language']); ?>">
      <i class="fas fa-globe" aria-hidden="true"></i>
      <?php foreach (site_languages() as $code => $hreflang):
          $href = $alternates[$code] ?? ($base . page_path($code, ''));
          $href = substr($href, strlen($base)); ?>
        <a href="<?php echo e($href); ?>" hreflang="<?php echo e($hreflang); ?>" lang="<?php echo e($hreflang); ?>"<?php echo $code === $lang ? ' aria-current="true"' : ''; ?>><?php echo e(load_language($code)['name'] ?? $code); ?></a>
      <?php endforeach; ?>
    </nav>
    <p><?php echo fill(e($ui['made_by']), ['asresoft' => '<a href="https://asresoft.com" target="_blank" rel="noopener">Asresoft</a>']); ?> &copy; <?php echo $year; ?></p>
  </footer>

<?php if ($isTool): ?>
  <script>
    window.COMPRESS_PRESET = <?php echo json_encode((object) $page['preset'], JSON_HEX_TAG); ?>;
    window.COMPRESS_I18N = <?php echo json_encode($ui, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE); ?>;
  </script>
  <script src="/vendor/jszip.min.js" defer></script>
  <script src="/vendor/pako.min.js" defer></script>
  <script src="/vendor/UPNG.js" defer></script>
  <script src="/scripts/app.js?v=4" defer></script>
<?php endif; ?>
</body>

</html>
