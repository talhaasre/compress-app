<?php
/**
 * Writes sitemap.xml with every tool page in every language, each with its
 * hreflang alternates. Run it after adding a page or a language:
 *
 *   php inc/build-sitemap.php > sitemap.xml
 */
$config = require __DIR__ . '/config.php';
require __DIR__ . '/i18n.php';
$englishPages = require __DIR__ . '/pages.php';
$infoPages = require __DIR__ . '/info-pages.php';

$base = rtrim($config['site_url'], '/');
$today = date('Y-m-d');

// slug => [lang => url]
$urls = [];
foreach (site_languages() as $code => $hreflang) {
    foreach (language_tool_pages($code, $englishPages) as $slug => $page) {
        $urls[$slug][$hreflang] = $base . page_path($code, $slug);
    }
}

$x = function ($s) { return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8'); };

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
foreach ($urls as $slug => $langs) {
    $priority = $slug === '' ? '1.0' : '0.8';
    foreach ($langs as $hreflang => $loc) {
        echo "  <url>\n    <loc>{$x($loc)}</loc>\n";
        foreach ($langs as $alt => $href) {
            echo "    <xhtml:link rel=\"alternate\" hreflang=\"{$alt}\" href=\"{$x($href)}\"/>\n";
        }
        echo "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$x($langs['en'])}\"/>\n";
        echo "    <lastmod>{$today}</lastmod>\n    <priority>" . ($hreflang === 'en' ? $priority : ($slug === '' ? '0.9' : '0.7')) . "</priority>\n  </url>\n";
    }
}
foreach (array_keys($infoPages) as $slug) {
    echo "  <url>\n    <loc>{$x($base . '/' . $slug)}</loc>\n    <lastmod>{$today}</lastmod>\n    <priority>0.3</priority>\n  </url>\n";
}
echo "</urlset>\n";
