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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\DatabaseInterface;

FormHelper::loadFieldClass('text');

class JFormFieldUIDatepicker extends JFormFieldText
{
    protected $type = 'UIDatepicker';

    protected function getInput()
    {
        $app         = Factory::getApplication();
        $config      = $app->getConfig();
        $user        = $app->getIdentity();
        $filter      = $this->getAttribute('filter', 'user_utc');
        $dateFormat  = ComponentHelper::getParams('com_solidres')->get('date_format', 'd-m-Y');
        $jsFormat    = SRUtilities::convertDateFormatPattern($dateFormat);
        $nullDate    = Factory::getContainer()->get(DatabaseInterface::class)->getNullDate();
        $formatValue = '';

        if ($this->value == $nullDate || strtotime($this->value) === false) {
            $this->value = '';
        }

        if ($this->value) {
            switch (strtoupper($filter)) {
                case 'SERVER_UTC':
                    $date = Factory::getDate($this->value, 'UTC');
                    $date->setTimezone(new DateTimeZone($config->get('offset')));
                    $this->value = $date->format('Y-m-d H:i:s', true, false);
                    $formatValue = 1000 * $date->format('U', true, false);
                    break;

                case 'USER_UTC':
                    $date = Factory::getDate($this->value, 'UTC');
                    $date->setTimezone($user->getTimezone());
                    $this->value = $date->format('Y-m-d H:i:s', true, false);
                    $formatValue = 1000 * $date->format('U', true, false);
                    break;

                default:
                    $tz = date_default_timezone_get();
                    date_default_timezone_set('UTC');
                    $this->value = date('Y-m-d H:i:s', strtotime($this->value));
                    date_default_timezone_set($tz);
                    $formatValue = 1000 * Factory::getDate($this->value)->format('U', true, false);
                    break;
            }
        }

        $id      = preg_replace('/[^0-9a-z_\-]/i', '', $this->id);
        $options = [
            'dateFormat' => $jsFormat,
            'altField'   => '#' . $id,
            'altFormat'  => 'yy-mm-dd',
        ];

        $extraOptions = [
            'showButtonPanel' => false,
            'changeMonth'     => false,
            'changeYear'      => false,
            'numberOfMonths'  => ComponentHelper::getParams('com_solidres')->get('datepicker_month_number', 1),
        ];

        foreach ($extraOptions as $name => $value) {
            $option         = $this->getAttribute($name, null);
            $options[$name] = null === $option ? $value : $option;

            if (is_numeric($options[$name])) {
                $options[$name] = (int)$options[$name];
            }
        }

        if ($minDate = $this->getAttribute('minDate', null)) {
            $options['minDate'] = $minDate;
        }

        if ($maxDate = $this->getAttribute('maxDate', null)) {
            $options['maxDate'] = $maxDate;
        }

        $onSelect = trim($this->getAttribute('onSelect', ''));

        SRHtml::_('jquery.ui');
        HTMLHelper::_(
            'script',
            'com_solidres/assets/datePicker/localization/jquery.ui.datepicker-' . $app->getLanguage()->getTag() . '.js',
            ['version' => SRVersion::getHashVersion(), 'relative' => true]
        );
        $app->getDocument()->addScriptDeclaration(
            'Solidres.jQuery(document).ready(function($) {	
			const alias = $("#' . $id . '-alias");
			const dateInput = $("#' . $id . '");
			const onSelect = ' . ($onSelect ?: 'false') . ';
			const options = ' . json_encode($options) . ';
			const formatValue = "' . $formatValue . '";
			
            if (formatValue) {
                alias.val($.datepicker.formatDate("' . $jsFormat . '", new Date(+formatValue)));
                dateInput.val($.datepicker.formatDate("yy-mm-dd", new Date(+formatValue)));
            }
            
			if (typeof onSelect === "function") {
				options.onSelect = onSelect;
			}
			
			alias.trigger("onBeforeInitDatePicker", options);
			alias.datepicker(options);
			alias.on("change", function() {
			
				if (this.value == "") {
					dateInput.val("");
				}
				
				dateInput.trigger("change");
			});		
			
			$("#' . $id . '-btn").on("click", function() {
				if (!alias.datepicker("widget").is(":visible")) {
					alias.datepicker("show");
				}
			});
		});'
        );

        $required              = $this->getAttribute('required');
        $onChange              = empty($this->onchange) ? '' : ' onchange="' . $this->onchange . '"';
        $required              = empty($required) || $required == '0' || $required == '1' ? '' : ' required="required"';
        $hint                  = $this->hint ? ' placeholder="' . Text::_($this->hint) . '"' : '';
        $inputClass            = 'form-control';
        $inputAppendClass      = SR_UI_INPUT_APPEND;
        $inputAppendAddonClass = SR_UI_INPUT_ADDON;

        return <<<HTML
			<div class="sr-field-ui-datepicker-container {$inputAppendClass}">
				<input type="text" id="{$id}-alias" autocomplete="off" readonly{$hint}{$required} class="{$inputClass}"/>
				<span class="{$inputAppendAddonClass}" id="{$id}-btn">
					<i class="fa fa-calendar"></i>	
				</span>					
			</div>
			<input type="hidden" id="{$id}" name="{$this->name}" {$onChange}/>
HTML;
    }
}
