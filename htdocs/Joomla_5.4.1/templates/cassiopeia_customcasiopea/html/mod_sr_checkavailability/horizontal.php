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
 * /templates/TEMPLATENAME/html/mod_sr_checkavailability/horizontal.php
 *
 * However, occasionally we will need to update template/layout related files and it is the template developers'
 * responsibility to update the overridden files (if any) to maintain full compatibility with Solidres.
 *
 * We do not provide support if any of the overridden files are out of date and are not compatible with Solidres.
 *
 * @version 3.1.1
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

// Check if we have a property/asset
if (empty($tableAsset) || empty($tableAsset->id)) {
    echo '<div class="alert alert-warning p-4 m-4">
            <h4><i class="fas fa-exclamation-triangle"></i> Configuration Required</h4>
            <p><strong>No default property found.</strong></p>
            <p>To fix this, go to:</p>
            <ol>
                <li><strong>Components → Solidres → Reservation Assets</strong></li>
                <li>Click on one of your published properties</li>
                <li>Set <strong>"Default"</strong> to <strong>Yes</strong></li>
                <li>Click <strong>Save & Close</strong></li>
            </ol>
            <p>Then refresh this page.</p>
          </div>';
    return;
}
?>

<!-- Hero Section Wrapper -->
<section class="hero-section">
    <div class="container">
        <div class="text-center mb-4">
            <h1 class="hero-title"><?php echo Text::_('SR_FIND_YOUR_PERFECT_STAY'); ?></h1>
            <p class="hero-subtitle"><?php echo Text::_('SR_LUXURY_ACCOMMODATION_SUBTITLE'); ?></p>
        </div>

        <div class="sr-search-horizontal">
            <form id="sr-checkavailability-form-<?php echo $module->id ?>"
                  action="<?php echo Route::_('index.php?option=com_solidres&view=reservationasset&id='.$tableAsset->id.'&Itemid='.$params->get('target_itemid'), false)?>"
                  method="GET" class="sr-validate solidres-module-checkavailability"
                  onsubmit="this.action = ((Joomla.getOptions('com_solidres.general').AutoScroll == 1) ? this.action + (this.room_type_id != undefined && this.room_type_id.value != '' ? '#srt_' + this.room_type_id.value : '#book-form') : this.action)">
	<div class="row g-3 align-items-end">
		<!-- Check In Date -->
		<div class="col-md-3">
			<label><i class="far fa-calendar-alt me-1"></i> <?php echo Text::_('SR_SEARCH_CHECKIN_DATE'); ?></label>
			<?php
			echo SRLayoutHelper::render('field.datepicker', [
				'fieldLabel'            => '',
				'fieldName'             => 'checkin',
				'fieldClass'            => 'checkin_module form-control form-control-lg',
				'datePickerInlineClass' => 'checkin_datepicker_inline_module',
				'dateUserFormat'        => isset($checkin) ?
					$checkinModule->format($dateFormat, true) :
					$dateCheckInMin->format($dateFormat, true),
				'dateDefaultFormat'     => isset($checkin) ?
					$checkinModule->format('Y-m-d', true) :
					$dateCheckInMin->format('Y-m-d', true)
			]);
			?>
		</div>
		
		<!-- Check Out Date -->
		<div class="col-md-3">
			<label><i class="far fa-calendar-alt me-1"></i> <?php echo Text::_('SR_SEARCH_CHECKOUT_DATE'); ?></label>
			<?php
			echo SRLayoutHelper::render('field.datepicker', [
				'fieldLabel'            => '',
				'fieldName'             => 'checkout',
				'fieldClass'            => 'checkout_module form-control form-control-lg',
				'datePickerInlineClass' => 'checkout_datepicker_inline_module',
				'dateUserFormat'        => isset($checkout) ?
					$checkoutModule->format($dateFormat, true) :
					$dateCheckOut->format($dateFormat, true),
				'dateDefaultFormat'     => isset($checkout) ?
					$checkoutModule->format('Y-m-d', true) :
					$dateCheckOut->format('Y-m-d', true)
			]);
			?>
		</div>
		
		<!-- Guests / Room Options -->
		<div class="col-md-4">
			<label><i class="fas fa-user-friends me-1"></i> <?php echo Text::_('SR_SEARCH_GUESTS'); ?></label>
			<?php if ($enableRoomQuantity && !$hideRoomQuantity) : ?>
				<select class="form-select form-select-lg room_quantity" name="room_quantity">
					<?php for ($room_num = 1; $room_num <= $maxRooms; $room_num ++) : ?>
						<option <?php echo $room_num == $roomsOccupancyOptionsCount ? 'selected' : '' ?> value="<?php echo $room_num  ?>"><?php echo $room_num  ?> <?php echo Text::_('SR_SEARCH_ROOMS'); ?></option>
					<?php endfor ?>
				</select>
			<?php elseif ($enableRoomTypeDropdown && !empty($roomTypes)) : ?>
				<select class="form-select form-select-lg" name="room_type_id">
					<option value=""><?php echo Text::_('SR_SELECT_ROOM_TYPE'); ?></option>
					<?php
					foreach ($roomTypes as $roomType) :
						$selected = $prioritizingRoomTypeId == $roomType->id ? 'selected' : '';
						echo '<option value="' . $roomType->id . '" '.$selected.'>' . $roomType->name . '</option>';
					endforeach;
					?>
				</select>
			<?php else : ?>
				<select class="form-select form-select-lg">
					<option>2 <?php echo Text::_('SR_SEARCH_ROOM_ADULTS'); ?>, 0 <?php echo Text::_('SR_SEARCH_ROOM_CHILDREN'); ?></option>
				</select>
			<?php endif; ?>
		</div>
		
		<!-- Submit Button -->
		<div class="col-md-2">
			<button class="btn btn-primary btn-lg w-100" type="submit">
				<i class="fa fa-search"></i> <?php echo Text::_('SR_SEARCH'); ?>
			</button>
		</div>
	</div>
	
	<!-- Hidden Fields for Room Occupancy -->
	<?php if ($enableRoomQuantity) : ?>
		<div class="d-none">
			<?php if ($hideRoomQuantity) : ?>
				<input type="hidden" class="room_quantity" name="room_quantity" value="1" />
			<?php endif ?>
			<?php for ($room_num = 1; $room_num <= $maxRooms; $room_num ++) : ?>
				<div class="room_num_row" id="room_num_row_<?php echo $room_num ?>">
					<?php if (($hideRoomQuantity && !$mergeAdultChild) || !$hideRoomQuantity) : ?>
						<?php
						echo HTMLHelper::_('select.genericlist',
							$adultOptions,
							"room_opt[$room_num][adults]",
							['class' => 'form-select'],
							'value',
							'text',
							$roomsOccupancyOptions[$room_num]['adults'] ?? null
						);
						echo HTMLHelper::_('select.genericlist',
							$childrenOptions,
							"room_opt[$room_num][children]",
							['class' => 'form-select'],
							'value',
							'text',
							$roomsOccupancyOptions[$room_num]['children'] ?? null,
							"children_ages_$room_num"
						);
						for ($i = 1; $i <= $maxChildren; $i++) :
							$attributesAge = [];
							$selectedAge = isset($roomsOccupancyOptions[$room_num]['children_ages'][$i]);
							if (!$selectedAge) :
								$attributesAge['disabled'] = 'disabled';
							endif;
							$attributesAge['class'] = 'form-select child-age-validation-ordering';
							echo HTMLHelper::_('select.genericlist',
								$childrenAgeOptions,
								"room_opt[$room_num][children_ages][$i]",
								$attributesAge,
								'value',
								'text',
								$roomsOccupancyOptions[$room_num]['children_ages'][$i] ?? null,
							);
						endfor;
						?>
					<?php else : ?>
						<?php
						echo HTMLHelper::_('select.genericlist',
							$adultOptions,
							"room_opt[$room_num][guests]",
							['class' => 'form-select'],
							'value',
							'text',
							$roomsOccupancyOptions[$room_num]['guests'] ?? null
						);
						?>
					<?php endif ?>
				</div>
			<?php endfor; ?>
		</div>
	<?php endif ?>

	<input name="id" value="<?php echo $tableAsset->id ?>" type="hidden" />
	<input type="hidden" name="option" value="com_solidres" />
	<input type="hidden" name="view" value="reservationasset" />
	<input type="hidden" name="Itemid" value="<?php echo $params->get('target_itemid') ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
        </div>
    </div>
</section>