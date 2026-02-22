<?php
/**
 * Hero Section Simplified Layout
 * Shows only: Check-in, Check-out, Number of Guests
 * 
 * Template override for mod_sr_checkavailability
 * Simplified version for hero section
 */

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;
?>

<form id="sr-checkavailability-form-<?php echo $module->id ?>"
      action="<?php echo Route::_('index.php?option=com_solidres&view=reservationasset&id=' . $tableAsset->id . '&Itemid=' . $params->get('target_itemid'), false) ?>"
      method="GET" class="form-stacked sr-validate solidres-module-checkavailability <?php echo SR_UI ?> hero-simplified-form"
      onsubmit="this.action = ((Joomla.getOptions('com_solidres.general').AutoScroll == 1) ? this.action + (this.room_type_id != undefined && this.room_type_id.value != '' ? '#srt_' + this.room_type_id.value : '#book-form') : this.action)">
	
	<div class="<?php echo SR_UI_GRID_CONTAINER ?> g-3">
		<!-- Check-in Date -->
		<div class="<?php echo SR_UI_GRID_COL_4 ?>">
			<?php
			echo SRLayoutHelper::render('field.datepicker', [
				'fieldLabel'            => 'SR_SEARCH_CHECKIN_DATE',
				'fieldName'             => 'checkin',
				'fieldClass'            => 'checkin_module',
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

		<!-- Check-out Date -->
		<div class="<?php echo SR_UI_GRID_COL_4 ?>">
			<?php
			echo SRLayoutHelper::render('field.datepicker', [
				'fieldLabel'            => 'SR_SEARCH_CHECKOUT_DATE',
				'fieldName'             => 'checkout',
				'fieldClass'            => 'checkout_module',
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

		<!-- Number of Guests -->
		<div class="<?php echo SR_UI_GRID_COL_4 ?>">
			<label><?php echo Text::_('SR_SEARCH_GUESTS') ?></label>
			<?php
			// Create guest options (1-20 guests)
			$guestOptions = [];
			for ($i = 1; $i <= 20; $i++) {
				$guestOptions[] = HTMLHelper::_('select.option', $i, $i);
			}
			
			// Get saved value from session
			$savedGuests = $roomsOccupancyOptions[1]['guests'] ?? 2;
			
			echo HTMLHelper::_('select.genericlist',
				$guestOptions,
				"room_opt[1][guests]",
				['class' => 'form-select'],
				'value',
				'text',
				$savedGuests
			);
			?>
		</div>
	</div>

	<!-- Search Button -->
	<div class="mt-3">
		<div class="d-grid">
			<button class="btn btn-primary btn-lg" type="submit">
				<i class="fa fa-search"></i> <?php echo Text::_('SR_SEARCH') ?>
			</button>
		</div>
	</div>

	<!-- Hidden fields -->
	<input type="hidden" name="room_quantity" value="1" />
	<input name="id" value="<?php echo $tableAsset->id ?>" type="hidden"/>
	<input type="hidden" name="option" value="com_solidres"/>
	<input type="hidden" name="view" value="reservationasset"/>
	<input type="hidden" name="Itemid" value="<?php echo $params->get('target_itemid') ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
