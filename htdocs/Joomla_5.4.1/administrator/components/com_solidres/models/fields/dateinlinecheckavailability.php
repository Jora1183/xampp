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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

class JFormFieldDateInlineCheckAvailability extends JFormField
{
	protected $type = 'DateInlineCheckAvailability';

	protected function loadDocument()
	{
		static $load = false;

		$solidresConfig = ComponentHelper::getParams('com_solidres');

		if (!$load)
		{
			SRHtml::_('jquery.ui');
			Factory::getApplication()->getDocument()
				->addStyleDeclaration('
				.inline-available-date .ui-datepicker .sr-state-selected.ui-state-active {
					background: #999;
					color: white;
					border: none
				}
				
				.inline-available-date .ui-datepicker .ui-datepicker-current-day .ui-state-active {
					background: inherit;
					color: inherit;
				}
				
				.inline-available-date .ui-datepicker .ui-datepicker-current-day.ui-state-active .ui-state-active {
					background: inherit;
				}
				')
				->addScriptDeclaration('
				jQuery(document).ready(function ($) {
					var activeDates = $.parseJSON($("#' . $this->id . '").val() ? $("#' . $this->id . '").val() : "{}");
					var dateFormat = "' . SRUtilities::convertDateFormatPattern($solidresConfig->get('date_format', 'd-m-Y')) . '";
						
					if($.type(activeDates) !== "array"){
						activeDates = [];
					}
					
					var setActiveDates = function (dateText, inst, type) {
						var index = $.inArray(dateText, activeDates);						
						
						switch (type)
						{
							case "toggle":
								if (index === -1) {
									activeDates.push(dateText);
								} else {	
									activeDates.splice(index, 1);
									//console.log(dateText);
									inst.dpDiv.find(".ui-datepicker-current-day").removeClass("sr-state-selected");
									//console.log(inst.dpDiv.find(".ui-datepicker-current-day a").text());
									//console.log(inst.dpDiv.find(".ui-datepicker-current-day a").attr("class"));
								}
								
								break;
								
							case "check":
								if (index === -1) {
									activeDates.push(dateText);
								}
								
								break;
								
							case "uncheck":
								if (index !== -1) {
									activeDates.splice(index, 1);
								}
								
								break;
						}
							
						$("#' . $this->id . '").val(JSON.stringify(activeDates));
					};

					function getDate(element) {
					    try {
					        return $.datepicker.parseDate(dateFormat, element.value);
					    } catch(error) {
					        return null;
					    }
					}
					
					$("#datepicker-' . $this->id . '").datepicker({
						numberOfMonths: [3, 4],
						showButtonPanel: true,
						dateFormat: "yy-mm-dd",
						minDate: new Date(),
						firstDay: ' . ($solidresConfig->get('week_start_day', 1)) . ',
						onSelect: function (dateText, inst) {							
							setActiveDates(dateText, inst, "toggle");
						},
						
						beforeShowDay: function (date) {
							var
								day = new String(date.getDate()),
								month = new String(date.getMonth() + 1),
								year = date.getFullYear(),
								result = [true, "sr-date-cell"],								
								dateText;
							
							if (day.length == 1) {
								day = "0" + day;
							}
							
							if (month.length == 1) {
								month = "0" + month;
							}
							
							dateText = year + "-" + month + "-" + day;
							result[1] += " sr-date-" + dateText;
							
							if ($.inArray(dateText, activeDates) !== -1) {
								//result[1] += " ui-state-active sr-state sr-state-active";
								result[1] += " ui-state-active sr-state-selected";
							}							
							
							return result;
						}
					});	
					
					$(document).on("click", "#datepicker-' . $this->id . ' .ui-datepicker-header", function(e) {
						const isPrevNext = e.target?.classList?.contains("ui-corner-left") || e.target?.classList?.contains("ui-corner-right");

						if (isPrevNext && e.target.querySelector(".ui-state-disabled")) {
							return;
						}					
							
						$(this).siblings(".ui-datepicker-calendar").find("tr>td[data-handler]").each(function(){
							var el = $(this),
								day = new String(el.find(">a").text()),
								month = new String(el.data("month") + 1),
								year = new String(el.data("year"));
							if (day.length == 1) {
								day = "0" + day;
							}
							if (month.length == 1) {
								month = "0" + month;
							}
							var date = year + "-" + month + "-" + day,
								index = $.inArray(date, activeDates);
							if (index === -1) {
								activeDates.push(date);
							}else{
								activeDates.splice(index, 1);
							}							
						});
						
						$("#' . $this->id . '").val(JSON.stringify(activeDates));
						$("#datepicker-' . $this->id . '").datepicker("refresh");						
					});
					
					var dateResize = function(){		
						var datepicker = $("#datepicker-' . $this->id . '"),			
							containerWidth = $("#sr_panel_right").prop("clientWidth"),
							numberOfMonths = [3, 4];
						if(containerWidth < 1040){
							if(containerWidth >= 780){
								numberOfMonths = [3, 3];
							}else if(containerWidth >= 500){
								numberOfMonths = [2, 2];
							}else{
								numberOfMonths = [2, 1];
							}
						}																		
						datepicker.datepicker("option", "numberOfMonths", numberOfMonths);
					};
					
					$(window).on("resize", dateResize);
					
				});
			');
			$load = true;
		}
	}

	protected function getInput()
	{
		$this->loadDocument();
		$displayData = array(
			'field'   => $this,
		);

		return SRLayoutHelper::render('solidres.form.field.datepicker', $displayData);
	}
}