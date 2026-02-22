/**
 * Solidres Flatpickr Adapter
 * Replaces jQuery UI Datepicker with Flatpickr for modern UX
 * Maintains full compatibility with Solidres.initDatePickers() interface
 */
(function () {
    'use strict';

    // Store original function as fallback
    const originalInitDatePickers = Solidres.initDatePickers;

    // Convert Solidres/jQuery UI date format to Flatpickr format
    function convertDateFormat(jqueryFormat) {
        // jQuery UI: dd-mm-yy => Flatpickr: d-m-Y
        return jqueryFormat
            .replace('dd', 'd')
            .replace('mm', 'm')
            .replace('yy', 'Y');
    }

    // Format a date as Y-m-d for hidden input
    function formatYmd(date) {
        if (!date) return '';
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    // Calculate future date from today
    function futureDate(days) {
        const d = new Date();
        d.setDate(d.getDate() + days);
        d.setHours(0, 0, 0, 0);
        return d;
    }

    // Check if a day is valid for check-in
    function isValidCheckInDate(dayOfWeek, enabledCheckinDays) {
        if (!enabledCheckinDays || enabledCheckinDays.length === 0) return true;
        return enabledCheckinDays.indexOf(dayOfWeek) > -1;
    }

    Solidres.initDatePickers = function (wrapperId, dpMinCheckoutDate, dpDefaultCheckinDate, dpDefaultCheckoutDate, dpMinCheckinDate, dpMaxCheckinDate, enableUnoccupiedPricing, enabledCheckinDays, availableDates) {

        // Default parameters
        dpMinCheckinDate = dpMinCheckinDate || '';
        dpMaxCheckinDate = dpMaxCheckinDate || '';
        enableUnoccupiedPricing = enableUnoccupiedPricing || false;
        enabledCheckinDays = enabledCheckinDays || [];
        availableDates = availableDates || [];

        const $ = Solidres.jQuery;
        const generalOptions = Joomla.getOptions('com_solidres.general');
        const minLengthOfStay = generalOptions.hasOwnProperty('InlineDefaultLOS') ? generalOptions.InlineDefaultLOS : generalOptions.MinLengthOfStay;
        const minDaysBookInAdvance = generalOptions.MinDaysBookInAdvance;
        const maxDaysBookInAdvance = generalOptions.MaxDaysBookInAdvance;
        const dateFormat = generalOptions.DateFormatJS;
        const fpDateFormat = convertDateFormat(dateFormat);
        const moduleInstance = document.getElementById(wrapperId);

        if (!moduleInstance) return;

        // Resolve min/max checkin dates
        if (typeof dpMinCheckinDate === 'string' && dpMinCheckinDate.length === 0) {
            dpMinCheckinDate = minDaysBookInAdvance;
        }
        if (typeof dpMaxCheckinDate === 'string' && dpMaxCheckinDate.length === 0) {
            dpMaxCheckinDate = maxDaysBookInAdvance;
        }

        // Compute actual min/max Date objects for checkin
        let checkinMinDate, checkinMaxDate;

        if (typeof dpMinCheckinDate === 'number') {
            checkinMinDate = futureDate(dpMinCheckinDate);
        } else if (typeof dpMinCheckinDate === 'string' && dpMinCheckinDate.length > 0) {
            checkinMinDate = new Date(Date.parse(dpMinCheckinDate));
        } else {
            checkinMinDate = 'today';
        }

        if (typeof dpMaxCheckinDate === 'number' && dpMaxCheckinDate > 0) {
            checkinMaxDate = futureDate(maxDaysBookInAdvance);
        } else if (typeof dpMaxCheckinDate === 'string' && dpMaxCheckinDate.length > 0) {
            checkinMaxDate = new Date(Date.parse(dpMaxCheckinDate));
        } else {
            checkinMaxDate = null;
        }

        // Compute initial checkout min date
        let checkoutMinDate;
        if (typeof dpMinCheckoutDate === 'number') {
            checkoutMinDate = futureDate(dpMinCheckoutDate);
        } else if (typeof dpMinCheckoutDate === 'string' && dpMinCheckoutDate.length > 0) {
            checkoutMinDate = new Date(Date.parse(dpMinCheckoutDate));
        } else {
            checkoutMinDate = futureDate(minLengthOfStay);
        }

        // Parse default dates
        let defaultCheckinDate = null;
        let defaultCheckoutDate = null;

        if (dpDefaultCheckinDate && dpDefaultCheckinDate.length > 0) {
            defaultCheckinDate = new Date(Date.parse(dpDefaultCheckinDate));
        }
        if (dpDefaultCheckoutDate && dpDefaultCheckoutDate.length > 0) {
            defaultCheckoutDate = new Date(Date.parse(dpDefaultCheckoutDate));
        }

        // Flatpickr locale config
        const localeConfig = {
            locale: (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.ru) ? flatpickr.l10ns.ru : 'default',
            firstDayOfWeek: generalOptions.WeekStartDay || 1
        };

        // Get DOM elements
        const checkinInput = moduleInstance.querySelector('.checkin_module');
        const checkoutInput = moduleInstance.querySelector('.checkout_module');
        const checkinInline = moduleInstance.querySelector('.checkin_datepicker_inline_module');
        const checkoutInline = moduleInstance.querySelector('.checkout_datepicker_inline_module');
        const checkinHidden = moduleInstance.querySelector('input[name="checkin"]');
        const checkoutHidden = moduleInstance.querySelector('input[name="checkout"]');

        if (!checkinInput || !checkoutInput) return;

        // Hide original inline containers - Flatpickr uses its own dropdown
        if (checkinInline) checkinInline.style.display = 'none';
        if (checkoutInline) checkoutInline.style.display = 'none';

        // Remove readonly so Flatpickr can bind
        checkinInput.removeAttribute('readonly');
        checkoutInput.removeAttribute('readonly');

        // Shared Flatpickr options
        const showMonths = generalOptions.DatePickerMonthNum || 1;

        // --- CHECKOUT PICKER ---
        const checkoutPicker = flatpickr(checkoutInput, {
            locale: localeConfig.locale,
            dateFormat: fpDateFormat,
            minDate: checkoutMinDate,
            defaultDate: defaultCheckoutDate,
            showMonths: showMonths,
            disableMobile: false,
            animate: true,
            static: false,
            appendTo: undefined,
            clickOpens: true,
            onReady: function (selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('sr-flatpickr', 'sr-flatpickr-checkout');
            },
            onChange: function (selectedDates) {
                if (selectedDates.length > 0) {
                    checkoutHidden.value = formatYmd(selectedDates[0]);

                    if (generalOptions.EnableUnoccupiedPricing === 1 && enableUnoccupiedPricing) {
                        getCheckInOutDates($(moduleInstance));
                    }
                }
            }
        });

        // --- CHECKIN PICKER ---
        // Build enable/disable config for checkin days
        let checkinEnable = undefined;
        let checkinDisable = undefined;

        if (availableDates && availableDates.length > 0) {
            // Only allow specific available dates
            checkinEnable = availableDates.map(function (d) { return d; });
        } else if (enabledCheckinDays && enabledCheckinDays.length > 0) {
            // Only allow specific days of week
            checkinEnable = [
                function (date) {
                    return isValidCheckInDate(date.getDay(), enabledCheckinDays);
                }
            ];
        }

        const checkinPicker = flatpickr(checkinInput, {
            locale: localeConfig.locale,
            dateFormat: fpDateFormat,
            minDate: checkinMinDate,
            maxDate: checkinMaxDate,
            defaultDate: defaultCheckinDate,
            showMonths: showMonths,
            enable: checkinEnable,
            disableMobile: false,
            animate: true,
            static: false,
            appendTo: undefined,
            clickOpens: true,
            onReady: function (selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('sr-flatpickr', 'sr-flatpickr-checkin');
            },
            onDayCreate: function (dObj, dStr, fp, dayElem) {
                const dateStr = formatYmd(dayElem.dateObj);

                if (availableDates.length > 0 && availableDates.indexOf(dateStr) > -1) {
                    dayElem.classList.add('bookable');
                } else if (availableDates.length === 0 && !dayElem.classList.contains('flatpickr-disabled')) {
                    dayElem.classList.add('bookable');
                }
            },
            onChange: function (selectedDates) {
                if (selectedDates.length > 0) {
                    const selectedDate = selectedDates[0];

                    // Update hidden checkin input
                    checkinHidden.value = formatYmd(selectedDate);

                    // Calculate checkout min date based on selection + min length of stay
                    const newCheckoutMin = new Date(selectedDate);
                    newCheckoutMin.setDate(newCheckoutMin.getDate() + minLengthOfStay);

                    // Update checkout picker constraints
                    checkoutPicker.set('minDate', newCheckoutMin);

                    // Auto-set checkout date
                    checkoutPicker.setDate(newCheckoutMin, true);
                    checkoutHidden.value = formatYmd(newCheckoutMin);

                    if (generalOptions.EnableUnoccupiedPricing === 1 && enableUnoccupiedPricing) {
                        getCheckInOutDates($(moduleInstance));
                    }
                }
            }
        });

        // --- ROOM QUANTITY HANDLER (preserve existing behavior) ---
        const roomQuantitySelect = moduleInstance.querySelector('.room_quantity');
        if (roomQuantitySelect) {
            roomQuantitySelect.addEventListener('change', function () {
                const curQuantity = parseInt(this.value);
                const rows = moduleInstance.querySelectorAll('.room_num_row');
                rows.forEach(function (row, index) {
                    const rowNum = index + 1;
                    if (rowNum <= curQuantity) {
                        row.style.display = '';
                        row.querySelectorAll('select.form-select-occupancy').forEach(function (sel) {
                            sel.removeAttribute('disabled');
                        });
                    } else {
                        row.style.display = 'none';
                        row.querySelectorAll('select.form-select-occupancy').forEach(function (sel) {
                            sel.setAttribute('disabled', 'disabled');
                        });
                    }
                });
            });
            // Trigger initial state
            roomQuantitySelect.dispatchEvent(new Event('change'));
        }

        // Store pickers on the module element for external access
        moduleInstance._flatpickrCheckin = checkinPicker;
        moduleInstance._flatpickrCheckout = checkoutPicker;
    };

})();
