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
 * /templates/TEMPLATENAME/html/layouts/com_solidres/reservation/general_details.php
 *
 * However, occasionally we will need to update template/layout related files and it is the template developers'
 * responsibility to update the overridden files (if any) to maintain full compatibility with Solidres.
 *
 * We do not provide support if any of the overridden files are out of date and are not compatible with Solidres.
 *
 * @version 3.2.1
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

// Load custom CSS
$doc = Factory::getDocument();
$doc->addStyleSheet(Uri::root() . 'templates/cassiopeia_customcasiopea/css/solidres-custom.css');

extract($displayData);

$fields = [];

if (SRPlugin::isEnabled('customfield'))
{
	$app        = Factory::getApplication();
	$scope      = $app->scope;
	$app->scope = 'com_solidres.manage';
	$fields     = SRCustomFieldHelper::findFields(['context' => 'com_solidres.customer'], [$cid], $reservation->customer_language ?? null);
	$app->scope = $scope;
}

if (count($fields)):
	$fieldsValues = SRCustomFieldHelper::getValues(['context' => 'com_solidres.customer.' . $reservation->id]);
	SRCustomFieldHelper::setFieldDataValues($fieldsValues);
	$customFieldLength = count($fields);
	$partialNumber     = ceil($customFieldLength / 2);
	$rootUrl           = Uri::root(true);
	$token             = Session::getFormToken();
	$renderValue       = function ($field) use ($rootUrl, $token) {
		$value = SRCustomFieldHelper::displayFieldValue($field->field_name);

		if ($field->type == 'file')
		{
			$file     = base64_encode($value);
			$fileName = basename($value);

			if (strpos($fileName, '_') !== false)
			{
				$parts    = explode('_', $fileName, 2);
				$fileName = $parts[1];
			}

			$value = '<a href="' . Route::_('index.php?option=com_solidres&task=customfield.downloadFile&file=' . $file . '&' . $token . '=1', false) . '" style="max-width: 180px" target="_blank">' . $fileName . '</a>';
		}

		return $value;
	};

	?>
	<!-- Modern Checkout Customer Details Layout -->
	<div class="checkout-form">
		<h4 class="mb-4"><?php echo Text::_('SR_CUSTOMER_INFORMATION'); ?></h4>
		<div class="row g-3">
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_CUSTOMER_TITLE") ?></label>
					<div class="info-value"><?php echo $reservation->customer_title ?></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_FIRSTNAME") ?></label>
					<div class="info-value"><?php echo $reservation->customer_firstname ?></div>
				</div>
			</div>
			<?php if (!empty($reservation->customer_middlename)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_MIDDLENAME") ?></label>
					<div class="info-value"><?php echo $reservation->customer_middlename ?></div>
				</div>
			</div>
			<?php endif; ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_LASTNAME") ?></label>
					<div class="info-value"><?php echo $reservation->customer_lastname ?></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("JGLOBAL_EMAIL") ?></label>
					<?php if ($mail = $reservation->customer_email): ?>
						<div class="info-value"><a href="mailto:<?php echo $mail ?>"><?php echo $mail ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_PHONE") ?></label>
					<?php if ($phone = $reservation->customer_phonenumber): ?>
						<div class="info-value"><a href="tel:<?php echo $phone ?>"><?php echo $phone ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<?php if (!empty($reservation->customer_mobilephone)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_MOBILEPHONE") ?></label>
					<?php if ($phone = $reservation->customer_mobilephone): ?>
						<div class="info-value"><a href="tel:<?php echo $phone ?>"><?php echo $phone ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_company)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_COMPANY") ?></label>
					<div class="info-value"><?php echo $reservation->customer_company ?></div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		
		<?php if (!empty($reservation->customer_address1) || !empty($reservation->customer_city)) : ?>
		<h5 class="mt-4 mb-3"><?php echo Text::_('SR_ADDRESS_INFORMATION'); ?></h5>
		<div class="row g-3">
			<?php if (!empty($reservation->customer_address1)) : ?>
			<div class="col-12">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_ADDRESS_1") ?></label>
					<div class="info-value"><?php echo $reservation->customer_address1 ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_address2)) : ?>
			<div class="col-12">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_ADDRESS_2") ?></label>
					<div class="info-value"><?php echo $reservation->customer_address2 ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_city)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_CITY") ?></label>
					<div class="info-value"><?php echo $reservation->customer_city ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_zipcode)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_ZIP") ?></label>
					<div class="info-value"><?php echo $reservation->customer_zipcode ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_country_name)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_FIELD_COUNTRY_LABEL") ?></label>
					<div class="info-value"><?php echo $reservation->customer_country_name ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_geostate_name)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_FIELD_GEO_STATE_LABEL") ?></label>
					<div class="info-value"><?php echo $reservation->customer_geostate_name ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_vat_number)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_VAT_NUMBER") ?></label>
					<div class="info-value"><?php echo $reservation->customer_vat_number ?></div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<?php if (!empty($reservation->note)) : ?>
		<h5 class="mt-4 mb-3"><?php echo Text::_('SR_SPECIAL_REQUESTS'); ?></h5>
		<div class="info-item">
			<div class="info-value"><?php echo $reservation->note ?></div>
		</div>
		<?php endif; ?>
	</div>
<?php else: ?>
	<!-- Standard Layout (Without Custom Fields) -->
	<div class="checkout-form">
		<h4 class="mb-4"><?php echo Text::_('SR_CUSTOMER_INFORMATION'); ?></h4>
		<div class="row g-3">
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_CUSTOMER_TITLE") ?></label>
					<div class="info-value"><?php echo $reservation->customer_title ?></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_FIRSTNAME") ?></label>
					<div class="info-value"><?php echo $reservation->customer_firstname ?></div>
				</div>
			</div>
			<?php if (!empty($reservation->customer_middlename)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_MIDDLENAME") ?></label>
					<div class="info-value"><?php echo $reservation->customer_middlename ?></div>
				</div>
			</div>
			<?php endif; ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_LASTNAME") ?></label>
					<div class="info-value"><?php echo $reservation->customer_lastname ?></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("JGLOBAL_EMAIL") ?></label>
					<?php if ($mail = $reservation->customer_email): ?>
						<div class="info-value"><a href="mailto:<?php echo $mail ?>"><?php echo $mail ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_PHONE") ?></label>
					<?php if ($phone = $reservation->customer_phonenumber): ?>
						<div class="info-value"><a href="tel:<?php echo $phone ?>"><?php echo $phone ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<?php if (!empty($reservation->customer_mobilephone)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_MOBILEPHONE") ?></label>
					<?php if ($phone = $reservation->customer_mobilephone): ?>
						<div class="info-value"><a href="tel:<?php echo $phone ?>"><?php echo $phone ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_company)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_COMPANY") ?></label>
					<div class="info-value"><?php echo $reservation->customer_company ?></div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		
		<h5 class="mt-4 mb-3"><?php echo Text::_('SR_ADDRESS_INFORMATION'); ?></h5>
		<div class="row g-3">
			<?php if (!empty($reservation->customer_address1)) : ?>
			<div class="col-12">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_ADDRESS_1") ?></label>
					<div class="info-value"><?php echo $reservation->customer_address1 ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_address2)) : ?>
			<div class="col-12">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_ADDRESS_2") ?></label>
					<div class="info-value"><?php echo $reservation->customer_address2 ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_city)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_CITY") ?></label>
					<div class="info-value"><?php echo $reservation->customer_city ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_zipcode)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_ZIP") ?></label>
					<div class="info-value"><?php echo $reservation->customer_zipcode ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_country_name)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_FIELD_COUNTRY_LABEL") ?></label>
					<div class="info-value"><?php echo $reservation->customer_country_name ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_geostate_name)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_FIELD_GEO_STATE_LABEL") ?></label>
					<div class="info-value"><?php echo $reservation->customer_geostate_name ?></div>
				</div>
			</div>
			<?php endif; ?>
			<?php if (!empty($reservation->customer_vat_number)) : ?>
			<div class="col-md-6">
				<div class="info-item">
					<label class="form-label"><?php echo Text::_("SR_VAT_NUMBER") ?></label>
					<div class="info-value"><?php echo $reservation->customer_vat_number ?></div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		
		<?php if (!empty($reservation->note)) : ?>
		<h5 class="mt-4 mb-3"><?php echo Text::_('SR_NOTES'); ?></h5>
		<div class="info-item">
			<div class="info-value"><?php echo $reservation->note ?></div>
		</div>
		<?php endif; ?>
	</div>
<?php endif; ?>