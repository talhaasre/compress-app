<?php
/** Plain content pages: about, privacy and terms. */
$email = $config['contact_email'];
$updated = '18 November 2026';

return [
    'about' => [
        'title' => 'About COMPRESS | Free Image Compressor by Asresoft',
        'description' => 'COMPRESS is a free, open source image compressor made by Asresoft, a digital agency in Mumbai, India. Learn how it works and how to contact us.',
        'h1' => 'About COMPRESS',
        'html' => <<<HTML
<p>COMPRESS is a free online image compressor made by <a href="https://asresoft.com" target="_blank" rel="noopener">Asresoft</a>, a digital marketing, design and web development agency based in Mumbai, India.</p>
<p>We built it because we compress images every day for client websites and ad campaigns, and wanted a tool that shows the result before you download, keeps PNG transparency, and never sends images to a server.</p>
<h2>How it works</h2>
<p>Everything happens in your browser. Your images are decoded, compressed and zipped on your own device using JavaScript, so they are never uploaded. This is also why COMPRESS stays fast with large batches.</p>
<h2>Open source</h2>
<p>The code is open source under the MIT license. You can read it, report issues or suggest improvements on <a href="https://github.com/talhaasre/compress-app" target="_blank" rel="noopener">GitHub</a>.</p>
<h2>Contact</h2>
<p>Questions, ideas or problems? Email <a href="mailto:{$email}">{$email}</a>.</p>
HTML,
    ],

    'privacy' => [
        'title' => 'Privacy Policy | COMPRESS',
        'description' => 'How COMPRESS handles your data: images never leave your device. We use Google Analytics and Google AdSense, which use cookies.',
        'h1' => 'Privacy policy',
        'html' => <<<HTML
<p><em>Last updated: {$updated}</em></p>
<p>This policy explains what data COMPRESS (compress-app.com) collects. The site is run by Asresoft, Mumbai, India. You can contact us at <a href="mailto:{$email}">{$email}</a>.</p>
<h2>Your images</h2>
<p>Images you add to COMPRESS are processed entirely in your web browser. They are <strong>never uploaded</strong> to our servers or anyone else's, and we cannot see, store or share them. When you close or reload the page, they are gone.</p>
<h2>Analytics</h2>
<p>We use Google Analytics (through Google Tag Manager) to understand how many people visit, which pages they use and roughly where they come from. Google Analytics uses cookies and collects information such as your IP address, browser, device and pages viewed. We use this only in aggregate to improve the site. See <a href="https://policies.google.com/technologies/partner-sites" target="_blank" rel="noopener">how Google uses data from sites that use its services</a>.</p>
<h2>Advertising</h2>
<p>COMPRESS is free and is paid for by ads served by Google AdSense. Google and its partners use cookies to show ads based on your previous visits to this and other websites. Google's use of advertising cookies lets it and its partners show ads to you based on your visits to this site and other sites on the internet.</p>
<p>You can turn off personalised advertising in <a href="https://adssettings.google.com" target="_blank" rel="noopener">Google Ad Settings</a> or at <a href="https://www.aboutads.info/choices/" target="_blank" rel="noopener">aboutads.info</a>. Visitors in the European Economic Area, the UK and Switzerland are asked for consent before personalised ads are shown.</p>
<h2>Cookies</h2>
<p>COMPRESS itself does not set cookies. The cookies mentioned above are set by Google. You can block or delete cookies in your browser settings. The compressor still works without them.</p>
<h2>Children</h2>
<p>COMPRESS is a general-audience tool and does not knowingly collect personal information from children.</p>
<h2>Changes</h2>
<p>If we change this policy, we will update the date at the top of this page.</p>
HTML,
    ],

    'terms' => [
        'title' => 'Terms of Use | COMPRESS',
        'description' => 'Terms of use for COMPRESS, the free online image compressor by Asresoft.',
        'h1' => 'Terms of use',
        'html' => <<<HTML
<p><em>Last updated: {$updated}</em></p>
<p>By using COMPRESS (compress-app.com) you agree to these terms.</p>
<h2>The service</h2>
<p>COMPRESS is a free tool for compressing, converting and resizing images in your browser. It is provided "as is", without warranties of any kind. We do our best to keep it working well, but we can't guarantee that it will always be available or error-free, or that results will meet any particular requirement, such as a form's file size rule.</p>
<h2>Your content</h2>
<p>You keep all rights to your images. Because they are processed on your device and never uploaded, we don't receive or store them. You are responsible for having the right to use the images you process.</p>
<h2>Acceptable use</h2>
<p>Don't try to disrupt the site, overload it with automated requests, or use it for anything unlawful.</p>
<h2>Liability</h2>
<p>To the extent allowed by law, Asresoft is not liable for any loss or damage arising from using COMPRESS. Keep a copy of your original images.</p>
<h2>Open source</h2>
<p>The COMPRESS source code is licensed under the <a href="https://github.com/talhaasre/compress-app/blob/master/LICENSE" target="_blank" rel="noopener">MIT license</a>. These terms cover the hosted website at compress-app.com.</p>
<h2>Contact</h2>
<p><a href="mailto:{$email}">{$email}</a></p>
HTML,
    ],
];
