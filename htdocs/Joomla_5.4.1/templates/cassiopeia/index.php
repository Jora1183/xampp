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
        /* DEBUG: Red border to verify CSS update */
        .container-gallery {
            border: 2px dashed red;
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
    <header class="fixed top-0 w-full bg-white shadow-sm border-b border-gray-100" style="z-index: 50;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center" data-purpose="logo-container">
                    <a class="flex items-center gap-2" href="<?php echo $this->baseurl; ?>/">
                        <svg class="h-8 w-8 text-resort-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                        <span
                            class="font-serif text-xl font-bold text-resort-dark tracking-wide"><?php echo $sitename; ?></span>
                    </a>
                </div>

                <!-- Navigation (Menu Module) -->
                <?php if ($this->countModules('menu', true)): ?>
                    <nav class="hidden md:flex space-x-8" data-purpose="main-navigation">
                        <jdoc:include type="modules" name="menu" style="none" />
                    </nav>
                <?php endif; ?>

                <!-- Header Actions -->
                <div class="flex items-center space-x-4" data-purpose="header-actions">
                    <?php if ($this->countModules('search', true)): ?>
                        <div class="hidden sm:flex relative">
                            <jdoc:include type="modules" name="search" style="none" />
                        </div>
                    <?php endif; ?>

                    <?php if ($this->countModules('header-right', true)): ?>
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
        <div class="container-hero full-width" style="margin-top: -5rem;">
            <!-- Negative margin to go under fixed header if needed, or rely on absolute positioning in demo css -->
            <section class="relative h-screen min-h-[600px] flex items-center justify-center pt-20">
                <div class="absolute inset-0 z-0">
                    <img alt="Nature Park Resort Landscape" class="w-full h-full object-cover"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuA0do_7vIn7FZ309sCt9m3PZbL3VlVOwjWar8TDUFD0Yg9CNq08J6wDbVqY6RUXOOCXi30DRjcrLXW3A-hUroy1c3BvLDJ_mHGUyxf0wICB1gYxzy-ft1Y8clyMkAamMQfG7TWfuVbdtlkL2zkA0Js-zXn8NxQDPzvv_Vgg69W6XOznoqotfvfiHtobgicfM69p8pZRCO_VHGHUBYDR_lMPxKoPBaDR7f5kZdrxc_HYFIvirVDGTG69zSxU-YLsxef6BG3SFEBYTQE" />
                    <div
                        class="absolute inset-0 bg-black/40 bg-gradient-to-t from-black/60 via-transparent to-black/30">
                    </div>
                </div>
                <div class="relative z-10 text-center px-4 max-w-4xl mx-auto" data-purpose="hero-content">
                    <h1
                        class="text-4xl md:text-6xl lg:text-7xl font-serif font-bold text-white mb-6 drop-shadow-lg leading-tight drop-shadow-xl">
                        Безмятежный отдых <br />в удивительном месте
                    </h1>
                    <p class="text-lg md:text-xl text-gray-100 mb-10 max-w-2xl mx-auto font-light drop-shadow-md">
                        пос. Новоильинский, Нытвенский р-н, Пермский край
                    </p>
                    <a class="inline-block bg-resort-green hover:bg-resort-green-hover text-white text-lg px-8 py-3 rounded-md font-semibold transition-all hover:scale-105 shadow-xl"
                        href="#accommodations">
                        Посмотреть Варианты
                    </a>
                </div>
            </section>
        </div>

        <!-- Accommodations Section -->
        <div class="grid-child container-top-a full-width">
            <!-- Added full-width manually if needed, or rely on inner max-w-7xl -->
            <section class="py-28 bg-white" id="accommodations">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl md:text-4xl font-serif font-bold text-resort-dark mb-4">Наши Уникальные
                            Размещения</h2>
                        <div class="h-[3px] w-14 bg-resort-green mx-auto rounded-full"></div>
                        <p class="mt-4 text-gray-500 max-w-3xl mx-auto leading-relaxed">База отдыха "Дивная усадьба"
                            расположена в уникальном месте и, одновременно, обеспечивает городской комфорт проживания.
                            Это небольшая база, создающая неповторимую атмосферу спокойного отдыха и комфорта в полном
                            единении с великолепной природой.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                        <article
                            class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 flex flex-col zoom-card hover:shadow-xl transition-shadow duration-300"
                            data-purpose="accommodation-card">
                            <div class="h-64 overflow-hidden relative">
                                <img alt="Lakeside Villa" class="w-full h-full object-cover"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDE8x4SduwX_kvs2lRzUKVIIPVnJSogR7GU0U5wOIIgUYThankVO7yQhu6HI8ws4Oz5p8m9fYinIEDwJm5VBlQgDqjUbtQrtwtoz3WmBUFBEmgkqzgGNwv3PGkgwpE1m9yflG7gAL157-iJYTJKHKdr9z8wBihu55GOoYP5eOMoXdkdSP02Rz7RzHH1HOWVbb9xakfKjbW5DF_c9igKkcd3I_XM8LjlfxcYsZZ6AQPesHrqFzuGzuiV2BX0pUthvb63GxU7GQjCyh4" />
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-resort-dark font-serif">Дом №1</h3>
                                </div>
                                <p class="text-gray-500 text-sm mb-6 flex-grow">Уютный дом для компании до 6 человек.
                                    Прекрасный вид и все удобства.</p>
                                <div class="border-t border-gray-100 pt-4 mt-auto">
                                    <p class="text-resort-dark font-bold text-lg mb-4">10000 руб.<span
                                            class="text-sm font-normal text-gray-500">/сутки</span></p>
                                    <a href="<?php echo $this->baseurl; ?>/booking-options"
                                        class="w-full block bg-resort-green hover:bg-resort-green-hover text-white rounded-lg font-bold transition-colors py-4 shadow-md text-center">
                                        Подробнее
                                    </a>
                                </div>
                            </div>
                        </article>
                        <article
                            class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 flex flex-col zoom-card hover:shadow-xl transition-shadow duration-300"
                            data-purpose="accommodation-card">
                            <div class="h-64 overflow-hidden relative">
                                <img alt="Forest Cabin" class="w-full h-full object-cover"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCdc4Q8dbUOtcGTEpOJBSz_9bqleerDLsukzoPIeQwMlWeifBH_v6zXXN4mBJyCObrLukdnyXd1P-ruyWjwXXl2jmT_z1ek2H9vLmIdq0Y6F2AT3HKF3ttoXFvN4DMMfqOUN2AbeWwH_Osc5B7wd_elVzN6CGITwCTPggILcnlWISkkc1uzPSfjQ6cOf7kB4Bix2LngAoXNaC70k8EW4HF2hgCaT313F4LYMNRrbR-6E-9YTZpyjvySaVjMS8ZF8EWiC-1IkFjFbp4" />
                                <span
                                    class="absolute top-4 right-4 bg-white/90 px-3 py-1 rounded-full text-xs font-bold text-resort-green shadow-sm">Популярный</span>
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-resort-dark font-serif">Дом №4</h3>
                                </div>
                                <p class="text-gray-500 text-sm mb-6 flex-grow">Просторный дом для больших компаний до
                                    10 человек. Идеально для праздников.</p>
                                <div class="border-t border-gray-100 pt-4 mt-auto">
                                    <p class="text-resort-dark font-bold text-lg mb-4">15000 руб.<span
                                            class="text-sm font-normal text-gray-500">/сутки</span></p>
                                    <a href="<?php echo $this->baseurl; ?>/booking-options"
                                        class="w-full block bg-resort-green hover:bg-resort-green-hover text-white rounded-lg font-bold transition-colors py-4 shadow-md text-center">
                                        Подробнее
                                    </a>
                                </div>
                            </div>
                        </article>
                        <article
                            class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 flex flex-col zoom-card hover:shadow-xl transition-shadow duration-300"
                            data-purpose="accommodation-card">
                            <div class="h-64 overflow-hidden relative">
                                <img alt="Executive Bungalow" class="w-full h-full object-cover"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuD1e5ZnuyXQcGVvOEaKq35a63hmj15VbfgR8uUeltbvDN4bycJslJi7b4xaEl-j2e5xSASFy0VRwkXYxx_Q9ryy0N2Rd7e7gn2nUh8WDvFfav-N_Tfxb2Lbtvl5Xx64gj9TbJ0lUNKhbxApDEhFs7yNFlPma0coyftFTLjmcEE11HNIuu-KVsNqr377--gFGlhnbqM5-V_4T47zuk2Ty14vKIwHFxeRdxV3qMtQRJ4xJo2MNIn3uhH3ilFK_LWJFolNefhbo1fYUJY" />
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-resort-dark font-serif">Кемпинговые палатки</h3>
                                </div>
                                <p class="text-gray-500 text-sm mb-6 flex-grow">Для любителей настоящего туризма.
                                    Вместимость до 5 человек.</p>
                                <div class="border-t border-gray-100 pt-4 mt-auto">
                                    <p class="text-resort-dark font-bold text-lg mb-4">3000 руб.<span
                                            class="text-sm font-normal text-gray-500">/сутки</span></p>
                                    <a href="<?php echo $this->baseurl; ?>/booking-options"
                                        class="w-full block bg-resort-green hover:bg-resort-green-hover text-white rounded-lg font-bold transition-colors py-4 shadow-md text-center">
                                        Подробнее
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>
        </div>

        <!-- Services Section -->
        <div class="grid-child container-top-b full-width">
            <section class="py-28 bg-resort-light-gray relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-resort-green/5 rounded-full blur-3xl">
                </div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl md:text-4xl font-serif font-bold text-resort-dark mb-4">Развлечения и Услуги
                        </h2>
                        <div class="h-[3px] w-14 bg-resort-green mx-auto rounded-full"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12">
                        <div
                            class="flex items-start gap-4 p-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-300 flex-row">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 rounded-full bg-resort-green/10 flex items-center justify-center"
                                    style="background-color: #f0f7e6;"><span
                                        class="material-symbols-outlined text-3xl text-resort-green">pool</span></div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-resort-dark mb-2 font-serif">Бассейн с джакузи</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Роскошный бассейн с джакузи, водопадом
                                    и мультимедийным сопровождением.</p>
                            </div>
                        </div>
                        <div
                            class="flex items-start gap-4 p-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-300 flex-row">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 rounded-full bg-resort-green/10 flex items-center justify-center"
                                    style="background-color: #f0f7e6;"><span
                                        class="material-symbols-outlined text-3xl text-resort-green">sauna</span></div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-resort-dark mb-2 font-serif">Дровяная сауна</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Традиционная русская баня на дровах для
                                    полного расслабления и оздоровления.</p>
                            </div>
                        </div>
                        <div
                            class="flex items-start gap-4 p-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-300 flex-row">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 rounded-full bg-resort-green/10 flex items-center justify-center"
                                    style="background-color: #f0f7e6;"><span
                                        class="material-symbols-outlined text-3xl text-resort-green">outdoor_grill</span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-resort-dark mb-2 font-serif">Гриль-домик</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Уютный гриль-домик для теплых вечеров и
                                    приготовления вкуснейших блюд на огне.</p>
                            </div>
                        </div>
                        <div
                            class="flex items-start gap-4 p-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-300 flex-row">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 rounded-full bg-resort-green/10 flex items-center justify-center"
                                    style="background-color: #f0f7e6;"><span
                                        class="material-symbols-outlined text-3xl text-resort-green">sports_volleyball</span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-resort-dark mb-2 font-serif">Спортивные развлечения
                                </h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Волейбол, настольный теннис, рыбалка,
                                    велосипед, сап-борд для активного отдыха.</p>
                            </div>
                        </div>
                        <div
                            class="flex items-start gap-4 p-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-300 flex-row">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 rounded-full bg-resort-green/10 flex items-center justify-center"
                                    style="background-color: #f0f7e6;"><span
                                        class="material-symbols-outlined text-3xl text-resort-green">deck</span></div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-resort-dark mb-2 font-serif">Пикники</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Организация пикников на свежем воздухе
                                    в живописных уголках нашей усадьбы.</p>
                            </div>
                        </div>
                        <div
                            class="flex items-start gap-4 p-4 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-300 flex-row">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 rounded-full bg-resort-green/10 flex items-center justify-center"
                                    style="background-color: #f0f7e6;"><span
                                        class="material-symbols-outlined text-3xl text-resort-green">museum</span></div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-resort-dark mb-2 font-serif">Экскурсии</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Музей ложки, Конный завод,
                                    Крестовоздвиженский скит и другие интересные места.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

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
        <div class="grid-child container-bottom-a full-width">
            <section class="py-24 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl md:text-4xl font-serif font-bold text-resort-dark mb-4">Отзывы</h2>
                        <div class="h-[3px] w-14 bg-resort-green mx-auto rounded-full"></div>
                    </div>
                    <div class="max-w-4xl mx-auto">
                        <div class="bg-resort-light-gray p-8 md:p-12 rounded-2xl relative">
                            <span
                                class="absolute top-8 left-8 text-6xl text-resort-green opacity-20 font-serif">“</span>
                            <blockquote
                                class="text-lg md:text-xl text-gray-600 italic text-center mb-6 relative z-10 leading-relaxed">
                                "Прекрасная база для тех, кто любит спокойный тихий отдых, наедине с природой и душевным
                                покоем. Все, что нужно для комфортного отдыха есть. А про воздух вообще молчу. Он
                                нереально вкусный!"
                            </blockquote>
                            <div class="text-center">
                                <cite class="not-italic font-bold text-resort-dark font-serif text-lg">— Екатерина
                                    Кириллова</cite>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- CTA Section -->
        <div class="grid-child container-bottom-b full-width">
            <section class="relative py-24 md:py-32">
                <div class="absolute inset-0 z-0">
                    <img alt="Resort Sunset" class="w-full h-full object-cover"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCTCGHfxPHbB3zj1xkb4c2avUHaGUfbJ_-4ln52FRvLC5IbDZbun3V6lUjhoCBqQ-v39ccPE449RAR8Qo1tK25d9lalUgtpLI1y4s7ERrfw6VzEI78GMmN5F0UbeIybqKGfmRr58eoVIRuFxkkGRpRHb-p54V9Iq39RRJk5gwPx0ptlHXn6IbpN0QjMNIRXV0xTrQnU8s16ak4E0MSijGqiXoSJeeOTrcI5Uo1jB_dwpoOQUtuZh88zrvdmO3Ms9HfEp_LTLB3hhnQ" />
                    <div class="absolute inset-0 bg-resort-dark/60"></div>
                </div>
                <div class="relative z-10 max-w-4xl mx-auto text-center px-4" data-purpose="cta-content">
                    <h2 class="text-3xl md:text-5xl font-serif font-bold text-white mb-8 leading-tight">Готовы к Своему
                        Побегу?</h2>
                    <p class="text-gray-200 text-lg mb-10 max-w-2xl mx-auto">Для любимых гостей, постоянных и новых
                        клиентов База отдыха «Дивная усадьба» предлагает уникальные предложения на 2025 год</p>
                    <a class="inline-block bg-resort-green hover:bg-resort-green-hover text-white text-lg px-10 py-4 rounded-full font-bold transition-all hover:scale-105 shadow-xl border-2 border-transparent hover:border-white/20"
                        href="#">
                        Бронировать Сейчас
                    </a>
                </div>
            </section>
        </div>
    </div>

    <?php if ($this->countModules('footer', true)): ?>
        <footer class="container-footer footer full-width bg-gray-100 border-t border-gray-200 pt-24 pb-12">
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