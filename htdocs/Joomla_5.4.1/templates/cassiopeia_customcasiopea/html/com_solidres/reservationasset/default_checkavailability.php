<?php
/**
 * Template override for Solidres check availability form
 * Enhanced with Flatpickr date picker, loading states, and modern UX
 *
 * Original: components/com_solidres/views/reservationasset/tmpl/default_checkavailability.php
 * Override version: 3.2.0 (matches core Solidres version)
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

$config                = Factory::getConfig();
$roomsOccupancyOptions = $this->app->getUserState($this->context . '.room_opt', array());

$dateCheckIn = Date::getInstance();
if (empty($this->checkin)) :
	$dateCheckIn->add(new DateInterval('P' . ($this->minDaysBookInAdvance) . 'D'))->setTimezone($this->timezone);
endif;

$dateCheckOut = Date::getInstance();
if (empty($this->checkout)) :
	$dateCheckOut->add(new DateInterval('P' . ($this->minDaysBookInAdvance + $this->minLengthOfStay) . 'D'))->setTimezone($this->timezone);
endif;

$jsDateFormat               = SRUtilities::convertDateFormatPattern($this->dateFormat);
$roomsOccupancyOptionsCount = count($roomsOccupancyOptions);
$maxRooms                   = $this->item->params['max_room_number'] ?? 10;
$maxAdults                  = $this->item->params['max_adult_number'] ?? 10;
$maxChildren                = $this->item->params['max_child_number'] ?? 10;
$hideRoomQuantity           = $this->item->params['hide_room_quantity'] ?? 0;
$mergeAdultChild            = $this->item->params['merge_adult_child'] ?? 0;

$defaultCheckinDate  = '';
$defaultCheckoutDate = '';
if (!empty($this->checkin))
{
	$this->checkinModule  = Date::getInstance($this->checkin, $this->timezone);
	$this->checkoutModule = Date::getInstance($this->checkout, $this->timezone);
	$defaultCheckinDate  = $this->checkinModule->format('Y-m-d', true);
	$defaultCheckoutDate = $this->checkoutModule->format('Y-m-d', true);
}

$minCheckoutDate = $this->minDaysBookInAdvance + $this->minLengthOfStay;

$availableDates = (!empty($this->item->params['available_dates'])) ? $this->item->params['available_dates'] : '[]';

if (empty($defaultCheckinDate) && !empty($this->item->params['available_dates']))
{
	$defaultLOS = $this->item->params['inline_default_los'] ?? $this->minLengthOfStay;
	$defaultCheckinDate = json_decode($availableDates)[0];
	$defaultCheckoutDate = Date::getInstance($defaultCheckinDate, $this->timezone)->add(new DateInterval("P{$defaultLOS}D"))->format('Y-m-d', true);

	if (empty($this->checkin))
	{
		$dateCheckIn = Date::getInstance($defaultCheckinDate, $this->timezone);
	}

	if (empty($this->checkout))
	{
		$dateCheckOut = Date::getInstance($defaultCheckoutDate, $this->timezone);
	}

	$this->getDocument()->addScriptOptions('com_solidres.general', [
		'InlineDefaultLOS' => (int) $defaultLOS,
	]);
}

// --- FLATPICKR ASSETS ---
$wa = $this->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_solidres');
$wa->useStyle('flatpickr')->useScript('flatpickr')->useScript('flatpickr.ru');

// Load Flatpickr adapter (must load after flatpickr.ru and before inline init script)
HTMLHelper::_('script', 'templates/' . $this->app->getTemplate() . '/js/solidres-datepicker-adapter.js', ['version' => 'auto']);

// Load UX enhancements script
HTMLHelper::_('script', 'templates/' . $this->app->getTemplate() . '/js/solidres-ux.js', ['version' => 'auto']);

// Keep jQuery UI datepicker localization for backward compatibility
HTMLHelper::_('script', 'com_solidres/assets/datePicker/localization/jquery.ui.datepicker-' . $this->app->getLanguage()->getTag() . '.js', array('version' => SRVersion::getHashVersion(), 'relative' => true));

// --- FLATPICKR INITIALIZATION (replaces jQuery UI init) ---
$wa->addInlineScript(<<<JS
	Solidres.jQuery(function($) {
		let dpMinCheckoutDate = {$minCheckoutDate};
		let dpDefaultCheckoutDate = '{$defaultCheckoutDate}';
		let dpDefaultCheckinDate = '{$defaultCheckinDate}';
		let dpAvailableDates = {$availableDates};
		Solidres.initDatePickers('sr-checkavailability-form-asset-{$this->item->id}', dpMinCheckoutDate, dpDefaultCheckinDate, dpDefaultCheckoutDate, '', '', false, [], dpAvailableDates);
	});

	document.addEventListener("DOMContentLoaded", function() {
		Solidres.initAgeFields(document.getElementById('sr-checkavailability-form-asset-{$this->item->id}'));
	});
JS);

$enableRoomQuantity = $this->item->params['enable_room_quantity_option'] ?? 0;

$childrenMaxAge = SRUtilities::getChildMaxAge($this->item->params, $this->config);

$adultOptions = $childrenOptions = $childrenAgeOptions = [];
for ($i = 1; $i <= $maxAdults; $i++)
{
	$adultOptions[] = HTMLHelper::_('select.option', $i, $i);
}

for ($i = 0; $i <= $maxChildren; $i++)
{
	$childrenOptions[] = HTMLHelper::_('select.option', $i, $i);
}

$childrenAgeOptions[] = HTMLHelper::_('select.option', '', Text::_('SR_CHILD_AGE_REQUIRED'));
for ($i = 0; $i <= $childrenMaxAge; $i++)
{
	$childrenAgeOptions[] = HTMLHelper::_('select.option', $i, Text::plural('SR_CHILD_AGE_SELECTION', $i));
}

$attributesAdult = [
	'class'    => 'form-select form-select-occupancy',
	'disabled' => 'disabled'
];

$attributesChild = [
	'class'    => 'form-select form-select-occupancy form-select-occupancy-child',
	'disabled' => 'disabled'
];

$attributesAge = [
	'class'    => 'form-select child-age-validation-ordering',
	'disabled' => 'disabled'
];

?>

<?php if (!empty($this->prioritizingRoomTypeId)): ?>
<div style="margin-bottom:0.75rem;">
    <a href="<?php echo Route::_('index.php?option=com_solidres&view=reservationasset&id=' . $this->item->id . ($this->itemid ? '&Itemid=' . $this->itemid : ''), false) ?>"
       style="display:inline-flex; align-items:center; gap:0.35rem; color:#5c7c3b; font-size:0.85rem; font-weight:700; text-decoration:none;">
        <span class="material-symbols-outlined" style="font-size:16px; vertical-align:middle;">arrow_back</span>
        Все варианты размещения
    </a>
</div>
<?php endif ?>

<!-- Skeleton Loader (shown until form is ready) -->
<div id="sr-skeleton-<?php echo $this->item->id ?>" class="sr-skeleton-container" aria-hidden="true">
	<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
		<div class="<?php echo SR_UI_GRID_COL_4 ?>">
			<div class="sr-skeleton sr-skeleton-label"></div>
			<div class="sr-skeleton sr-skeleton-input"></div>
		</div>
		<div class="<?php echo SR_UI_GRID_COL_4 ?>">
			<div class="sr-skeleton sr-skeleton-label"></div>
			<div class="sr-skeleton sr-skeleton-input"></div>
		</div>
		<div class="<?php echo SR_UI_GRID_COL_4 ?>">
			<div class="sr-skeleton sr-skeleton-label"></div>
			<div class="sr-skeleton sr-skeleton-button"></div>
		</div>
	</div>
</div>

<!-- Enhanced Search Form -->
<form id="sr-checkavailability-form-asset-<?php echo $this->item->id ?>"
      action="<?php echo Route::_('index.php' . ($this->enableAutoScroll ? '#book-form' : ''), false) ?>" method="GET"
      class="form-stacked sr-validate sr-search-form-enhanced">

	<!-- Date Presets (Quick Select) -->
	<div class="sr-date-presets" role="group" aria-label="<?php echo Text::_('SR_QUICK_DATE_SELECT') ?>">
		<button type="button" class="sr-date-preset-btn" data-preset="tonight">
			<i class="fa fa-moon"></i> Сегодня
		</button>
		<button type="button" class="sr-date-preset-btn" data-preset="tomorrow">
			<i class="fa fa-sun"></i> Завтра
		</button>
		<button type="button" class="sr-date-preset-btn" data-preset="weekend">
			<i class="fa fa-umbrella-beach"></i> Выходные
		</button>
		<button type="button" class="sr-date-preset-btn" data-preset="nextweek">
			<i class="fa fa-calendar-week"></i> Неделя
		</button>
	</div>

	<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
		<div class="<?php echo $enableRoomQuantity == 0 ? SR_UI_GRID_COL_9 : ($hideRoomQuantity ? SR_UI_GRID_COL_7 : SR_UI_GRID_COL_5) ?>">
			<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
				<div class="<?php echo SR_UI_GRID_COL_6 ?>">
					<?php
					echo SRLayoutHelper::render('field.datepicker', [
						'fieldLabel'            => 'SR_SEARCH_CHECKIN_DATE',
						'fieldName'             => 'checkin',
						'fieldClass'            => 'checkin_module',
						'datePickerInlineClass' => 'checkin_datepicker_inline_module',
						'dateUserFormat'        => !empty($this->checkin) ?
							$this->checkinModule->format($this->dateFormat, true) :
							$dateCheckIn->format($this->dateFormat, true),
						'dateDefaultFormat'     => !empty($this->checkin) ?
							$this->checkinModule->format('Y-m-d', true) :
							$dateCheckIn->format('Y-m-d', true)
					]);
					?>

				</div>
				<div class="<?php echo SR_UI_GRID_COL_6 ?>">
					<?php
					echo SRLayoutHelper::render('field.datepicker', [
						'fieldLabel'            => 'SR_SEARCH_CHECKOUT_DATE',
						'fieldName'             => 'checkout',
						'fieldClass'            => 'checkout_module',
						'datePickerInlineClass' => 'checkout_datepicker_inline_module',
						'dateUserFormat'        => !empty($this->checkout) ?
							$this->checkoutModule->format($this->dateFormat, true) :
							$dateCheckOut->format($this->dateFormat, true),
						'dateDefaultFormat'     => !empty($this->checkout) ?
							$this->checkoutModule->format('Y-m-d', true) :
							$dateCheckOut->format('Y-m-d', true)
					]);
					?>
				</div>
			</div>
		</div>
		<?php if ($enableRoomQuantity) : ?>
			<div class="<?php echo $hideRoomQuantity ? SR_UI_GRID_COL_3 : SR_UI_GRID_COL_5 ?>">
				<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
					<?php if ($hideRoomQuantity == 0) : ?>
						<div class="<?php echo SR_UI_GRID_COL_3 ?>">
							<label><?php echo Text::_('SR_SEARCH_ROOMS') ?></label>
							<select class="form-select room_quantity"
							        name="room_quantity">
								<?php for ($room_num = 1; $room_num <= $maxRooms; $room_num++) : ?>
									<option <?php echo $room_num == $roomsOccupancyOptionsCount ? 'selected' : '' ?>
											value="<?php echo $room_num ?>"><?php echo $room_num ?></option>
								<?php endfor ?>
							</select>
						</div>
					<?php else : ?>
						<input type="hidden" class="room_quantity" name="room_quantity" value="1"/>
					<?php endif ?>
					<div class="<?php echo $hideRoomQuantity ? SR_UI_GRID_COL_12 : SR_UI_GRID_COL_9 ?>">
						<?php for ($room_num = 1; $room_num <= $maxRooms; $room_num++) : ?>
							<div class="room_num_row"
							     id="room_num_row_<?php echo $room_num ?>"
							     style="<?php echo $room_num > 0 ? 'display: none' : '' ?>">
								<div class="<?php echo SR_UI_GRID_CONTAINER ?> child-ages-validation">
									<?php if (!$hideRoomQuantity) : ?>
										<div class="<?php echo SR_UI_GRID_COL_4 ?>">
											<label style="display: block">&nbsp;</label>
											<?php echo Text::_('SR_SEARCH_ROOM') ?> <?php echo $room_num ?>
										</div>
									<?php endif ?>
									<?php if (($hideRoomQuantity && !$mergeAdultChild) || !$hideRoomQuantity) : ?>
										<div class="<?php echo $hideRoomQuantity ? SR_UI_GRID_COL_6 : SR_UI_GRID_COL_4 ?>">
											<label><?php echo Text::_('SR_SEARCH_ROOM_ADULTS') ?></label>
											<?php
											echo HTMLHelper::_('select.genericlist',
												$adultOptions,
												"room_opt[$room_num][adults]",
												$attributesAdult,
												'value',
												'text',
												$roomsOccupancyOptions[$room_num]['adults'] ?? null
											);
											?>
										</div>
										<div class="<?php echo $hideRoomQuantity ? SR_UI_GRID_COL_6 : SR_UI_GRID_COL_4 ?>">
											<label><?php echo Text::_('SR_SEARCH_ROOM_CHILDREN') ?></label>
											<?php
											echo HTMLHelper::_('select.genericlist',
												$childrenOptions,
												"room_opt[$room_num][children]",
												$attributesChild,
												'value',
												'text',
												$roomsOccupancyOptions[$room_num]['children'] ?? null,
												"children_ages_$room_num"
											);
											?>
										</div>
										<?php
										for ($i = 1; $i <= $maxChildren; $i++) :
											$selectedAge = isset($roomsOccupancyOptions[$room_num]['children_ages'][$i]);

											if ($selectedAge) :
												unset($attributesAge['disabled']);
											else:
												$attributesAge['disabled'] = 'disabled';
											endif;

											?>

											<div class="children_ages_<?php echo $room_num ?> mt-3 children_ages_<?php echo $room_num ?>_<?php echo $i ?>"
											     style="<?php echo $selectedAge ? '' : 'display: none' ?>">
												<?php
												echo HTMLHelper::_('select.genericlist',
													$childrenAgeOptions,
													"room_opt[$room_num][children_ages][$i]",
													$attributesAge,
													'value',
													'text',
													$roomsOccupancyOptions[$room_num]['children_ages'][$i] ?? null,
												);
												?>
											</div>

										<?php endfor; ?>
									<?php else : ?>
										<div>
											<label><?php echo Text::_('SR_SEARCH_GUESTS') ?></label>
											<?php
											echo HTMLHelper::_('select.genericlist',
												$adultOptions,
												"room_opt[$room_num][guests]",
												$attributesAdult,
												'value',
												'text',
												$roomsOccupancyOptions[$room_num]['guests'] ?? null
											);
											?>
										</div>
									<?php endif ?>
								</div>
							</div>
						<?php endfor; ?>
					</div>
				</div>
			</div>
		<?php endif ?>
		<div class="<?php echo $enableRoomQuantity == 0 ? SR_UI_GRID_COL_3 : SR_UI_GRID_COL_2 ?>">
			<div class="d-grid">
				<label>&nbsp;</label>
				<button class="btn <?php echo SR_UI_BTN_DEFAULT ?> sr-search-btn" type="submit">
					<span class="sr-search-btn-text"><i class="fa fa-search"></i> <?php echo Text::_('SR_SEARCH') ?></span>
					<span class="sr-search-btn-loading" style="display:none;">
						<span class="sr-btn-spinner"></span>
					</span>
				</button>
			</div>
		</div>
	</div>
	<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
    <input type="hidden" name="option" value="com_solidres"/>
    <input type="hidden" name="view" value="reservationasset"/>
    <input type="hidden" name="Itemid" value="<?php echo $this->itemid ?>"/>
	<?php if (!empty($this->prioritizingRoomTypeId)): ?>
	<input type="hidden" name="room_type_id" value="<?php echo (int)$this->prioritizingRoomTypeId ?>"/>
	<?php endif ?>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<!-- Loading Overlay -->
<div id="sr-loading-overlay" class="sr-loading-overlay" aria-hidden="true">
	<div class="sr-loading-content">
		<div class="sr-spinner"></div>
		<p class="sr-loading-text"><?php echo Text::_('SR_PROCESSING') ?></p>
	</div>
</div>
