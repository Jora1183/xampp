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

        .map-container {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .map-container img {
            width: 100%;
            height: auto;
            transition: transform 0.3s ease;
        }

        .map-container:hover img {
            transform: scale(1.02);
        }

        /* Map Legend Styling */
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .legend-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .legend-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .legend-icon.houses {
            background: linear-gradient(135deg, #5c7c3b 0%, #4a632f 100%);
            color: white;
        }

        .legend-icon.lake {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }

        .legend-icon.forest {
            background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
            color: white;
        }

        .legend-icon.facilities {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .legend-icon.parking {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }

        .legend-icon.paths {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
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
                    <a class="flex items-center gap-3" href="<?php echo $this->baseurl; ?>/">
                        <img src="<?php echo Uri::root(false); ?>images/main_logo.png" alt="Logo" class="h-12 w-auto">
                        <span class="font-serif text-xl font-bold text-resort-dark tracking-wide">Дивная усадьба</span>
                    </a>
                </div>

                <!-- Navigation (Menu Module) -->
                <?php if ($this->countModules('menu', true)): ?>
                    <nav class="hidden md:flex md:visible space-x-8" data-purpose="main-navigation">
                        <jdoc:include type="modules" name="menu" style="none" />
                    </nav>
                <?php endif; ?>

                <!-- Header Actions -->
                <div class="flex items-center space-x-4" data-purpose="header-actions">
                    <?php if ($this->countModules('search', true)): ?>
                        <div class="hidden sm:flex sm:visible relative">
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
        <!-- Spacer for Fixed Header -->
        <div class="h-20 w-full"></div>

        <?php if ($this->countModules('banner', true)): ?>
            <div class="container-banner full-width">
                <jdoc:include type="modules" name="banner" style="none" />
            </div>
        <?php endif; ?>

        <!-- Title Section -->
        <div class="bg-gray-50 py-12 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl font-serif font-bold text-resort-dark">Карта базы</h1>
                <p class="mt-4 text-gray-500 max-w-2xl mx-auto">Ознакомьтесь с расположением домов, инфраструктуры и природных достопримечательностей на территории нашей базы отдыха</p>
                <div class="h-[3px] w-14 bg-resort-green mx-auto rounded-full mt-6"></div>
            </div>
        </div>

        <!-- Map Content Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Map Image -->
                <div class="map-container mb-12">
                    <img src="<?php echo Uri::root(false); ?>images/resort_map.png" 
                         alt="Карта базы отдыха Дивная усадьба" 
                         class="w-full h-auto rounded-xl"
                         onerror="this.src='https://placehold.co/1200x800/e2e8f0/64748b?text=%D0%9A%D0%B0%D1%80%D1%82%D0%B0+%D0%B1%D0%B0%D0%B7%D1%8B';">
                </div>

                <!-- Legend Section -->
                <div class="bg-gray-50 rounded-2xl p-8">
                    <h2 class="text-2xl font-serif font-bold text-resort-dark mb-6 text-center">Условные обозначения</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Houses -->
                        <div class="legend-item">
                            <div class="legend-icon houses">
                                <span class="material-symbols-outlined">cottage</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Гостевые дома</p>
                                <p class="text-sm text-gray-500">Дома №1-9</p>
                            </div>
                        </div>

                        <!-- Lake -->
                        <div class="legend-item">
                            <div class="legend-icon lake">
                                <span class="material-symbols-outlined">water</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Озеро</p>
                                <p class="text-sm text-gray-500">Зона отдыха у воды</p>
                            </div>
                        </div>

                        <!-- Forest -->
                        <div class="legend-item">
                            <div class="legend-icon forest">
                                <span class="material-symbols-outlined">forest</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Лесная зона</p>
                                <p class="text-sm text-gray-500">Прогулочные маршруты</p>
                            </div>
                        </div>

                        <!-- Facilities -->
                        <div class="legend-item">
                            <div class="legend-icon facilities">
                                <span class="material-symbols-outlined">restaurant</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Инфраструктура</p>
                                <p class="text-sm text-gray-500">Кафе, баня, беседки</p>
                            </div>
                        </div>

                        <!-- Parking -->
                        <div class="legend-item">
                            <div class="legend-icon parking">
                                <span class="material-symbols-outlined">local_parking</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Парковка</p>
                                <p class="text-sm text-gray-500">Бесплатная стоянка</p>
                            </div>
                        </div>

                        <!-- Paths -->
                        <div class="legend-item">
                            <div class="legend-icon paths">
                                <span class="material-symbols-outlined">directions_walk</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Дорожки</p>
                                <p class="text-sm text-gray-500">Пешеходные тропы</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-12 text-center">
                    <p class="text-gray-500 mb-6">Площадь территории базы составляет более 5 гектаров живописной природы</p>
                    <a href="<?php echo $this->baseurl; ?>/index.php/razmeshchenie" 
                       class="inline-flex items-center gap-2 bg-resort-green hover:bg-resort-green-hover text-white px-8 py-3 rounded-full font-semibold transition-all transform hover:scale-105 shadow-lg">
                        <span class="material-symbols-outlined">villa</span>
                        Посмотреть размещение
                    </a>
                </div>

            </div>
        </div>

        <jdoc:include type="message" />

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
