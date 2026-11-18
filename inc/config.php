<?php
/**
 * Site settings for COMPRESS.
 *
 * Ads stay switched off until a Google AdSense publisher ID is set below.
 * After AdSense approves the site:
 *   1. Set adsense_client to your "ca-pub-XXXXXXXXXXXXXXXX" ID.
 *      That loads the AdSense script, so Auto ads work if you turn them on
 *      in AdSense, and /ads.txt is published automatically.
 *   2. Optional: create "Display" ad units in AdSense and paste their slot IDs
 *      into adsense_slots to show ads at fixed places in the page content.
 * Ads are never placed inside the compressor itself (next to the upload or
 * download buttons), to avoid accidental clicks that AdSense penalises.
 */
return [
    'site_name' => 'COMPRESS',
    'site_url' => 'https://compress-app.com',

    'ga_id' => 'G-LFBJ872LJ4',
    'gtm_id' => 'GTM-TCWTHLTF',

    // e.g. 'ca-pub-1234567890123456'. Empty = no ads and no ads.txt.
    'adsense_client' => '',
    'adsense_slots' => [
        'content_top' => '',    // after the "how to" steps
        'content_bottom' => '', // before the FAQ
    ],

    'contact_email' => 'info@asresoft.com',
    'repo_url' => 'https://github.com/talhaasre/compress-app',
];
