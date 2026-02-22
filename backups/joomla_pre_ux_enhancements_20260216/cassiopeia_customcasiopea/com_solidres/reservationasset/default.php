<?php
/**
 ------------------------------------------------------------------------
 SOLIDRES - Accommodation booking extension for Joomla
 ------------------------------------------------------------------------
 * @author    Solidres Team <contact@solidres.com>
 * @website   https://www.solidres.com
 * @copyright Copyright (C) 2013 Solidres. All Rights Reserved.
 * @license   GNU General Public License version 3, or later
 ------------------------------------------------------------------------
 */

/*
 * Modern Property Detail View Layout
 * This layout provides a clean, card-based design for property listings
 * 
 * @version 3.2.1
 */

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

// Load custom CSS
$doc = Factory::getDocument();
$doc->addStyleSheet(Uri::root() . 'templates/cassiopeia_customcasiopea/css/solidres-custom.css');
$doc->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
?>

<!-- Property Detail View -->
<div class="container my-5 solidres-property-view">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header mb-4">
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- LEFT COLUMN: Main Content (75%) -->
        <div class="col-lg-8">
            
            <!-- This section would contain Solidres template output -->
            <?php echo $this->loadTemplate('content'); ?>

        </div>

        <!-- RIGHT COLUMN: Sticky Sidebar (25%) -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 2rem;">
                
                <!-- Contact Widget -->
                <div class="sidebar-widget text-center">
                    <h5><?php echo Text::_('SR_NEED_HELP'); ?></h5>
                    <p class="small text-muted"><?php echo Text::_('SR_CONCIERGE_AVAILABLE_247'); ?></p>
                    <a href="tel:+1234567890" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-phone"></i> +1 234 567 890
                    </a>
                </div>

                <!-- Map Widget Placeholder -->
                <?php if ($this->countModules('property-map')) : ?>
                    <div class="sidebar-widget p-0 overflow-hidden">
                        <jdoc:include type="modules" name="property-map" style="none" />
                    </div>
                <?php else : ?>
                    <div class="sidebar-widget p-0 overflow-hidden">
                        <div style="background: #e9ecef; height: 200px; display: flex; align-items-center; justify-content-center;">
                            <span class="text-muted"><i class="fas fa-map-marker-alt"></i> <?php echo Text::_('SR_MAP'); ?></span>
                        </div>
                        <div class="p-3">
                            <p class="mb-0 small"><i class="fas fa-map-pin text-danger"></i> <?php echo Text::_('SR_PROPERTY_LOCATION'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Why Book Direct Widget -->
                <div class="sidebar-widget">
                    <h6 class="fw-bold mb-3"><?php echo Text::_('SR_WHY_BOOK_DIRECT'); ?></h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i> 
                            <?php echo Text::_('SR_BEST_PRICE_GUARANTEE'); ?>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i> 
                            <?php echo Text::_('SR_FREE_CANCELLATION'); ?>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i> 
                            <?php echo Text::_('SR_EARLY_CHECKIN'); ?>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i> 
                            <?php echo Text::_('SR_SPECIAL_OFFERS'); ?>
                        </li>
                    </ul>
                </div>

                <!-- Additional Sidebar Modules -->
                <?php if ($this->countModules('property-sidebar')) : ?>
                    <jdoc:include type="modules" name="property-sidebar" style="none" />
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>