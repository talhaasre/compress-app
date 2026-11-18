<?php
/**
 * Languages. English lives at /, every other language under /<code>/
 * (e.g. /es/compress-png). Each inc/lang/<code>.php file translates the
 * interface ('ui') and the tool pages; anything it leaves out falls back to
 * English.
 */

/** code => hreflang. Order is the order of the language links in the footer. */
function site_languages()
{
    return [
        'en' => 'en', 'es' => 'es', 'pt' => 'pt', 'fr' => 'fr', 'de' => 'de', 'it' => 'it',
        'ru' => 'ru', 'tr' => 'tr', 'ar' => 'ar', 'ur' => 'ur', 'hi' => 'hi', 'id' => 'id', 'ja' => 'ja',
    ];
}

function load_language($code)
{
    static $cache = [];
    if (!isset($cache[$code])) {
        $file = __DIR__ . '/lang/' . $code . '.php';
        $cache[$code] = is_file($file) ? require $file : [];
    }
    return $cache[$code];
}

/** Replace {placeholders} in a string or in every string of an array. */
function fill($value, array $vars)
{
    if (is_array($value)) {
        return array_map(function ($v) use ($vars) { return fill($v, $vars); }, $value);
    }
    foreach ($vars as $k => $v) {
        $value = str_replace('{' . $k . '}', (string) $v, (string) $value);
    }
    return $value;
}

/** The interface text for a language, with English as the fallback. */
function language_ui($code)
{
    $en = load_language('en');
    $lang = $code === 'en' ? [] : load_language($code);
    return array_merge($en['ui'], $lang['ui'] ?? []);
}

/**
 * Tool pages in a language. English pages come from inc/pages.php with all
 * their sections. Other languages get translated titles, help text, steps
 * and FAQ; the long English-only sections are left out.
 */
function language_tool_pages($code, array $englishPages)
{
    if ($code === 'en') {
        return $englishPages;
    }
    $lang = load_language($code);
    $pages = [];
    foreach ($englishPages as $slug => $en) {
        $isTarget = isset($en['preset']['target']) && strpos($slug, 'compress-image-to-') === 0;
        if ($isTarget) {
            $kb = $en['preset']['target'];
            $tr = fill($lang['target'] ?? [], ['kb' => $kb]);
            $faqs = array_merge(fill($lang['target_faqs'] ?? [], ['kb' => $kb]), $lang['faqs'] ?? []);
        } else {
            $tr = $lang['pages'][$slug] ?? [];
            $faqs = $lang['faqs'] ?? [];
        }
        if (empty($tr['title'])) {
            continue; // not translated: no page in this language
        }
        $pages[$slug] = [
            'slug' => $slug,
            'nav' => $tr['nav'] ?? $tr['h1'],
            'title' => $tr['title'],
            'description' => $tr['description'],
            'h1' => $tr['h1'],
            'tagline' => $tr['tagline'],
            'howto' => $lang['ui']['steps_title'] ?? null,
            'intro' => $tr['intro'],
            'steps' => $tr['steps'],
            'faqs' => array_map(function ($f) { return ['q' => $f[0], 'a' => htmlspecialchars($f[1], ENT_QUOTES, 'UTF-8')]; }, $faqs),
            'preset' => $en['preset'],
        ];
    }
    return $pages;
}

/** URL path for a page in a language: '/', '/compress-png', '/es/', '/es/compress-png'. */
function page_path($code, $slug)
{
    $prefix = $code === 'en' ? '' : '/' . $code;
    if ($slug === '') {
        return $prefix . '/';
    }
    return $prefix . '/' . $slug;
}
