<?php
/**
 * Every page of the site. Tool pages share the compressor and differ in
 * their preset (format / target size / resize), heading and help text, so
 * each one can rank for its own search (e.g. "compress image to 100kb").
 *
 * preset keys: format (image/jpeg|image/png|image/webp), target (KB), resize (px)
 */

$privacyLine = 'Your images are processed on your own device and are never uploaded to a server.';

$targetFaqs = function ($kb) {
    return [
        ['q' => "How do I compress a photo to under {$kb} KB?",
         'a' => "Upload the photo, keep the target set to {$kb} KB and download. COMPRESS tries the highest quality that fits under {$kb} KB, and only makes the image smaller in pixels if lowering the quality alone isn't enough."],
        ['q' => "Will my image look blurry at {$kb} KB?",
         'a' => 'That depends on how big the original is. Drag the slider over the preview to compare the original and the compressed version before downloading. If text looks soft, choose a smaller "Max size" so the picture keeps more detail per pixel.'],
        ['q' => 'Which format should I use for forms and applications?',
         'a' => 'Most online forms ask for JPG/JPEG, so the target-size pages save as JPEG by default. You can switch to PNG or WebP in the Format menu if the form accepts them.'],
        ['q' => 'Is it safe to compress ID photos and documents here?',
         'a' => 'Yes. ' . 'Your images are processed on your own device and are never uploaded to a server.' . ' You can even disconnect from the internet after the page loads.'],
    ];
};

$targetPage = function ($kb, $extra) use ($targetFaqs) {
    return array_merge([
        'slug' => "compress-image-to-{$kb}kb",
        'nav' => "Compress to {$kb} KB",
        'title' => "Compress Image to {$kb}KB Online Free (JPG, PNG) | COMPRESS",
        'description' => "Reduce a photo or document image to under {$kb} KB in seconds. Free, no sign-up and private: images are compressed in your browser, never uploaded.",
        'h1' => "Compress image to {$kb} KB",
        'tagline' => "Get any photo under {$kb} KB for forms and uploads",
        'preset' => ['format' => 'image/jpeg', 'target' => $kb],
        'faqs' => $targetFaqs($kb),
    ], $extra);
};

return [
    '' => [
        'slug' => '',
        'nav' => 'Image compressor',
        'title' => 'Free Online Image Compressor – Compress JPG, PNG & WebP | COMPRESS',
        'description' => 'Compress JPG, PNG, WebP and BMP images in bulk for free. Compare quality side by side, convert formats, resize or hit a target size. Nothing is uploaded.',
        'h1' => 'Free online image compressor',
        'howto' => 'How to compress images',
        'tagline' => 'Compress images in bulk and compare the quality',
        'preset' => [],
        'intro' => 'COMPRESS makes JPG, PNG, WebP and BMP images smaller so they load faster, fit email and upload limits, and take less space. Drop in up to 10 images, check the before-and-after with the slider, and download one image or a ZIP. ' . $privacyLine,
        'steps' => [
            'Drop your images on the page, click Browse Files, or paste a screenshot with Ctrl/Cmd + V.',
            'Drag the slider across the preview to compare. Change the format, quality, max size or target file size if you want.',
            'Click Download to save the image, or a ZIP when you compressed several.',
        ],
        'sections' => [
            ['h' => 'Why compress images?', 'p' => [
                'Large images slow down websites, which hurts both visitors and search rankings. They also bounce off upload limits on forms, job portals and marketplaces, and fill up phones and inboxes.',
                'A photo straight from a phone camera is often 3 to 8 MB. For a website, an email or a form, the same photo usually looks the same at 150 to 400 KB.',
            ]],
            ['h' => 'What makes COMPRESS different', 'p' => [
                '<strong>You see the result before you download it.</strong> The comparison slider shows the original on the left and the compressed image on the right, at any zoom level.',
                '<strong>PNGs keep their transparency.</strong> PNGs are compressed by reducing their colour palette, the same idea as popular PNG optimisers, so logos and screenshots stay sharp and transparent.',
                '<strong>It never makes a file bigger.</strong> If an image is already well optimised, COMPRESS keeps the original instead of giving you a larger file.',
            ]],
        ],
        'faqs' => [
            ['q' => 'Is COMPRESS really free?', 'a' => 'Yes. There is no sign-up, watermark or daily limit. The site is supported by ads.'],
            ['q' => 'Are my images uploaded anywhere?', 'a' => $privacyLine . ' The compression runs in your browser using JavaScript.'],
            ['q' => 'How many images can I compress at once?', 'a' => 'Up to 10 images at a time, up to 10 MB each. When you are done, click Clear All and add the next batch.'],
            ['q' => 'Which formats are supported?', 'a' => 'You can upload JPG/JPEG, PNG, WebP and BMP. You can save as JPEG, PNG or WebP (WebP depends on your browser). BMP files are saved as PNG.'],
            ['q' => 'How much smaller will my images get?', 'a' => 'Typical photos shrink by 50 to 80% with no visible difference. PNG screenshots and graphics often shrink by 40 to 70%. Images that are already compressed may not get any smaller, and then the original is kept.'],
        ],
    ],

    'compress-jpeg' => [
        'slug' => 'compress-jpeg',
        'nav' => 'Compress JPEG',
        'title' => 'Compress JPEG Online – Reduce JPG File Size Free | COMPRESS',
        'description' => 'Reduce JPG and JPEG file size online for free. Set the quality, compare before and after, and download in bulk as a ZIP. Private: nothing is uploaded.',
        'h1' => 'Compress JPEG images',
        'tagline' => 'Make JPG photos smaller without visible quality loss',
        'preset' => [],
        'intro' => 'JPEG is the format of almost every photo, and it compresses very well. Lowering the JPEG quality from a camera\'s 90 to 95% to around 60 to 75% usually cuts the file size by more than half, and most people can\'t see the difference. ' . $privacyLine,
        'steps' => [
            'Add your JPG or JPEG photos (up to 10 at a time).',
            'Move the quality slider. 60% is a good start for photos, and 75 to 85% suits product shots with fine detail.',
            'Compare with the slider, then download the photo or a ZIP of all of them.',
        ],
        'sections' => [
            ['h' => 'Choosing a JPEG quality', 'p' => [
                '<strong>40 to 55%:</strong> the smallest files, good for thumbnails and previews. You may notice softness around edges and text.',
                '<strong>60 to 70%:</strong> the best balance for most website and social media photos.',
                '<strong>75 to 90%:</strong> for photos you will print or zoom into, such as product photography and portfolios.',
            ]],
            ['h' => 'Make big photos web-friendly', 'p' => [
                'Phone cameras save photos at 4000 pixels wide or more, which is far bigger than any screen. Choose "Max 1920 px" or "Max 1280 px" in the Max size menu to resize while you compress. This often saves more than the quality setting does.',
            ]],
        ],
        'faqs' => [
            ['q' => 'Is JPG the same as JPEG?', 'a' => 'Yes. They are the same format, and .jpg is just the shorter file extension.'],
            ['q' => 'Does compressing a JPEG again lose quality?', 'a' => 'Each JPEG save loses a little detail, so always compress from the original photo rather than from an already compressed copy.'],
            ['q' => 'Will the photo\'s dimensions change?', 'a' => 'No. The width and height stay the same unless you pick a Max size or a target file size that needs smaller dimensions.'],
            ['q' => 'Is my photo uploaded?', 'a' => $privacyLine],
        ],
    ],

    'compress-png' => [
        'slug' => 'compress-png',
        'nav' => 'Compress PNG',
        'title' => 'Compress PNG Online – Smaller PNGs, Transparency Kept | COMPRESS',
        'description' => 'Shrink PNG files by up to 70% while keeping transparency and full size. Free online PNG compressor that runs in your browser, with bulk download.',
        'h1' => 'Compress PNG images',
        'tagline' => 'Smaller PNGs that keep their transparency',
        'preset' => ['format' => 'image/png'],
        'intro' => 'PNG is used for logos, icons, screenshots and anything with a transparent background. PNG is lossless, so the way to make it smaller is to use fewer colours. COMPRESS reduces a PNG to a palette of up to 256 well-chosen colours, which usually cuts the size by half or more while the image looks the same. ' . $privacyLine,
        'steps' => [
            'Add your PNG files.',
            'Keep the quality at 85% (256 colours) or lower it for even smaller files. 100% is lossless.',
            'Check edges and gradients with the comparison slider, then download.',
        ],
        'sections' => [
            ['h' => 'When PNG is the right choice', 'p' => [
                'Use PNG for images that need transparency, and for graphics with flat colours and sharp edges such as logos, UI screenshots, charts and illustrations.',
                'For photos, JPEG or WebP is usually 3 to 10 times smaller. If your PNG is really a photo, switch the format to JPEG or WebP.',
            ]],
            ['h' => 'Quality settings for PNG', 'p' => [
                '<strong>100%:</strong> lossless, every colour kept.',
                '<strong>85 to 99%:</strong> up to 256 colours. This is visually identical for most graphics.',
                '<strong>70 to 84%:</strong> 128 colours. <strong>55 to 69%:</strong> 64 colours. <strong>40 to 54%:</strong> 32 colours, for simple icons.',
            ]],
        ],
        'faqs' => [
            ['q' => 'Will my transparent background be kept?', 'a' => 'Yes. Transparency, including soft edges, is kept when you save as PNG or WebP. Only JPEG has no transparency, and then the background becomes white.'],
            ['q' => 'Why did my PNG not get smaller?', 'a' => 'Some PNGs are already optimised or have very few colours. If compressing would make the file bigger, COMPRESS keeps your original.'],
            ['q' => 'Does it resize my PNG?', 'a' => 'No. The width and height stay the same unless you choose a Max size.'],
            ['q' => 'Is my PNG uploaded?', 'a' => $privacyLine],
        ],
    ],

    'png-to-jpg' => [
        'slug' => 'png-to-jpg',
        'nav' => 'PNG to JPG',
        'title' => 'PNG to JPG Converter Online – Free & Private | COMPRESS',
        'description' => 'Convert PNG to JPG online for free and make files up to 10x smaller. Transparent areas become white. Convert in bulk; nothing is uploaded.',
        'h1' => 'Convert PNG to JPG',
        'tagline' => 'Turn PNG photos and screenshots into smaller JPGs',
        'preset' => ['format' => 'image/jpeg'],
        'intro' => 'PNG files from screenshots or design tools are often several megabytes. Converting them to JPG makes them much smaller and accepted almost everywhere. COMPRESS converts up to 10 PNGs at once and puts any transparent background on white. ' . $privacyLine,
        'steps' => [
            'Add your PNG files. The output format is already set to JPEG.',
            'Adjust the quality if you want. 60 to 75% suits most images.',
            'Download one JPG, or a ZIP of all of them.',
        ],
        'sections' => [
            ['h' => 'Should you convert PNG to JPG?', 'p' => [
                'Convert when the image is a photo or has many colours and no transparency, for example camera photos saved as PNG or screenshots of photos. The JPG will often be 5 to 10 times smaller.',
                'Keep PNG for logos, icons and anything that needs a transparent background. You can still make those smaller on the <a href="/compress-png">PNG compressor</a>.',
            ]],
        ],
        'faqs' => [
            ['q' => 'What happens to transparent areas?', 'a' => 'JPG has no transparency, so transparent areas are filled with white.'],
            ['q' => 'Can I convert JPG to PNG instead?', 'a' => 'Yes. Choose PNG in the Format menu. The file will usually be bigger, because PNG is lossless.'],
            ['q' => 'Can I convert many PNGs at once?', 'a' => 'Yes, up to 10 at a time. Click Apply All to use the same settings for every image, then download the ZIP.'],
            ['q' => 'Is it private?', 'a' => $privacyLine],
        ],
    ],

    'convert-to-webp' => [
        'slug' => 'convert-to-webp',
        'nav' => 'Convert to WebP',
        'title' => 'Convert JPG & PNG to WebP Online Free | COMPRESS',
        'description' => 'Convert JPG and PNG images to WebP for faster websites. Free, bulk conversion with quality control and a side-by-side preview. Nothing is uploaded.',
        'h1' => 'Convert images to WebP',
        'tagline' => 'Faster websites with smaller WebP images',
        'preset' => ['format' => 'image/webp'],
        'intro' => 'WebP is a modern image format from Google. It is usually 25 to 35% smaller than JPEG at the same quality and supports transparency like PNG. All current browsers support it, which makes it a good default for websites. ' . $privacyLine,
        'steps' => [
            'Add JPG, PNG or BMP images. The output is already set to WebP.',
            'Set the quality. 65% is a good default for website images.',
            'Download the .webp files and use them on your site.',
        ],
        'sections' => [
            ['h' => 'Why use WebP on your website?', 'p' => [
                'Smaller images make pages load faster, which improves Core Web Vitals scores and how your pages rank in Google.',
                'WebP keeps transparency, so it can replace both JPEG photos and PNG graphics.',
            ]],
            ['h' => 'Browser note', 'p' => [
                'Saving as WebP uses your browser\'s own encoder. Chrome, Edge, Firefox and Safari 17 or later support it. If your browser can\'t save WebP, the WebP option is hidden.',
            ]],
        ],
        'faqs' => [
            ['q' => 'Is WebP better than JPEG?', 'a' => 'For websites, usually yes. It is smaller at the same quality and supports transparency. For email attachments and forms, JPEG is still the safest choice.'],
            ['q' => 'Does WebP keep transparency?', 'a' => 'Yes. PNGs with transparent backgrounds stay transparent when converted to WebP.'],
            ['q' => 'Can I convert WebP back to JPG or PNG?', 'a' => 'Yes. Upload the WebP file and choose JPEG or PNG in the Format menu.'],
            ['q' => 'Are my images uploaded?', 'a' => $privacyLine],
        ],
    ],

    'resize-image' => [
        'slug' => 'resize-image',
        'nav' => 'Resize image',
        'title' => 'Resize Image Online – Reduce Image Dimensions Free | COMPRESS',
        'description' => 'Resize photos to 1920, 1280 or 800 pixels and compress them in one step. Free bulk image resizer that keeps the aspect ratio. Nothing is uploaded.',
        'h1' => 'Resize and compress images',
        'tagline' => 'Shrink image dimensions and file size together',
        'preset' => ['resize' => 1280],
        'intro' => 'Most photos are far bigger than the screens they are shown on. Resizing a 4000-pixel phone photo to 1280 pixels makes it about 10 times smaller before compression even starts. COMPRESS resizes and compresses in one step and always keeps the aspect ratio. ' . $privacyLine,
        'steps' => [
            'Add your images. Max size is already set to 1280 px, which suits blogs and websites.',
            'Pick another size if you want: 3840 for 4K, 1920 for full-screen, or 800 for thumbnails.',
            'Compare and download.',
        ],
        'sections' => [
            ['h' => 'Which size should I pick?', 'p' => [
                '<strong>1920 px:</strong> full-width website banners and backgrounds. <strong>1280 px:</strong> blog and article images. <strong>800 px:</strong> product thumbnails and cards. <strong>3840 px:</strong> keeps 4K detail but trims oversized camera files.',
                'The size applies to the longer side, so portrait and landscape photos both keep their shape. Images that are already smaller than the size you pick are not enlarged.',
            ]],
        ],
        'faqs' => [
            ['q' => 'Will resizing stretch my image?', 'a' => 'No. The aspect ratio is always kept. Only the longer side is limited to the size you pick.'],
            ['q' => 'Can I resize many images at once?', 'a' => 'Yes. The Max size setting applies to every image in the batch.'],
            ['q' => 'Can I set an exact file size instead?', 'a' => 'Yes. Use the Target size menu, or go straight to <a href="/compress-image-to-100kb">compress to 100 KB</a>.'],
            ['q' => 'Is it private?', 'a' => $privacyLine],
        ],
    ],

    'compress-image-to-100kb' => $targetPage(100, [
        'intro' => 'Many online forms, job portals and government websites only accept photos under 100 KB. COMPRESS gets your image under that limit automatically: it finds the highest quality that fits, so the photo stays as clear as possible. ' . $privacyLine,
        'steps' => [
            'Add your photo or scanned document. The target is already set to 100 KB.',
            'COMPRESS finds the best quality that fits. Check the preview with the slider.',
            'Download the JPG and upload it to your form.',
        ],
        'sections' => [
            ['h' => 'Tips for photos under 100 KB', 'p' => [
                'Crop away empty background before compressing, so the space goes to your face or the document.',
                'For passport-style photos, a size of about 600 to 800 pixels is usually plenty. Choose "Max 800 px" for a sharper result.',
                'Need a different limit? Try <a href="/compress-image-to-50kb">50 KB</a>, <a href="/compress-image-to-200kb">200 KB</a> or <a href="/compress-image-to-20kb">20 KB</a>.',
            ]],
        ],
    ]),

    'compress-image-to-50kb' => $targetPage(50, [
        'intro' => 'Exam and job application forms often ask for a photo or signature under 50 KB. COMPRESS reduces your image to under 50 KB with the best quality that fits, without any software to install. ' . $privacyLine,
        'steps' => [
            'Add your photo, signature or document scan. The target is already 50 KB.',
            'Check the result with the comparison slider.',
            'Download the JPG and upload it.',
        ],
        'sections' => [
            ['h' => 'Getting a clear image under 50 KB', 'p' => [
                'Crop the image tightly first. Smaller dimensions leave more room for detail.',
                'For signatures, a clean white background compresses much better than a shadowed photo of paper.',
                'Other limits: <a href="/compress-image-to-20kb">20 KB</a>, <a href="/compress-image-to-100kb">100 KB</a>, <a href="/compress-image-to-200kb">200 KB</a>.',
            ]],
        ],
    ]),

    'compress-image-to-20kb' => $targetPage(20, [
        'intro' => 'Some forms limit signatures and photos to 20 KB or less. That is very small, so COMPRESS lowers the quality first and then the dimensions until the image fits, keeping it as clear as possible. ' . $privacyLine,
        'steps' => [
            'Add your signature or photo. The target is already 20 KB.',
            'Zoom into the preview to check that it is readable.',
            'Download and upload it to your form.',
        ],
        'sections' => [
            ['h' => 'Tips for 20 KB images', 'p' => [
                'Crop to just the signature or face. Every extra pixel of background uses space.',
                'If the result looks too small, ask whether the form also accepts <a href="/compress-image-to-50kb">50 KB</a>.',
            ]],
        ],
    ]),

    'compress-image-to-200kb' => $targetPage(200, [
        'intro' => 'A 200 KB limit is common for document uploads, listings and email-friendly photos. At 200 KB most photos still look sharp. COMPRESS picks the highest quality that fits. ' . $privacyLine,
        'steps' => [
            'Add your images. The target is already 200 KB.',
            'Compare the before-and-after with the slider.',
            'Download one JPG or a ZIP of all of them.',
        ],
        'sections' => [
            ['h' => 'Good to know', 'p' => [
                'If a photo is already under 200 KB, it is left as it is.',
                'Other limits: <a href="/compress-image-to-100kb">100 KB</a>, <a href="/compress-image-to-50kb">50 KB</a>, <a href="/compress-image-to-500kb">500 KB</a>.',
            ]],
        ],
    ]),

    'compress-image-to-500kb' => $targetPage(500, [
        'title' => 'Compress Image to 500KB Online Free | COMPRESS',
        'intro' => 'Marketplaces, real estate portals and website builders often cap uploads at 500 KB. That is enough for a sharp, full-screen photo. COMPRESS brings large camera photos under 500 KB with the best quality that fits. ' . $privacyLine,
        'steps' => [
            'Add your photos. The target is already 500 KB.',
            'Compare with the slider.',
            'Download the photos.',
        ],
        'sections' => [
            ['h' => 'Good to know', 'p' => [
                'Photos already under 500 KB are left as they are.',
                'Need smaller files? Try <a href="/compress-image-to-200kb">200 KB</a> or <a href="/compress-image-to-100kb">100 KB</a>.',
            ]],
        ],
    ]),
];
