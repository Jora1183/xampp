<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app = Factory::getApplication();
$input = $app->getInput();
$wa = $this->getWebAssetManager();

// Browsers support SVG favicons
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon-pinned.svg', '', [], true, 1), 'mask-icon', 'rel', ['color' => '#000']);

// Detecting Active Variables
$option = $input->getCmd('option', '');
$view = $input->getCmd('view', '');
$layout = $input->getCmd('layout', '');
$task = $input->getCmd('task', '');
$itemid = $input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';

// Color Theme
$paramsColorName = $this->params->get('colorName', 'colors_standard');
$assetColorName = 'theme.' . $paramsColorName;

// Use a font scheme if set in the template style options
$paramsFontScheme = $this->params->get('useFontScheme', false);
$fontStyles = '';

if ($paramsFontScheme) {
    if (stripos($paramsFontScheme, 'https://') === 0) {
        $this->getPreloadManager()->preconnect('https://fonts.googleapis.com/', ['crossorigin' => 'anonymous']);
        $this->getPreloadManager()->preconnect('https://fonts.gstatic.com/', ['crossorigin' => 'anonymous']);
        $this->getPreloadManager()->preload($paramsFontScheme, ['as' => 'style', 'crossorigin' => 'anonymous']);
        $wa->registerAndUseStyle('fontscheme.current', $paramsFontScheme, [], ['rel' => 'lazy-stylesheet', 'crossorigin' => 'anonymous']);

        if (preg_match_all('/family=([^?:]*):/i', $paramsFontScheme, $matches) > 0) {
            $fontStyles = '--cassiopeia-font-family-body: "' . str_replace('+', ' ', $matches[1][0]) . '", sans-serif;
			--cassiopeia-font-family-headings: "' . str_replace('+', ' ', $matches[1][1] ?? $matches[1][0]) . '", sans-serif;
			--cassiopeia-font-weight-normal: 400;
			--cassiopeia-font-weight-headings: 700;';
        }
    } elseif ($paramsFontScheme === 'system') {
        $fontStylesBody = $this->params->get('systemFontBody', '');
        $fontStylesHeading = $this->params->get('systemFontHeading', '');

        if ($fontStylesBody) {
            $fontStyles = '--cassiopeia-font-family-body: ' . $fontStylesBody . ';
            --cassiopeia-font-weight-normal: 400;';
        }
        if ($fontStylesHeading) {
            $fontStyles .= '--cassiopeia-font-family-headings: ' . $fontStylesHeading . ';
    		--cassiopeia-font-weight-headings: 700;';
        }
    } else {
        $wa->registerAndUseStyle('fontscheme.current', $paramsFontScheme, ['version' => 'auto'], ['rel' => 'lazy-stylesheet']);
        $this->getPreloadManager()->preload($wa->getAsset('style', 'fontscheme.current')->getUri() . '?' . $this->getMediaVersion(), ['as' => 'style']);
    }
}

// Enable assets
$wa->usePreset('template.cassiopeia.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr'))
    ->useStyle('template.active.language')
    ->registerAndUseStyle($assetColorName, 'global/' . $paramsColorName . '.css')
    ->useStyle('template.user')
    ->useScript('template.user')
    ->addInlineStyle(":root {
		--hue: 214;
		--template-bg-light: #f0f4fb;
		--template-text-dark: #495057;
		--template-text-light: #ffffff;
		--template-link-color: var(--link-color);
		--template-special-color: #001B4C;
		$fontStyles
	}");

// Override 'template.active' asset to set correct ltr/rtl dependency
$wa->registerStyle('template.active', '', [], [], ['template.cassiopeia.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr')]);

// Logo file or site title param
if ($this->params->get('logoFile')) {
    $logo = HTMLHelper::_('image', Uri::root(false) . htmlspecialchars($this->params->get('logoFile'), ENT_QUOTES), $sitename, ['loading' => 'eager', 'decoding' => 'async'], false, 0);
} elseif ($this->params->get('siteTitle')) {
    $logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
} else {
    $logo = HTMLHelper::_('image', 'logo.svg', $sitename, ['class' => 'logo d-inline-block', 'loading' => 'eager', 'decoding' => 'async'], true, 0);
}

$hasClass = '';

if ($this->countModules('sidebar-left', true)) {
    $hasClass .= ' has-sidebar-left';
}

if ($this->countModules('sidebar-right', true)) {
    $hasClass .= ' has-sidebar-right';
}

// Container
$wrapper = $this->params->get('fluidContainer') ? 'wrapper-fluid' : 'wrapper-static';

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

$stickyHeader = $this->params->get('stickyHeader') ? 'position-sticky sticky-top' : '';

// Defer fontawesome for increased performance. Once the page is loaded javascript changes it to a stylesheet.
$wa->getAsset('style', 'fontawesome')->setAttribute('rel', 'lazy-stylesheet');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">

<head>
    <jdoc:include type="metas" />
    <jdoc:include type="styles" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&amp;family=Lato:wght@300;400;700&amp;display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'resort-green': '#5c7c3b',
                        'resort-green-hover': '#4a632f',
                        'resort-light-gray': '#f9f9f9',
                        'resort-dark': '#1a1a1a',
                    },
                    fontFamily: {
                        sans: ['Lato', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }

        .zoom-card:hover img {
            transform: scale(1.05);
        }

        .zoom-card img {
            transition: transform 0.5s ease;
        }

        /* Gallery Module Styling - Horizontal Scroll */
        .container-gallery {
            /* cleaned up debug border */
        }

        .container-gallery ul,
        .container-gallery ol,
        .container-gallery .custom ul {
            display: flex !important;
            gap: 1.5rem;
            padding: 1rem 0.5rem 2rem 0.5rem;
            list-style: none;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scrollbar-width: thin;
            scrollbar-color: #5c7c3b #f0f4fb;
            margin: 0;
        }

        /* Custom Scrollbar */
        .container-gallery ul::-webkit-scrollbar {
            height: 8px;
        }

        .container-gallery ul::-webkit-scrollbar-track {
            background: #f0f4fb;
            border-radius: 4px;
        }

        .container-gallery ul::-webkit-scrollbar-thumb {
            background-color: #5c7c3b;
            border-radius: 4px;
        }

        .container-gallery li {
            flex: 0 0 350px;
            /* Fixed width for cards */
            scroll-snap-align: start;
        }

        .container-gallery li img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .container-gallery li:hover img {
            transform: scale(1.02);
        }

        @media (max-width: 640px) {
            .container-gallery li {
                flex: 0 0 280px;
                /* Smaller width on mobile */
            }
        }

        /* Hero Search Form Styling */
        @media (min-width: 768px) {
            .hero-simplified-form {
                display: flex !important;
                align-items: flex-end !important;
                flex-wrap: nowrap !important;
                gap: 1rem !important;
                background: white;
                padding: 1.5rem !important;
                border-radius: 0.75rem;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                max-width: 100%;
                margin: 0 auto;
            }

            .hero-simplified-form .row {
                display: contents !important;
            }

            .hero-simplified-form .col-md-4 {
                width: auto !important;
                flex: 1 !important;
                min-width: 160px;
                margin-bottom: 0 !important;
            }

            /* Button Alignment */
            .hero-simplified-form .mt-3 {
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                flex: 0 0 auto !important;
                width: auto !important;
            }

            .hero-simplified-form .d-grid {
                display: block !important;
            }
        }

        /* Common Styles */
        .hero-simplified-form label {
            font-size: 0.875rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .hero-simplified-form .form-control,
        .hero-simplified-form .form-select {
            border: 1px solid #d1d5db;
            padding: 0 0.75rem;
            /* Standardized height via user.css, removing vertical padding */
            border-radius: 0.375rem;
            height: 48px !important;
        }

        .hero-simplified-form .btn-primary {
            background-color: #5c7c3b;
            border-color: #5c7c3b;
            padding: 0 2rem;
            font-weight: 600;
            height: 48px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .hero-simplified-form .btn-primary:hover {
            background-color: #4a632f;
            border-color: #4a632f;
        }

        /* Force Home menu item to the left */
        .item-101 {
            order: -1;
        }

        /* Header Navigation Styling */

        /* 1. Top Level Menu Container */
        nav[data-purpose="main-navigation"]>ul {
            display: flex !important;
            flex-direction: row !important;
            gap: 2rem !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
        }

        /* 2. List Items (Parents) */
        nav[data-purpose="main-navigation"] li {
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
            width: auto !important;
            position: relative !important;
            /* For dropdown positioning */
        }

        /* 3. Links Styling */
        nav[data-purpose="main-navigation"] a {
            text-decoration: none !important;
            color: #4b5563 !important;
            /* text-gray-600 */
            font-weight: 500 !important;
            font-size: 1rem !important;
            transition: color 0.2s;
            display: flex !important;
            align-items: center !important;
            height: 48px !important;
            min-height: 48px !important;
            /* Enforce minimum */
            line-height: 1 !important;
            /* Reset line height */
            padding: 0 1rem !important;
            /* Add padding for clickable area */
            white-space: nowrap !important;
        }

        nav[data-purpose="main-navigation"] a:hover,
        nav[data-purpose="main-navigation"] li.active>a,
        nav[data-purpose="main-navigation"] li.current>a {
            color: #5c7c3b !important;
            /* text-resort-green */
        }

        /* 4. Dropdown (Submenus) Styling */
        nav[data-purpose="main-navigation"] ul ul,
        nav[data-purpose="main-navigation"] .mod-menu__sub {
            display: none !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            background: white !important;
            min-width: 200px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 0 !important;
            z-index: 50 !important;
            flex-direction: column !important;
            gap: 0 !important;
        }

        nav[data-purpose="main-navigation"] ul ul li {
            width: 100% !important;
        }

        nav[data-purpose="main-navigation"] ul ul a {
            padding: 0.5rem 1rem !important;
            font-size: 0.95rem !important;
        }

        nav[data-purpose="main-navigation"] ul ul a:hover {
            background-color: #f9fafb !important;
            color: #5c7c3b !important;
        }

        /* Show Dropdown on Hover */
        nav[data-purpose="main-navigation"] li:hover>ul {
            display: flex !important;
        }

        /* Hide redundant Solidres header elements on all pages */
        .reservation_asset_item h1,
        .reservation_asset_item .address_1,
        .reservation_asset_item .show_map,
        .reservation_asset_item .reservation_asset_subinfo,
        .sr-login-form {
            display: none !important;
        }
    </style>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <jdoc:include type="scripts" />
</head>

<body class="font-sans text-gray-600 antialiased site <?php echo $option
    . ' ' . $wrapper
    . ' view-' . $view
    . ($layout ? ' layout-' . $layout : ' no-layout')
    . ($task ? ' task-' . $task : ' no-task')
    . ($itemid ? ' itemid-' . $itemid : '')
    . ($pageclass ? ' ' . $pageclass : '')
    . $hasClass
    . ($this->direction == 'rtl' ? ' rtl' : '');
?>">
    <header class="fixed top-0 w-full bg-white shadow-sm border-b border-gray-100" style="z-index: 1050;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center h-20 gap-8">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center" data-purpose="logo-container">
                    <a class="flex items-center gap-3" href="<?php echo $this->baseurl; ?>/">
                        <img src="<?php echo Uri::root(false); ?>images/main_logo.png" alt="Logo" class="h-12 w-auto">
                        <span class="font-serif text-xl font-bold text-resort-dark tracking-wide">Дивная усадьба</span>
                    </a>
                </div>

                <!-- Navigation (Menu Module) -->
                <?php if ($this->countModules('menu', true)): ?>
                    <nav class="hidden md:flex md:visible" data-purpose="main-navigation">
                        <jdoc:include type="modules" name="menu" style="none" />
                    </nav>
                <?php endif; ?>

                <!-- Header Actions -->
                <div class="flex items-center" data-purpose="header-actions">
                    <?php if ($this->countModules('search', true)): ?>
                        <div class="hidden sm:flex sm:visible relative">
                            <jdoc:include type="modules" name="search" style="none" />
                        </div>
                    <?php endif; ?>

                    <?php if ($this->countModules('header-right', true) && $option !== 'com_users'): ?>
                        <jdoc:include type="modules" name="header-right" style="none" />
                    <?php endif; ?>

                    <button class="md:hidden text-gray-600 hover:text-resort-green focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="site-grid">
        <?php if ($this->countModules('banner', true)): ?>
            <div class="container-banner full-width">
                <jdoc:include type="modules" name="banner" style="none" />
            </div>
        <?php endif; ?>

        <!-- Hero Section -->
        <!-- Hero Section -->
        <?php if ($this->countModules('hero', true)): ?>
            <div class="container-hero full-width" style="margin-top: -5rem;">
                <section class="relative h-screen min-h-[600px] flex items-center justify-center pt-20">
                    <div class="absolute inset-0 z-0">
                        <img alt="Nature Park Resort Landscape" class="w-full h-full object-cover"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuA0do_7vIn7FZ309sCt9m3PZbL3VlVOwjWar8TDUFD0Yg9CNq08J6wDbVqY6RUXOOCXi30DRjcrLXW3A-hUroy1c3BvLDJ_mHGUyxf0wICB1gYxzy-ft1Y8clyMkAamMQfG7TWfuVbdtlkL2zkA0Js-zXn8NxQDPzvv_Vgg69W6XOznoqotfvfiHtobgicfM69p8pZRCO_VHGHUBYDR_lMPxKoPBaDR7f5kZdrxc_HYFIvirVDGTG69zSxU-YLsxef6BG3SFEBYTQE" />
                        <div
                            class="absolute inset-0 bg-black/40 bg-gradient-to-t from-black/60 via-transparent to-black/30">
                        </div>
                    </div>
                    <div class="relative z-10 text-center px-4 max-w-7xl w-full mx-auto" data-purpose="hero-content">
                        <jdoc:include type="modules" name="hero" style="none" />
                    </div>
                </section>
            </div>
        <?php endif; ?>

        <!-- Accommodations Section -->
        <?php if ($this->countModules('top-a', true)): ?>
            <div class="grid-child container-top-a full-width">
                <!-- Added full-width manually if needed, or rely on inner max-w-7xl -->
                <jdoc:include type="modules" name="top-a" style="none" />
            </div>
        <?php endif; ?>

        <!-- Services Section -->
        <?php if ($this->countModules('top-b', true)): ?>
            <div class="grid-child container-top-b full-width">
                <jdoc:include type="modules" name="top-b" style="none" />
            </div>
        <?php endif; ?>

        <!-- Gallery Section -->
        <?php if ($this->countModules('gallery', true)): ?>
            <div class="grid-child container-gallery full-width">
                <section class="py-24 bg-white relative">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center mb-12">
                            <h2 class="text-3xl md:text-4xl font-serif font-bold text-resort-dark mb-4">Галерея</h2>
                            <div class="h-[3px] w-14 bg-resort-green mx-auto rounded-full"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <jdoc:include type="modules" name="gallery" style="none" />
                        </div>
                    </div>
                </section>
            </div>
        <?php endif; ?>

        <?php if ($this->countModules('sidebar-left', true)): ?>
            <div class="grid-child container-sidebar-left">
                <jdoc:include type="modules" name="sidebar-left" style="card" />
            </div>
        <?php endif; ?>

        <div class="grid-child container-component">
            <jdoc:include type="modules" name="breadcrumbs" style="none" />
            <jdoc:include type="modules" name="main-top" style="card" />
            <jdoc:include type="message" />
            <main>
                <jdoc:include type="component" />
            </main>
            <jdoc:include type="modules" name="main-bottom" style="card" />
        </div>

        <?php if ($this->countModules('sidebar-right', true)): ?>
            <div class="grid-child container-sidebar-right">
                <jdoc:include type="modules" name="sidebar-right" style="card" />
            </div>
        <?php endif; ?>

        <!-- Reviews Section -->
        <?php if ($this->countModules('bottom-a', true)): ?>
            <div class="grid-child container-bottom-a full-width">
                <jdoc:include type="modules" name="bottom-a" style="none" />
            </div>
        <?php endif; ?>

        <!-- CTA Section -->
        <?php if ($this->countModules('bottom-b', true)): ?>
            <div class="grid-child container-bottom-b full-width">
                <jdoc:include type="modules" name="bottom-b" style="none" />
            </div>
        <?php endif; ?>
    </div>

    <?php if ($this->countModules('footer', true)): ?>
        <footer class="container-footer footer full-width">
            <div class="grid-child">
                <jdoc:include type="modules" name="footer" style="none" />
            </div>
        </footer>
    <?php endif; ?>

    <?php if ($this->params->get('backTop') == 1): ?>
        <a href="#top" id="back-top" class="back-to-top-link"
            aria-label="<?php echo Text::_('TPL_CASSIOPEIA_BACKTOTOP'); ?>">
            <span class="icon-arrow-up icon-fw" aria-hidden="true"></span>
        </a>
    <?php endif; ?>

    <jdoc:include type="modules" name="debug" style="none" />
</body>

</html>