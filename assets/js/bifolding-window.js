/**
 * Bifolding Window Builder – Main JavaScript
 *
 * Description:
 *   Handles a 14‑step wizard for building aluminium bifolding windows.
 *   Steps:
 *     1. Size         → 2. Panels          → 3. Opening Direction
 *     4. Outside Colour → 5. Inside Colour   → 6. Handle Colour
 *     7. Glass        → 8. Trickle Vents    → 9. Cill
 *     10. Postcode    → 11. Installation    → 12. Access
 *     13. Customer Information → 14. Order Summary
 *
 * Features:
 *   - Step navigation & validation
 *   - Dynamic panel options based on width/height (handled in step‑2 script)
 *   - Custom RAL colour dropdowns (outside & inside)
 *   - Inside colour visibility & auto‑selection
 *   - Delivery calculation via AJAX (from step‑10 script)
 *   - Installation type with photo upload requirement
 *   - Sliding drawer with real‑time summary & pricing
 *   - Edit mode prefill
 *
 * Dependencies: jQuery, global `windowBuilderData` (ajaxUrl, nonce),
 *               step‑2 defines `window.getWindowPaneCount()`
 *
 * @package Astra Child
 */

jQuery(function ($) {

    /**************************************************************
     * 1. STATE VARIABLES
     **************************************************************/
    let currentStep = 0;
    const steps = $('.wizard-step');
    const totalSteps = steps.length;
    const prevBtn = $('.prev-step');
    const nextBtn = $('.next-step');
    const submitContainer = $('.submit-container');
    const nextFooterBtn = $('.next-footer-btn');

    let editMode = false;
    let editCartKey = '';

    // Internal step name mapping (for debugging / future use)
    const stepMap = {
        0: 'size',
        1: 'panels',
        2: 'opening',
        3: 'outside_colour',
        4: 'inside_colour',
        5: 'handle_colour',
        6: 'glass',
        7: 'trickle_vents',
        8: 'cill',
        9: 'postcode',
        10: 'installation',
        11: 'access',
        12: 'customer_info',
        13: 'order_summary'
    };

    let selectedOutsideColour = 'anthracite_grey';   // default

    /**************************************************************
     * 2. HELPER
     **************************************************************/
    function isDev() {
        return window.location.hostname === 'localhost' ||
            window.location.hostname === '127.0.0.1';
    }

    /**************************************************************
     * 3. EDIT MODE PREFILL
     **************************************************************/
    function prefillFormData(data) {
        if (!data || Object.keys(data).length === 0) return;

        // Step 1 – Size
        if (data.width) $('#window_width').val(data.width);
        if (data.height) $('#window_height').val(data.height);

        // Step 2 – Panels (now uses the same panel values as your updated window builder)
        if (data.panels) {
            const panelMap = {
                '2 Panels Left': '2_left', '2 Panels Right': '2_right',
                '3 Panels Left': '3_left', '3 Panels Right': '3_right',
                '1+3 Panels': '1_3', '3+1 Panels': '3_1',
                '4 Panels Left': '4_left', '4 Panels Right': '4_right',
                '5 Panels Left': '5_left', '5 Panels Right': '5_right',
                // Add any other panel names you’ve introduced (e.g., French Doors)
            };
            let panelValue = panelMap[data.panels] || data.panels;
            setTimeout(function () {
                $('input[name="window_panel_layout"][value="' + panelValue + '"]')
                    .prop('checked', true).trigger('change');
            }, 200);
        }

        // Step 4 – Outside Colour
        if (data.colour) {
            if (data.colour.startsWith('RAL ')) {
                $('#colour_custom').prop('checked', true).trigger('change');
                $('#custom_colour_select').val(data.colour).trigger('change');
            } else {
                const colourMap = { 'Anthracite Grey': 'anthracite_grey', 'Black': 'black', 'White': 'white' };
                let val = colourMap[data.colour] || 'white';
                $('input[name="window_colour"][value="' + val + '"]')
                    .prop('checked', true).trigger('change');
            }
        }

        // Step 5 – Inside Colour
        if (data.inside_colour) {
            setTimeout(function () {
                if (data.inside_colour.startsWith('RAL ')) {
                    $('#inside_colour_custom').prop('checked', true).trigger('change');
                    $('#custom_inside_colour_select').val(data.inside_colour).trigger('change');
                } else {
                    const colourMap = { 'Anthracite Grey': 'anthracite_grey', 'Black': 'black', 'White': 'white' };
                    let val = colourMap[data.inside_colour] || 'custom_ral';
                    let $option = $('input[name="inside_colour"][value="' + val + '"]');
                    if ($option.length && $option.closest('.inside-colour-option').is(':visible')) {
                        $option.prop('checked', true).trigger('change');
                    }
                }
            }, 300);
        }

        // Step 6 – Handle Colour
        if (data.handle) {
            const handleMap = { 'White': 'white', 'Chrome': 'chrome', 'Black': 'black', 'Black and White': 'black_white' };
            $('input[name="handle_colour"][value="' + (handleMap[data.handle] || 'white') + '"]').prop('checked', true);
        }

        // Step 11 – Installation Type
        if (data.installation_type) {
            const installMap = {
                'collection': 'collection',
                'delivery': 'delivery',
                'prepared_opening': 'prepared_opening',
                'remove_existing': 'remove_existing'
            };
            let installValue = installMap[data.installation_type] || data.installation_type;
            $('input[name="window_installation_type"][value="' + installValue + '"]')
                .prop('checked', true).trigger('change');
        }

        // Step 12 – Access Issues
        if (data.access) {
            if (data.access === 'No' || data.access === 'No Access Issues') {
                $('input[name="access_issues"][value="no_access"]').prop('checked', true);
            } else {
                $('input[name="access_issues"][value="yes_access"]').prop('checked', true);
                let desc = data.access.replace('Yes, ', '').replace('Yes', '');
                if (desc) $('#access_description').val(desc);
            }
        }

        // Step 10 – Postcode
        if (data.postcode) {
            $('#postcode').val(data.postcode);
            setTimeout(function () {
                $('#postcode').trigger('input');
            }, 500);
        }

        // Finalise
        setTimeout(function () {
            if (typeof updatePrice === 'function') updatePrice();
            if (typeof updateDrawer === 'function') updateDrawer();
            $(document).trigger('stepChanged', [currentStep]);
        }, 600);
    }

    /**************************************************************
     * 4. WIZARD INITIALISATION
     **************************************************************/
    function initWizard() {
        if (!steps.length) return;

        // ----- Edit mode detection -----
        if (typeof window.editMode !== 'undefined' && window.editMode) {
            editMode = true;
            editCartKey = window.editCartKey || '';
            if (editCartKey) $('#cart_item_key_field').val(editCartKey);
            if (window.editData) prefillFormData(window.editData);
            setTimeout(function () {
                if (typeof updateDrawer === 'function') updateDrawer();
            }, 600);
        } else {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('edit_cart_item')) {
                editMode = true;
                editCartKey = urlParams.get('edit_cart_item');
                $('#cart_item_key_field').val(editCartKey);
                window.editCartKey = editCartKey;
            }
        }
        window.editMode = editMode;

        // ----- Initial UI -----
        showStep(currentStep);
        updateNavigation();
        updatePrice();
        validateCurrentStep();

        // ----- Colour management -----
        initCustomColourDropdowns();
        initOutsideColourTracking();
        updateInsideColourOptions();

        setTimeout(function () {
            const initialOutside = $('input[name="window_colour"]:checked').val();
            if (initialOutside) autoSelectMatchingInsideColour(initialOutside);
        }, 200);

        const savedPostcode = $('#postcode').val();
        if (savedPostcode && savedPostcode.length > 0) {
            setTimeout(function () {
                $('#postcode').trigger('input');
            }, 1000);
        }
    }

    /**************************************************************
     * 5. COLOUR MANAGEMENT
     **************************************************************/
    function initCustomColourDropdowns() {
        const $ccSelect = $('#custom_colour_select'),
            $ccRadio = $('#colour_custom'),
            $ccRal = $('.custom-colour-card .selected-ral-code'),
            $ccCard = $('.custom-colour-card'),
            $ccDrop = $('.custom-colour-dropdown');
        const $icSelect = $('#custom_inside_colour_select'),
            $icRadio = $('#inside_colour_custom'),
            $icRal = $('.custom-inside-colour-card .selected-inside-ral-code'),
            $icCard = $('.custom-inside-colour-card'),
            $icDrop = $('.custom-inside-colour-dropdown');

        // Outside
        if ($ccCard.length) {
            $ccCard.on('click', function (e) {
                if (!$(e.target).closest('.custom-colour-dropdown').length) {
                    $ccRadio.prop('checked', true);
                    $ccDrop.show();
                    $ccRadio.trigger('change');
                }
            });
            $ccSelect.on('change', function () {
                if ($(this).val()) {
                    $ccRal.text($(this).val());
                    $ccRadio.val($(this).val());
                    $ccRadio.data('price', $('option:selected', this).data('price') || 195);
                    $ccRadio.trigger('change');
                    if (currentStep === 3) validateCurrentStep();
                }
            });
            $('input[name="window_colour"]').on('change', function () {
                if ($(this).attr('id') !== 'colour_custom' && $(this).is(':checked')) {
                    $ccSelect.val('');
                    $ccRal.text('From £195 per panel');
                    $ccRadio.val('custom_ral');
                    $ccDrop.hide();
                    if (currentStep === 3) validateCurrentStep();
                }
            });
        }

        // Inside
        if ($icCard.length) {
            $icCard.on('click', function (e) {
                if (!$(e.target).closest('.custom-inside-colour-dropdown').length) {
                    $icRadio.prop('checked', true);
                    $icDrop.show();
                    $icRadio.trigger('change');
                }
            });
            $icSelect.on('change', function () {
                if ($(this).val()) {
                    $icRal.text($(this).val());
                    $icRadio.val($(this).val());
                    $icRadio.data('price', $('option:selected', this).data('price') || 195);
                    $icRadio.trigger('change');
                    if (currentStep === 4) validateCurrentStep();
                }
            });
            $('input[name="inside_colour"]').on('change', function () {
                if ($(this).attr('id') !== 'inside_colour_custom' && $(this).is(':checked')) {
                    $icSelect.val('');
                    $icRal.text('From £195 per panel');
                    $icRadio.val('custom_ral');
                    $icDrop.hide();
                    if (currentStep === 4) validateCurrentStep();
                }
            });
        }

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.custom-colour-card').length &&
                !$(e.target).closest('.custom-colour-dropdown').length) {
                $ccDrop.hide();
            }
            if (!$(e.target).closest('.custom-inside-colour-card').length &&
                !$(e.target).closest('.custom-inside-colour-dropdown').length) {
                $icDrop.hide();
            }
        });
    }

    function initOutsideColourTracking() {
        const initial = $('input[name="window_colour"]:checked');
        if (initial.length) {
            selectedOutsideColour = getOutsideColourCategory(initial.val());
            autoSelectMatchingInsideColour(initial.val());
        }
        $(document).on('change', 'input[name="window_colour"]', function () {
            const colourValue = $(this).val();
            selectedOutsideColour = getOutsideColourCategory(colourValue);
            updateInsideColourOptions(colourValue);
            validateInsideColourSelection();
            updatePrice();
            autoSelectMatchingInsideColour(colourValue);
        });
        $(document).on('change', '#custom_colour_select', function () {
            if ($('#colour_custom').is(':checked')) {
                updateInsideColourOptions('custom_ral');
                updatePrice();
                autoSelectMatchingInsideColour('custom_ral');
            }
        });
    }

    function getOutsideColourCategory(value) {
        if (value && value.startsWith('RAL ')) return 'custom_ral';
        if (['anthracite_grey', 'black', 'white'].includes(value)) return value;
        return 'custom_ral';
    }

    function autoSelectMatchingInsideColour(outsideValue) {
        if (!outsideValue) return;
        let match = null;
        if (outsideValue === 'custom_ral') {
            if ($('#custom_colour_select').val()) match = 'custom_ral';
        } else if (outsideValue === 'anthracite_grey') match = 'anthracite_grey';
        else if (outsideValue === 'black') match = 'black';
        else if (outsideValue === 'white') match = 'white';
        else match = 'white';

        if (match) {
            const $matchOption = $('input[name="inside_colour"][value="' + match + '"]');
            const $matchCard = $matchOption.closest('.inside-colour-option');
            if ($matchCard.is(':visible')) {
                $matchOption.prop('checked', true).trigger('change');
                if (match === 'custom_ral') {
                    const customOutsideValue = $('#custom_colour_select').val();
                    if (customOutsideValue) {
                        $('#custom_inside_colour_select').val(customOutsideValue).trigger('change');
                        $('.selected-inside-ral-code').text(customOutsideValue);
                        $('#inside_colour_custom').val(customOutsideValue);
                    }
                }
            } else {
                const $firstVisible = $('.inside-colour-option:visible input[name="inside_colour"]');
                if ($firstVisible.length) $firstVisible.prop('checked', true).trigger('change');
            }
        }
    }

    function updateInsideColourOptions(selectedOutsideValue) {
        if (!selectedOutsideValue) {
            selectedOutsideValue = $('input[name="window_colour"]:checked').val();
        }
        const $anthracite = $('#inside_colour_anthracite').closest('.inside-colour-option');
        const $black = $('#inside_colour_black').closest('.inside-colour-option');
        const $white = $('#inside_colour_white').closest('.inside-colour-option');
        const $custom = $('#inside_colour_custom').closest('.inside-colour-option');

        $anthracite.hide(); $black.hide(); $white.hide(); $custom.hide();

        switch (selectedOutsideValue) {
            case 'anthracite_grey': $anthracite.show(); $white.show(); $custom.show(); break;
            case 'black': $black.show(); $custom.show(); break;
            case 'white': $white.show(); $custom.show(); break;
            case 'custom_ral': $white.show(); $custom.show(); break;
            default: $white.show(); $custom.show();
        }

        setTimeout(function () {
            autoSelectMatchingInsideColour(selectedOutsideValue);
        }, 50);
        validateInsideColourSelection();
        updateInsideColourGridLayout();
    }

    function validateInsideColourSelection() {
        const $selected = $('input[name="inside_colour"]:checked');
        if ($selected.length) {
            if (!$selected.closest('.inside-colour-option').is(':visible')) {
                const outsideValue = $('input[name="window_colour"]:checked').val();
                autoSelectMatchingInsideColour(outsideValue);
            }
        } else {
            const outsideValue = $('input[name="window_colour"]:checked').val();
            if (outsideValue) autoSelectMatchingInsideColour(outsideValue);
            else if ($('#inside_colour_white').closest('.inside-colour-option').is(':visible')) {
                $('#inside_colour_white').prop('checked', true).trigger('change');
            }
        }
        $('input[name="inside_colour"]:checked').trigger('change');
    }

    function updateInsideColourGridLayout() {
        const visibleCount = $('.inside-colour-option:visible').length;
        const $grid = $('.colour-inside-options-grid');
        if (visibleCount === 1) $grid.css('grid-template-columns', '1fr');
        else if (visibleCount === 2) $grid.css('grid-template-columns', 'repeat(2, 1fr)');
        else if (visibleCount === 3) $grid.css('grid-template-columns', 'repeat(3, 1fr)');
        else $grid.css('grid-template-columns', 'repeat(4, 1fr)');
    }

    /**************************************************************
     * 6. STEP NAVIGATION & VALIDATION
     **************************************************************/
    function showStep(index) {
        if (index < 0 || index >= totalSteps) return;
        steps.removeClass('active');
        steps.eq(index).addClass('active');
        currentStep = index;
        window.currentStep = currentStep;

        updateNavigation();
        submitContainer.toggle(index === totalSteps - 1);
        prevBtn.prop('disabled', index === 0);

        // Step‑specific initialisation
        if (index === 3) setTimeout(validateCurrentStep, 100);
        if (index === 4) {
            setTimeout(function () {
                const outsideValue = $('input[name="window_colour"]:checked').val();
                if (outsideValue) updateInsideColourOptions(outsideValue);
                validateInsideColourSelection();
                validateCurrentStep();
            }, 200);
        }
        if (index === 9) {
            const postcode = $('#postcode').val().trim();
            if (!postcode) disableNext();
            else setTimeout(function () { $('#postcode').trigger('input'); }, 100);
        }
        if (index === 10) {
            const installValue = $('input[name="window_installation_type"]:checked').val();
            if (!installValue) disableNext();
            else if (installValue === 'remove_existing') {
                const photoFile = $('#window_photo').val();
                if (!photoFile) disableNext();
                else enableNext();
            } else enableNext();
        }
        if (index === 11) {
            const accessValue = $('input[name="access_issues"]:checked').val();
            if (!accessValue) disableNext();
            else if (accessValue === 'yes_access') {
                const desc = $('#access_description').val().trim();
                if (!desc) disableNext();
                else enableNext();
            } else enableNext();
        }
        // Step 13 – Customer Info (index 12)
        if (index === 12) {
            // The customer form script sets the button state; we also call validateCurrentStep to sync
            setTimeout(validateCurrentStep, 100);
        }

        $(document).trigger('stepChanged', [index]);
    }

    function updateNavigation() {
        if (currentStep === totalSteps - 1) {
            const label = editMode ? 'UPDATE CART' : 'ADD TO CART';
            nextBtn.text(label);
            if (nextFooterBtn.length) nextFooterBtn.text(label + ' →');
        } else {
            nextBtn.text('NEXT');
            if (nextFooterBtn.length) nextFooterBtn.text('NEXT →');
        }
    }

    function disableNext() {
        nextBtn.addClass('inactive').prop('disabled', true);
        if (nextFooterBtn.length) nextFooterBtn.addClass('inactive').prop('disabled', true);
    }

    function enableNext() {
        nextBtn.removeClass('inactive').prop('disabled', false);
        if (nextFooterBtn.length) nextFooterBtn.removeClass('inactive').prop('disabled', false);
    }

    function validateCurrentStep() {
        const isValid = validateStep(currentStep);
        if (currentStep < totalSteps - 1) {
            isValid ? enableNext() : disableNext();
        }
        return isValid;
    }

    function validateStep(step) {
        // Step 1 – Size
        if (step === 0) {
            const width = $('#window_width').val();
            const height = $('#window_height').val();
            let ok = true;
            $('#width-error, #height-error').hide();
            if (!width) { $('#width-error').text('Width required.').show(); ok = false; }
            else { const n = parseInt(width); if (isNaN(n) || n < 1600 || n > 5800) { $('#width-error').text('Width 1600‑5800 mm').show(); ok = false; } }
            if (!height) { $('#height-error').text('Height required.').show(); ok = false; }
            else { const n = parseInt(height); if (isNaN(n) || n < 700 || n > 1650) { $('#height-error').text('Height 700‑1650 mm').show(); ok = false; } }
            return ok;
        }
        // Step 2 – Panels
        if (step === 1) return $('input[name="window_panel_layout"]:checked').length > 0;
        // Step 3 – Opening Direction
        if (step === 2) return $('input[name="open_direction"]:checked').length > 0;
        // Step 4 – Outside Colour
        if (step === 3) {
            const val = $('input[name="window_colour"]:checked').val();
            if (val === 'custom_ral') {
                const ral = $('#custom_colour_select').val();
                return ral && ral !== '';
            }
            return !!val;
        }
        // Step 5 – Inside Colour
        if (step === 4) {
            const selected = $('input[name="inside_colour"]:checked');
            if (!selected.length) return false;
            const val = selected.val();
            if (val === 'custom_ral') {
                const ral = $('#custom_inside_colour_select').val();
                return ral && ral !== '';
            }
            return selected.closest('.inside-colour-option').is(':visible');
        }
        // Step 6 – Handle
        if (step === 5) return $('input[name="handle_colour"]:checked').length > 0;
        // Step 7 – Glass
        if (step === 6) return $('input[name="glass_upgrade"]:checked').length > 0;
        // Step 8 – Trickle Vents
        if (step === 7) return $('input[name="trickle_vents"]:checked').length > 0;
        // Step 9 – Cill
        if (step === 8) return $('input[name="cill"]:checked').length > 0;
        // Step 10 – Postcode
        if (step === 9) {
            const pc = $('#postcode').val().replace(/\s+/g, '').trim();
            return pc.length > 0 && !(window.deliveryData && window.deliveryData.bespoke);
        }
        // Step 11 – Installation
        if (step === 10) {
            const install = $('input[name="window_installation_type"]:checked').val();
            if (!install) return false;
            if (install === 'remove_existing') return !!$('#window_photo').val();
            return true;
        }
        // Step 12 – Access
        if (step === 11) {
            const access = $('input[name="access_issues"]:checked').val();
            if (!access) return false;
            if (access === 'yes_access') return $('#access_description').val().trim().length > 0;
            return true;
        }
        // Step 13 – Customer Information (index 12)
        if (step === 12) {
            const firstName = $('#first_name').val().trim();
            const lastName = $('#last_name').val().trim();
            const mobile = $('#mobile_number').val().trim();
            const email = $('#email_address').val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const mobileRegex = /^[0-9]{10,11}$/;
            const isValid = firstName && lastName && mobile && email &&
                emailRegex.test(email) &&
                mobileRegex.test(mobile.replace(/\s/g, ''));
            return isValid;
        }
    }

    /**************************************************************
     * 7. PRICE CALCULATION
     **************************************************************/
    function updatePrice() {
        let base = parseFloat($('#base_price_value').val()) || 0;
        let extra = 0;

        const paneCount = window.getWindowPaneCount ? window.getWindowPaneCount() : 1;

        // Outside Colour
        const oc = $('input[name="window_colour"]:checked').val();
        if (oc === 'custom_ral') extra += 195 * paneCount;

        // Inside Colour (free dual colour excluded)
        const ic = $('input[name="inside_colour"]:checked').val();
        if (ic === 'custom_ral' && !(oc === 'anthracite_grey' && ic === 'white')) {
            extra += 195 * paneCount;
        }

        // Glass upgrade
        const glass = $('input[name="glass_upgrade"]:checked');
        if (glass.length && glass.val() !== 'no_thanks') {
            extra += (parseFloat(glass.data('price')) || 0) * paneCount;
        }

        // Trickle Vents
        if ($('input[name="trickle_vents"]:checked').val() === 'yes_trickle') extra += 85;

        // Delivery
        if (window.deliveryData) {
            if (!window.deliveryData.bespoke) extra += parseFloat(window.deliveryData.price) || 0;
        } else {
            const dp = parseFloat($('#delivery_price').val()) || 0;
            if ($('#delivery_bespoke').val() !== '1') extra += dp;
        }

        // Installation
        const installType = $('input[name="window_installation_type"]:checked').val();
        if (installType === 'prepared_opening') extra += paneCount * 200;
        else if (installType === 'remove_existing') extra += (paneCount * 200) + 550;

        const total = base + extra;
        $('#final-price-confirm').text('£' + total.toFixed(2));
        $('#submit-price').text('£' + total.toFixed(2));
        $('#final_price_input').val(total.toFixed(2));
        window.lastCalculatedPrice = total.toFixed(2);
    }

    /**************************************************************
     * 8. BUTTON HANDLING & SUBMISSION
     **************************************************************/
    function goNextStep() {
        if (!validateCurrentStep()) return;
        if (currentStep < totalSteps - 1) {
            showStep(currentStep + 1);
        } else {
            // Last step → submit
            if (typeof window.submitBuilderForm === 'function') {
                window.submitBuilderForm();
            } else {
                $('#window-builder-form').submit();
            }
        }
    }

    function goPrevStep() {
        if (currentStep > 0) showStep(currentStep - 1);
    }

    // Attach navigation events
    $(document).on('click', '.next-step', goNextStep);
    $(document).on('click', '.prev-step', goPrevStep);
    if (nextFooterBtn.length) nextFooterBtn.on('click', goNextStep);

    // Recalculate price / validate on field changes
    $(document).on('change input',
        '#window_width, #window_height, ' +
        'input[name="window_panel_layout"], input[name="window_colour"], ' +
        'input[name="inside_colour"], input[name="handle_colour"], ' +
        'input[name="glass_upgrade"], input[name="trickle_vents"], ' +
        'input[name="open_direction"], input[name="window_installation_type"], ' +
        'input[name="access_issues"], #access_description, ' +
        '#postcode, #window_photo, #custom_colour_select, #custom_inside_colour_select, ' +
        '#first_name, #last_name, #mobile_number, #email_address',
        function () {
            updatePrice();
            validateCurrentStep();
            if (typeof updateDrawer === 'function') updateDrawer();
        }
    );

    /**************************************************************
     * 9. SLIDING DRAWER (REAL‑TIME SUMMARY)
     **************************************************************/
    const stepDefinitions = [
        {
            number: 1, name: 'Window Size',
            getValue: function () {
                const w = $('#window_width').val(), h = $('#window_height').val();
                return (w && h) ? w + ' x ' + h + 'mm' : '—';
            },
            getPrice: function () { return parseFloat($('#base_price_value').val()) || 0; }
        },
        {
            number: 2, name: 'Panels',
            getValue: function () {
                const val = $('input[name="window_panel_layout"]:checked').val();
                if (!val) return '—';
                // Map all possible panel values that your updated step‑2 may produce
                const map = {
                    '2_left': '2 Panels Left', '2_right': '2 Panels Right',
                    '3_left': '3 Panels Left', '3_right': '3 Panels Right',
                    '4_left': '4 Panels Left', '4_right': '4 Panels Right',
                    '5_left': '5 Panels Left', '5_right': '5 Panels Right',
                    'french': 'French Doors',   // if you added it
                    // Add any other custom values you introduced
                };
                return map[val] || val;
            },
            getPrice: function () { return 0; }
        },
        {
            number: 3, name: 'Opening',
            getValue: function () {
                const val = $('input[name="open_direction"]:checked').val();
                return val ? (val === 'inwards' ? 'Inwards' : 'Outwards') : '—';
            },
            getPrice: function () { return 0; }
        },
        {
            number: 4, name: 'Outside Colour',
            getValue: function () {
                const val = $('input[name="window_colour"]:checked').val();
                if (!val) return '—';
                if (val === 'custom_ral') {
                    const ral = $('#custom_colour_select').val();
                    return ral || 'Custom RAL';
                }
                const map = { 'anthracite_grey': 'Anthracite Grey', 'black': 'Black', 'white': 'White' };
                return map[val] || val;
            },
            getPrice: function () {
                const val = $('input[name="window_colour"]:checked').val();
                return val === 'custom_ral' ? 195 * (window.getWindowPaneCount ? window.getWindowPaneCount() : 1) : 0;
            }
        },
        {
            number: 5, name: 'Inside Colour',
            getValue: function () {
                const val = $('input[name="inside_colour"]:checked').val();
                if (!val) return '—';
                if (val === 'custom_ral') {
                    const ral = $('#custom_inside_colour_select').val();
                    return ral || 'Custom RAL';
                }
                const map = { 'anthracite_grey': 'Anthracite Grey', 'black': 'Black', 'white': 'White' };
                return map[val] || val;
            },
            getPrice: function () {
                const val = $('input[name="inside_colour"]:checked').val();
                const outside = $('input[name="window_colour"]:checked').val();
                const pc = window.getWindowPaneCount ? window.getWindowPaneCount() : 1;
                if (outside === 'anthracite_grey' && val === 'white') return 0;
                return val === 'custom_ral' ? 195 * pc : 0;
            }
        },
        {
            number: 6, name: 'Handle',
            getValue: function () {
                const val = $('input[name="handle_colour"]:checked').val();
                const map = { 'white': 'White', 'chrome': 'Chrome', 'black': 'Black', 'black_white': 'Black & White' };
                return map[val] || val || '—';
            },
            getPrice: function () { return 0; }
        },
        {
            number: 7, name: 'Glass',
            getValue: function () {
                const val = $('input[name="glass_upgrade"]:checked').val();
                if (!val) return '—';
                if (val === 'no_thanks') return 'Standard Glass';
                const map = {
                    'self_cleaning': 'Self‑cleaning',
                    'integral_blinds': 'Integral Blinds',
                    'obscure_glass': 'Obscure',
                    'saint_gobain_12': 'Saint‑Gobain 1.2'
                };
                return map[val] || val;
            },
            getPrice: function () {
                const val = $('input[name="glass_upgrade"]:checked');
                return val.val() === 'no_thanks' ? 0 : (parseFloat(val.data('price')) || 0) * (window.getWindowPaneCount ? window.getWindowPaneCount() : 1);
            }
        },
        {
            number: 8, name: 'Trickle Vents',
            getValue: function () {
                return $('input[name="trickle_vents"]:checked').val() === 'yes_trickle' ? 'Yes' : 'No';
            },
            getPrice: function () {
                return $('input[name="trickle_vents"]:checked').val() === 'yes_trickle' ? 85 : 0;
            }
        },
        {
            number: 9, name: 'Cill',
            getValue: function () {
                const val = $('input[name="cill"]:checked').val();
                const map = { 'none': 'No Cill', '150mm-aluminium-cill': 'Aluminium Cill', '150mm-upvc-cill': 'uPVC Cill' };
                return map[val] || val || '—';
            },
            getPrice: function () { return 0; }
        },
        {
            number: 10, name: 'Postcode',
            getValue: function () { return $('#postcode').val().replace(/\s+/g, '') || '—'; },
            getPrice: function () {
                if (window.deliveryData) {
                    return window.deliveryData.bespoke ? 0 : parseFloat(window.deliveryData.price) || 0;
                }
                const dp = parseFloat($('#delivery_price').val()) || 0;
                return $('#delivery_bespoke').val() === '1' ? 0 : dp;
            }
        },
        {
            number: 11, name: 'Installation',
            getValue: function () {
                const val = $('input[name="window_installation_type"]:checked').val();
                const map = {
                    'collection': 'Supply Only – Collection',
                    'delivery': 'Supply Only – Delivery',
                    'prepared_opening': 'Installed into Prepared Opening',
                    'remove_existing': 'Remove Existing Windows & Install'
                };
                return map[val] || val || '—';
            },
            getPrice: function () {
                const val = $('input[name="window_installation_type"]:checked').val();
                const pc = window.getWindowPaneCount ? window.getWindowPaneCount() : 1;
                if (val === 'collection') return 0;
                if (val === 'delivery') return 0; // already in delivery
                if (val === 'prepared_opening') return pc * 200;
                if (val === 'remove_existing') return (pc * 200) + 550;
                return 0;
            }
        },
        {
            number: 12, name: 'Access',
            getValue: function () {
                const val = $('input[name="access_issues"]:checked').val();
                if (!val) return '—';
                if (val === 'yes_access') {
                    const desc = $('#access_description').val();
                    return desc ? 'Yes: ' + desc : 'Yes';
                }
                return 'No';
            },
            getPrice: function () { return 0; }
        }
        // Steps 13 & 14 are not shown in the drawer – it only covers configuration
    ];

    function buildDrawerSteps() {
        let html = '';
        stepDefinitions.forEach(function (step) {
            const value = step.getValue();
            const price = step.getPrice();
            const priceDisplay = price > 0 ? '£' + price.toFixed(2) : '£0';
            const completedClass = value !== '—' ? 'completed' : '';
            html +=
                '<div class="drawer-step-item ' + completedClass + '" data-step="' + step.number + '">' +
                '<div class="step-label">' +
                '<span class="step-number">' + step.number + '</span>' +
                '<span class="step-name">' + step.name + '</span>' +
                '</div>' +
                '<div class="step-value" title="' + value + '">' + value + '</div>' +
                '<div class="step-price">' + priceDisplay + '</div>' +
                '</div>';
        });
        $('#drawerStepsList').html(html);
    }

    function updateDrawer() {
        let total = 0;
        stepDefinitions.forEach(function (step) {
            const value = step.getValue();
            const price = step.getPrice();
            const $item = $('.drawer-step-item[data-step="' + step.number + '"]');
            if ($item.length) {
                $item.find('.step-value').text(value).attr('title', value);
                $item.find('.step-price').text(price > 0 ? '£' + price.toFixed(2) : '£0');
                $item.toggleClass('completed', value !== '—');
            }
            total += price;
        });
        $('#drawer-total-price').text('£' + total.toFixed(2));
        $('#drawer-footer-total').text('£' + total.toFixed(2));
        $('#final_price_input').val(total.toFixed(2));
    }

    function initDrawer() {
        buildDrawerSteps();
        updateDrawer();

        $('#drawerToggle').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#drawerContent').addClass('open');
            $('.toggle-arrow').text('▶');
        });
        $('#drawerClose').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#drawerContent').removeClass('open');
            $('.toggle-arrow').text('◀');
        });
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#drawerContent').length && !$(e.target).closest('#drawerToggle').length) {
                $('#drawerContent').removeClass('open');
                $('.toggle-arrow').text('◀');
            }
        });

        // Update drawer on relevant field changes
        $(document).on('change input',
            '#window_width, #window_height, input[name="window_panel_layout"], ' +
            'input[name="window_colour"], input[name="inside_colour"], ' +
            'input[name="handle_colour"], input[name="glass_upgrade"], ' +
            'input[name="trickle_vents"], input[name="open_direction"], ' +
            'input[name="window_installation_type"], input[name="access_issues"], ' +
            '#access_description, #postcode, #window_photo, ' +
            '#custom_colour_select, #custom_inside_colour_select',
            function () { updateDrawer(); }
        );

        $(document).on('deliveryDataUpdated', function () { updateDrawer(); });

        $(document).on('stepChanged', function (e, stepIndex) {
            $('.drawer-step-item').removeClass('active-step');
            $('.drawer-step-item[data-step="' + (stepIndex + 1) + '"]').addClass('active-step');
        });

        $('#drawerAddToCart').on('click', function () {
            if ($(this).hasClass('disabled')) return;
            goNextStep(); // will trigger submit on last step
        });
        $('#drawerCheckout').on('click', function () {
            if ($(this).hasClass('disabled')) return;
            $('#builder_checkout_input').val('1');
            goNextStep();
        });

        if (window.editMode) {
            $('#drawerEditMode').show().text('Editing cart item: ' + (window.editCartKey || ''));
        }
    }

    if ($('.drawer-container').length) initDrawer();

    /**************************************************************
     * 10. GLOBAL HELPERS
     **************************************************************/
    window.updateDrawer = updateDrawer;

    // IMPORTANT: window.getWindowPaneCount() is defined in step‑2.php.
    // Do NOT redefine it here – rely on the step‑2 script to provide it.

    /**************************************************************
     * 11. START THE WIZARD
     **************************************************************/
    initWizard();
});