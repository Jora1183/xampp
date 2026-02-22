/**
 * Solidres UX Enhancements
 * Loading states, date presets, micro-interactions, and mobile optimizations
 */
(function () {
    'use strict';

    var SolidresUX = {

        init: function () {
            this.initSkeletonLoader();
            this.initLoadingStates();
            this.initDatePresets();
            this.initStickySearch();
        },

        // --- SKELETON LOADER ---
        initSkeletonLoader: function () {
            var forms = document.querySelectorAll('.sr-search-form-enhanced');
            var skeletons = document.querySelectorAll('.sr-skeleton-container');

            // Hide skeleton and show form once Flatpickr is ready
            forms.forEach(function (form) {
                form.style.opacity = '0';
                form.style.transition = 'opacity 0.3s ease';
            });

            // Check for Flatpickr readiness
            var checkReady = function () {
                if (typeof flatpickr !== 'undefined') {
                    skeletons.forEach(function (s) { s.style.display = 'none'; });
                    forms.forEach(function (f) { f.style.opacity = '1'; });
                } else {
                    setTimeout(checkReady, 50);
                }
            };

            // Show form immediately if Flatpickr loads fast, or show skeleton briefly
            setTimeout(function () {
                checkReady();
            }, 100);

            // Fallback: always show form after 2s regardless
            setTimeout(function () {
                skeletons.forEach(function (s) { s.style.display = 'none'; });
                forms.forEach(function (f) { f.style.opacity = '1'; });
            }, 2000);
        },

        // --- FORM SUBMISSION LOADING STATE ---
        initLoadingStates: function () {
            var forms = document.querySelectorAll('.sr-search-form-enhanced');

            forms.forEach(function (form) {
                form.addEventListener('submit', function () {
                    var btn = form.querySelector('.sr-search-btn');
                    var overlay = document.getElementById('sr-loading-overlay');

                    if (btn) {
                        btn.classList.add('loading');
                        btn.disabled = true;
                    }

                    if (overlay) {
                        overlay.classList.add('active');
                        overlay.setAttribute('aria-hidden', 'false');
                    }

                    // Reset if page doesn't navigate within 10s (error case)
                    setTimeout(function () {
                        if (btn) {
                            btn.classList.remove('loading');
                            btn.disabled = false;
                        }
                        if (overlay) {
                            overlay.classList.remove('active');
                            overlay.setAttribute('aria-hidden', 'true');
                        }
                    }, 10000);
                });
            });
        },

        // --- DATE PRESETS (Quick Select) ---
        initDatePresets: function () {
            var presetBtns = document.querySelectorAll('.sr-date-preset-btn');

            presetBtns.forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var preset = this.getAttribute('data-preset');
                    var form = this.closest('.sr-search-form-enhanced') || this.closest('form');

                    if (!form) return;

                    var checkinInput = form.querySelector('.checkin_module');
                    var checkoutInput = form.querySelector('.checkout_module');

                    if (!checkinInput || !checkoutInput) return;

                    var today = new Date();
                    today.setHours(0, 0, 0, 0);
                    var checkin, checkout;

                    switch (preset) {
                        case 'tonight':
                            checkin = new Date(today);
                            checkout = new Date(today);
                            checkout.setDate(checkout.getDate() + 1);
                            break;

                        case 'tomorrow':
                            checkin = new Date(today);
                            checkin.setDate(checkin.getDate() + 1);
                            checkout = new Date(checkin);
                            checkout.setDate(checkout.getDate() + 1);
                            break;

                        case 'weekend':
                            // Find next Friday
                            var dayOfWeek = today.getDay();
                            var daysUntilFriday = (5 - dayOfWeek + 7) % 7;
                            if (daysUntilFriday === 0 && today.getHours() >= 12) {
                                daysUntilFriday = 7;
                            }
                            checkin = new Date(today);
                            checkin.setDate(checkin.getDate() + daysUntilFriday);
                            checkout = new Date(checkin);
                            checkout.setDate(checkout.getDate() + 2); // Friday to Sunday
                            break;

                        case 'nextweek':
                            // Next Monday
                            var daysUntilMonday = (1 - today.getDay() + 7) % 7;
                            if (daysUntilMonday === 0) daysUntilMonday = 7;
                            checkin = new Date(today);
                            checkin.setDate(checkin.getDate() + daysUntilMonday);
                            checkout = new Date(checkin);
                            checkout.setDate(checkout.getDate() + 7);
                            break;
                    }

                    if (!checkin || !checkout) return;

                    // Update Flatpickr instances
                    if (checkinInput._flatpickr) {
                        checkinInput._flatpickr.setDate(checkin, true);
                    }
                    if (checkoutInput._flatpickr) {
                        checkoutInput._flatpickr.setDate(checkout, true);
                    }

                    // Also update hidden inputs
                    var checkinHidden = form.querySelector('input[name="checkin"]');
                    var checkoutHidden = form.querySelector('input[name="checkout"]');
                    if (checkinHidden) checkinHidden.value = formatYmd(checkin);
                    if (checkoutHidden) checkoutHidden.value = formatYmd(checkout);

                    // Visual feedback on button
                    presetBtns.forEach(function (b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    setTimeout(function () { btn.classList.remove('active'); }, 1500);
                });
            });
        },

        // --- STICKY SEARCH ON MOBILE ---
        initStickySearch: function () {
            if (window.innerWidth > 991) return;

            var searchForm = document.querySelector('.sr-search-form-enhanced');
            if (!searchForm) return;

            var lastScroll = 0;
            var ticking = false;

            window.addEventListener('scroll', function () {
                lastScroll = window.pageYOffset;

                if (!ticking) {
                    window.requestAnimationFrame(function () {
                        if (lastScroll > 200) {
                            searchForm.style.transform = 'translateY(0)';
                            searchForm.style.boxShadow = '0 4px 20px rgba(0,0,0,0.12)';
                        } else {
                            searchForm.style.transform = '';
                            searchForm.style.boxShadow = '';
                        }
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
        }
    };

    // Helper: format date as Y-m-d
    function formatYmd(date) {
        if (!date) return '';
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { SolidresUX.init(); });
    } else {
        SolidresUX.init();
    }

    window.SolidresUX = SolidresUX;

})();
