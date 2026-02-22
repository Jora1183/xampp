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
 * This layout file can be overridden by copying to:
 *
 * /templates/TEMPLATENAME/html/com_solidres/reservationasset/default_roomtype.php
 *
 * However, occasionally we will need to update template/layout related files and it is the template developers'
 * responsibility to update the overridden files (if any) to maintain full compatibility with Solidres.
 *
 * We do not provide support if any of the overridden files are out of date and are not compatible with Solidres.
 *
 * @version 3.1.1
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

if (!$this->isAmending):
    echo SRLayoutHelper::render('asset.coupon_form', [
        'asset' => $this->item,
        'coupon' => $this->coupon,
        'isFresh' => $this->isFresh,
    ]);
endif;

$layout = SRLayoutHelper::getInstance();
$showInquiryForm = !empty($this->item->params['show_inquiry_form']);
$inquiryRoomType = $showInquiryForm && !empty($this->item->params['inquiry_form_scope']);

if ($showInquiryForm):
    echo $this->loadTemplate('inquiry_form');
endif;

?>

<a id="book-form"></a>

<?php
if (
    isset($this->item->params['show_inline_checkavailability_form'])
    && $this->item->params['show_inline_checkavailability_form'] == 1
    && !$this->disableOnlineBooking
    && !$this->isAmending
): ?>
    <div id="asset-checkavailability-form" class="p-2">
        <?php echo $this->loadTemplate('checkavailability'); ?>
    </div>
<?php endif ?>

<?php if ($this->isAmending): ?>
    <h2>
        <?php echo Text::_('SR_AMENDING_HEADING') ?>
    </h2>
<?php endif ?>

<?php if (!$this->disableOnlineBooking): ?>
    <style>
        /* Fix for overlapping wizard steps */
        .wizard.wizard-default {
            margin-bottom: 3rem !important; /* Push content down */
            height: auto !important;
            overflow: visible !important;
        }
        .wizard .steps {
            display: flex !important; /* Use flexbox for better sizing */
            flex-wrap: nowrap !important; /* Force single line */
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            list-style: none !important;
        }
        .wizard .steps .list-inline-item {
            display: flex !important;
            align-items: center;
            justify-content: center;
            flex: 1 1 0px !important; /* Equal distribution */
            width: auto !important; /* Override explicit widths */
            max-width: none !important;
            height: 50px; /* Fixed height for steps */
            line-height: 1.2;
            padding: 0 5px;
            position: relative;
            background: #f5f5f5; /* Default background */
            margin: 0 !important; /* Remove gaps */
            float: none !important; /* Remove floats */
        }
        .wizard .steps .list-inline-item.active {
            background: #0d6efd; /* Active blue */
            color: #fff;
        }
        .wizard .steps .list-inline-item .badge {
            margin-right: 5px;
        }
        .wizard .steps .list-inline-item .chevron {
            /* Fix chevron if it is causing width issues */
            position: absolute;
            right: -14px;
            top: 0;
            bottom: 0;
            z-index: 10;
            display: block; /* Ensure it is block */
            border-left: 14px solid transparent;
            border-top: 25px solid transparent;
            border-bottom: 25px solid transparent;
        }
         /* Override specific step colors if needed */
         .reservation-tab-room.active { background-color: #008080 !important; border-color: #008080 !important; }
         .reservation-tab-room.active:after { border-left-color: #008080 !important; }

         /* Ensure text doesn't overlap below */
         .availability-search-info {
             margin-top: 1rem !important;
             clear: both;
         }
    </style>
    <div class="wizard wizard-default mb-4 clearfix">
        <ul class="<?php echo SR_UI_GRID_CONTAINER ?> steps list-inline d-flex w-100 m-0 p-0">
            <li data-target="#step1"
                class="list-inline-item active reservation-tab reservation-tab-room flex-fill text-center p-2 position-relative">
                <span class="badge bg-light text-dark">1</span>
                <?php echo Text::_('SR_STEP_ROOM_AND_RATE') ?><span class="chevron"></span>
            </li>
            <li data-target="#step2"
                class="list-inline-item reservation-tab reservation-tab-guestinfo flex-fill text-center p-2 position-relative"><span
                    class="badge bg-secondary">2</span>
                <?php echo Text::_('SR_STEP_GUEST_INFO_AND_PAYMENT') ?><span class="chevron"></span>
            </li>
            <li data-target="#step3"
                class="list-inline-item reservation-tab reservation-tab-confirmation flex-fill text-center p-2 position-relative">
                <span class="badge bg-secondary">3</span>
                <?php echo Text::_('SR_STEP_CONFIRMATION') ?>
            </li>
        </ul>
    </div>
<?php endif ?>

<div class="step-content">
    <div class="step-pane active" id="step1">
        <!-- Tab 1 -->
        <div class="reservation-single-step-holder room room-default">
            <?php
            if ($this->prioritizingRoomTypeId == 0):
                echo $this->loadTemplate('searchinfo');
            endif;
            ?>
            <form enctype="multipart/form-data" id="sr-reservation-form-room" class="sr-reservation-form"
                action="<?php echo Uri::base() ?>index.php?option=com_solidres&task=reservation.process&step=room&format=json"
                method="POST">
                <?php
                $roomTypeCount = count($this->item->roomTypes);
                $roomTypeDisplayData = [
                    'isFresh' => $this->isFresh,
                    'roomTypeCount' => $roomTypeCount,
                ];
                if ($roomTypeCount > 0):

                    echo SRLayoutHelper::render('asset.form_buttons_top', [
                        'isFresh' => $this->isFresh,
                        'isSingular' => $this->isSingular,
                    ]);

                    $count = 1;
                    $prioritizingRoomTypeName = '';
                    $countNotPrioritizing = 0;

                    if ($this->prioritizingRoomTypeId > 0):
                        $countNotPrioritizing = $roomTypeCount - 1;
                    endif;

                    foreach ($this->item->roomTypes as $roomType):
                        $rowCSSClass = [];
                        $rowCSSClass[] = ($count % 2) ? 'even' : 'odd';
                        $rowCSSClass[] = $roomType->featured == 1 ? 'featured' : '';
                        $rowCSSClass[] = 'room_type_row';

                        if (!is_array($roomType->params)):
                            $roomType->params = json_decode($roomType->params, true);
                        endif;

                        $roomTypeDisplayData = array_merge($roomTypeDisplayData, [
                            'Itemid' => $this->itemid,
                            'bookingType' => $this->item->booking_type,
                            'checkinFormatted' => $this->checkinFormatted ?? null,
                            'checkoutFormatted' => $this->checkoutFormatted ?? null,
                            'config' => $this->config,
                            'dayMapping' => $this->dayMapping,
                            'defaultTariffVisibility' => $this->defaultTariffVisibility,
                            'disableOnlineBooking' => $this->disableOnlineBooking,
                            'enableAutoScroll' => $this->enableAutoScroll,
                            'inquiryRoomType' => $inquiryRoomType,
                            'isExclusive' => (bool) ($roomType->params['is_exclusive'] ?? false),
                            'isSingular' => $this->isSingular,
                            'item' => $this->item,
                            'roomType' => $roomType,
                            'selectedRoomTypes' => $this->selectedRoomTypes,
                            'showRemainingRooms' => (bool) ($roomType->params['show_number_remaining_rooms'] ?? true),
                            'showTariffs' => $this->showTariffs,
                            'showTaxIncl' => $this->showTaxIncl,
                            'skipRoomForm' => (bool) ($roomType->params['skip_room_form'] ?? false),
                            'stayLength' => $this->stayLength,
                            'tariffNetOrGross' => $this->tariffNetOrGross,
                        ]);

                        echo $layout->render('roomtype.rateplan_breakdown', $roomTypeDisplayData);

                        $regex = '#<hr(.*)id="system-readmore"(.*)\/>#iU';
                        $intro = $full = '';

                        if (preg_match($regex, $roomType->description)) {
                            [$intro, $full] = preg_split($regex, $roomType->description, 2);

                            $roomType->text = $intro;
                        } else {
                            $roomType->text = $roomType->description;
                        }

                        Factory::getApplication()->triggerEvent('onContentPrepare', ['com_solidres.roomtype', &$roomType, &$roomType->params, 0]);

                        $isPrioritizingRoomType = false;
                        if ($this->prioritizingRoomTypeId == $roomType->id):
                            $isPrioritizingRoomType = true;
                            $rowCSSClass[] = 'prioritizing';
                            $prioritizingRoomTypeName = $roomType->name;
                        endif;

                        if ($this->prioritizingRoomTypeId > 0 && $count == 2):
                            if ($countNotPrioritizing > 1):
                                $msg = 'SR_PRIORITIZING_ROOMTYPE_NOTICE';
                            else:
                                $msg = 'SR_PRIORITIZING_ROOMTYPE_NOTICE_1';
                            endif;

                            echo '<div class="prioritizing-roomtype-notice">' . Text::sprintf($msg, $prioritizingRoomTypeName, $countNotPrioritizing) . '</div>';
                        endif;
                        ?>

                        <div class="<?php echo implode(' ', $rowCSSClass) ?>"
                            id="room_type_row_<?php echo $roomType->id ?>" <?php echo $this->prioritizingRoomTypeId > 0 && !$isPrioritizingRoomType ? 'style="display: none"' : '' ?>>
                            
                        <div class="<?php echo implode(' ', $rowCSSClass) ?> room-card-premium"
                            id="room_card_<?php echo $roomType->id ?>">
                                
                                <div class="row room-card-row g-0 align-items-stretch">
                                    <div class="col-md-6 big-slider-override-container">
                                        <?php
                                        if (!empty($roomType->media)):
                                            echo $layout->render('solidres.carousel', [
                                                'id' => 'carousel' . $roomType->id,
                                                'items' => $roomType->media,
                                                'objectId' => $roomType->id,
                                                'objectName' => $roomType->name,
                                                'linkItem' => true,
                                                'size' => 'original',
                                            ]);
                                        endif;
                                        ?>
                                    </div>

                                    <div class="col-md-6 room-description-container">
                                        <div>
                                            <h4 class="room-title-premium" id="srt_<?php echo $roomType->id ?>">
                                                <?php echo $roomType->name; ?>
                                                <span class="badge bg-light text-dark fw-normal border ms-2" style="font-size: 0.8rem; vertical-align: middle;">
                                                    <i class="fa fa-user me-1"></i>
                                                    <?php echo $roomType->occupancy_max > 0 ? $roomType->occupancy_max : (int) $roomType->occupancy_adult + (int) $roomType->occupancy_child ?>
                                                </span>

                                                <?php if ($roomType->featured == 1): ?>
                                                    <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7rem;">
                                                        <?php echo Text::_('SR_FEATURED_ROOM_TYPE') ?>
                                                    </span>
                                                <?php endif ?>
                                                <?php if ($isPrioritizingRoomType): ?>
                                                    <span class="badge bg-warning ms-1">
                                                        <?php echo Text::_('SR_PRIORITIZING_ROOM_TYPE') ?>
                                                    </span>
                                                <?php endif ?>
                                            </h4>

                                            <div class="roomtype_desc room-desc-premium mb-3">
                                                <?php
                                                echo $layout->render('roomtype.description', [
                                                    'intro' => $intro,
                                                    'full' => $full,
                                                    'roomType' => $roomType,
                                                ]);
                                                ?>
                                            </div>

                                            <?php
                                            if (!empty($roomType->facilities)):
                                                echo '<div class="text-muted small mb-3">';
                                                echo SRLayoutHelper::render('facility.facility', ['facilities' => $roomType->facilities]);
                                                echo '</div>';
                                            endif;
                                            ?>
                                        </div>

                                        <div class="mt-auto border-top pt-3">
                                            <?php
                                            echo SRLayoutHelper::render('roomtype.available_room_msg', $roomTypeDisplayData);
                                            echo SRLayoutHelper::render('roomtype.buttons', $roomTypeDisplayData);
                                            ?>
                                        </div>

                                        <div class="unstyled more_desc" id="more_desc_<?php echo $roomType->id ?>"
                                            style="display: none">
                                            <?php
                                            echo SRLayoutHelper::render('roomtype.customfields', $roomTypeDisplayData);
                                            ?>
                                        </div>
                                    </div>
                                </div>

                            <?php if ($this->config->get('availability_calendar_enable', 1)): ?>
                                <div class="availability-calendar" id="availability-calendar-<?php echo $roomType->id ?>"
                                    style="display: none">
                                </div>
                            <?php endif ?>

                            <?php

                            if (SRPlugin::isEnabled('flexsearch')):
                                $layout->addIncludePath(SRPlugin::getLayoutPath('flexsearch'));
                                echo $layout->render('roomtype.flexsearch', $roomTypeDisplayData);
                            endif;

                            echo $layout->render('asset.rateplans', $roomTypeDisplayData);
                            ?>
                        </div> <!-- End of room-card-premium -->
                    </div> <!-- End of room_type_row -->
                        <?php
                        $count++;
                    endforeach;
                else:
                    ?>
                    <div class="alert alert-warning">
                        <?php
                        if ($this->isFresh) {
                            echo Text::_('SR_NO_ROOM_TYPES');
                        } else {
                            echo Text::sprintf(
                                'SR_NO_ROOM_TYPES_MATCHED_SEARCH_CONDITIONS',
                                $this->checkinFormatted,
                                $this->checkoutFormatted
                            ) . ' <a href="' . $this->resetLink . '"><i class="fa fa-sync"></i> ' . Text::_('SR_SEARCH_RESET') . '</a>';
                        }
                        ?>
                    </div>
                    <?php
                endif;

                echo SRLayoutHelper::render('asset.form_buttons_bottom', $roomTypeDisplayData);

                ?>

                <input type="hidden" name="jform[raid]" value="<?php echo $this->item->id ?>" />
                <input type="hidden" name="jform[next_step]" value="guestinfo" />
                <input type="hidden" name="jform[return]"
                    value="<?php echo base64_encode(Uri::getInstance()->toString()); ?>" />

                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
        </div>
        <!-- /Tab 1 -->

    </div>

    <div class="step-pane" id="step2">
        <!-- Tab 2 -->
        <div class="reservation-single-step-holder guestinfo nodisplay" id="guestinfo"></div>
        <!-- /Tab 2 -->
    </div>

    <div class="step-pane" id="step3">
        <!-- Tab 3 -->
        <div class="reservation-single-step-holder confirmation nodisplay"></div>
        <!-- /Tab 3 -->
    </div>

</div>