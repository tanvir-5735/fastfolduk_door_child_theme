<?php
/**
 * Template part for Order Summary Step in Window Builder
 * Step 14: Your Order Summary
 *
 * @package Astra Child
 */
?>

<!-- Step 14: Order Summary -->
<div class="wizard-step" data-step="14">
    <div class="step-container summary-container">

        <div class="summary-header">
            <h1 class="summary-title">Aluminium Window</h1>
            <div class="summary-price">£<span id="summary-total-price">0.00</span> <span class="price-vat">(inc. VAT)</span></div>
        </div>

        <hr class="summary-divider">

        <div class="summary-section">
            <div class="summary-grid">
                <div class="summary-row"><span class="summary-label">Manufacturing Size (mm):</span><span class="summary-value" id="summary-size">—</span></div>
                <div class="summary-row"><span class="summary-label">Panels:</span><span class="summary-value" id="summary-panels">—</span></div>
                <div class="summary-row"><span class="summary-label">Opening Direction:</span><span class="summary-value" id="summary-opening">—</span></div>
                <div class="summary-row"><span class="summary-label">Outside Colour:</span><span class="summary-value" id="summary-outside-colour">—</span></div>
                <div class="summary-row"><span class="summary-label">Inside Colour:</span><span class="summary-value" id="summary-inside-colour">—</span></div>
                <div class="summary-row"><span class="summary-label">Handle Colour:</span><span class="summary-value" id="summary-handle-colour">—</span></div>
                <div class="summary-row"><span class="summary-label">Glass:</span><span class="summary-value" id="summary-glass">—</span></div>
                <div class="summary-row"><span class="summary-label">Trickle Vents:</span><span class="summary-value" id="summary-trickle-vents">—</span></div>
                <div class="summary-row"><span class="summary-label">Cill:</span><span class="summary-value" id="summary-cill">—</span></div>
                <div class="summary-row"><span class="summary-label">Postcode:</span><span class="summary-value" id="summary-postcode">—</span></div>
                
                <!-- ===== Installation Type Row ===== -->
                <div class="summary-row" id="summary-installation-row">
                    <span class="summary-label">Installation:</span>
                    <span class="summary-value" id="summary-installation">—</span>
                </div>
                
                <div class="summary-row"><span class="summary-label">Access Issues:</span><span class="summary-value" id="summary-access">—</span></div>
                
                <!-- ===== Delivery Row (will be added dynamically) ===== -->
            </div>
        </div>

        <!-- Submit Button -->
        <div class="submit-container">
            <button type="button" class="submit-btn add-to-cart-btn" id="submit-btn">
                ADD TO CART - <span id="submit-price">£0.00</span> <span class="price-vat">(inc. VAT)</span>
            </button>
        </div>

        <!-- Hidden fields -->
        <input type="hidden" name="summary_size" id="summary_size_field" value="">
        <input type="hidden" name="summary_panels" id="summary_panels_field" value="">
        <input type="hidden" name="summary_opening" id="summary_opening_field" value="">
        <input type="hidden" name="summary_outside_colour" id="summary_outside_colour_field" value="">
        <input type="hidden" name="summary_inside_colour" id="summary_inside_colour_field" value="">
        <input type="hidden" name="summary_handle_colour" id="summary_handle_colour_field" value="">
        <input type="hidden" name="summary_glass" id="summary_glass_field" value="">
        <input type="hidden" name="summary_trickle_vents" id="summary_trickle_vents_field" value="">
        <input type="hidden" name="summary_cill" id="summary_cill_field" value="">
        <input type="hidden" name="summary_postcode" id="summary_postcode_field" value="">
        <input type="hidden" name="summary_installation" id="summary_installation_field" value="">
        <input type="hidden" name="summary_access" id="summary_access_field" value="">
        <input type="hidden" name="summary_total_price" id="summary_total_price_field" value="">
    </div>
</div>

<style>
/* ================================
   Order Summary Step Styles
   (Identical to the door version)
   ================================ */

/* VAT text styling */
.price-vat {
    font-size: 14px;
    font-weight: 400;
    color: #666;
    margin-left: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    vertical-align: middle;
    white-space: nowrap;
}

.wizard-step.active {
    z-index: 1;
    margin-bottom: 40px;
}

.summary-container {
    max-width: 1400px;
    margin: 0 auto;
    background: #133013;
    z-index: 1;
    margin-top: 0;
    padding: 30px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    position: relative;
}

/* ===== HEADER SECTION ===== */
.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
}

.summary-title {
    font-size: 32px;
    color: #FFFFFF;
    font-weight: 600;
    margin: 0;
    line-height: 1.3;
    letter-spacing: -0.3px;
}

.summary-price {
    font-size: 28px;
    color: #cbbfa9;
    font-weight: 700;
    background: #f8f5f0;
    padding: 8px 20px;
    border-radius: 40px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.summary-price span {
    color: #1a1a1a;
}

/* ===== DIVIDER ===== */
.summary-divider {
    border: none;
    border-top: 2px solid #eaeaea;
    margin: 25px 0;
}

/* ===== SUMMARY GRID ===== */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px 30px;
    max-width: 1000px;
    background: #faf9f7;
    padding: 25px 30px;
    border-radius: 12px;
    border: 1px solid #eaeaea;
    margin-bottom: 20px;
}

/* ===== SUMMARY ROW ===== */
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 5px 0;
    border-bottom: 1px dashed #e0e0e0;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-label {
    font-size: 14px;
    color: #666;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    flex: 0 0 45%;
}

.summary-value {
    font-size: 15px;
    color: #222;
    font-weight: 500;
    text-align: right;
    flex: 0 0 50%;
    word-break: break-word;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    justify-content: flex-end;
}

/* Hover effect on rows */
.summary-row:hover {
    background-color: #f5f5f5;
    margin: 0 -5px;
    padding: 10px 5px;
    border-radius: 4px;
}

/* ===== INSTALLATION ROW ===== */
.summary-row#summary-installation-row {
    border-top: 2px solid #cbbfa9;
    margin-top: 5px;
    padding-top: 10px;
}

.summary-row#summary-installation-row .summary-label {
    color: #333;
    font-weight: 600;
}

/* ===== DELIVERY ROW ===== */
.summary-row#summary-delivery-row {
    border-top: 2px solid #cbbfa9;
    margin-top: 5px;
    padding-top: 15px;
    display: flex !important;
    flex-direction: row !important;
    justify-content: space-between !important;
    align-items: baseline !important;
    width: 100%;
}

.summary-row#summary-delivery-row .summary-label {
    flex: 0 0 45% !important;
    font-size: 14px;
    color: #666;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.summary-row#summary-delivery-row .summary-value {
    flex: 0 0 50% !important;
    font-size: 15px;
    color: #222;
    font-weight: 500;
    text-align: right !important;
    word-break: break-word;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    justify-content: flex-end !important;
    white-space: normal;
    line-height: 1.4;
}

.summary-row#summary-delivery-row .summary-value .price-vat {
    font-size: 10px;
    font-weight: 400;
    color: #666;
    margin-left: 2px;
    vertical-align: baseline;
    white-space: nowrap;
}

.summary-row#summary-delivery-row.bespoke-delivery .summary-value {
    color: #d32f2f;
    font-weight: 600;
}

/* ===== SUBMIT BUTTON ===== */
.submit-container {
    margin-top: 40px;
    text-align: left;
    position: relative;
    z-index: 2;
}

.submit-btn {
    background: #0CBB07;
    color: white;
    border: none;
    padding: 18px 50px;
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(203, 191, 169, 0.3);
    min-width: 350px;
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-bottom: 0;
}

.submit-btn:hover:not(:disabled) {
    background: #0A9E05;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(203, 191, 169, 0.4);
}

.submit-btn:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 4px 15px rgba(203, 191, 169, 0.3);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.submit-btn.add-to-cart-btn {
    background: #0CBB07;
}

/* Button loading state */
.submit-btn.loading {
    opacity: 0.8;
    cursor: not-allowed;
}

.submit-btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 2px solid transparent;
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Loading spinner for button text */
.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 0.8s linear infinite;
    margin-right: 8px;
    vertical-align: middle;
}

/* ===== HIDDEN FIELDS ===== */
input[type="hidden"] {
    display: none;
}

/* ===== RESPONSIVE DESIGN ===== */

/* Tablet */
@media (max-width: 1024px) {
    .summary-container {
        padding: 30px 20px 100px 20px;
    }
    
    .summary-title {
        font-size: 28px;
    }
    
    .summary-price {
        font-size: 24px;
        padding: 6px 16px;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
        gap: 0;
        padding: 20px;
    }
    
    .summary-row {
        padding: 8px 0;
    }
    
    .submit-btn {
        min-width: 300px;
        padding: 16px 40px;
    }
    
    .price-vat {
        font-size: 12px;
    }
}

/* Mobile Landscape */
@media (max-width: 768px) {
    .summary-container {
        padding: 25px 15px 100px 15px;
    }
    
    .summary-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .summary-title {
        font-size: 24px;
    }
    
    .summary-price {
        font-size: 22px;
        padding: 5px 15px;
    }
    
    .summary-grid {
        padding: 15px;
        border-radius: 8px;
    }
    
    .summary-label {
        font-size: 13px;
        flex: 0 0 45%;
    }
    
    .summary-value {
        font-size: 14px;
        flex: 0 0 50%;
    }
    
    .submit-btn {
        min-width: 280px;
        padding: 14px 30px;
        font-size: 15px;
    }
    
    .price-vat {
        font-size: 11px;
    }
    
    /* Installation row mobile fix */
    .summary-row#summary-installation-row {
        flex-direction: row !important;
        padding: 8px 0;
    }
    
    .summary-row#summary-installation-row .summary-label {
        flex: 0 0 45% !important;
        font-size: 13px;
    }
    
    .summary-row#summary-installation-row .summary-value {
        flex: 0 0 50% !important;
        font-size: 14px;
        text-align: right !important;
        justify-content: flex-end !important;
    }
    
    /* Delivery row mobile fix */
    .summary-row#summary-delivery-row {
        flex-direction: row !important;
        padding: 8px 0;
    }
    
    .summary-row#summary-delivery-row .summary-label {
        flex: 0 0 45% !important;
        font-size: 13px;
    }
    
    .summary-row#summary-delivery-row .summary-value {
        flex: 0 0 50% !important;
        font-size: 14px;
        text-align: right !important;
        justify-content: flex-end !important;
    }
}

/* Mobile Portrait */
@media (max-width: 480px) {
    .summary-title {
        font-size: 22px;
    }
    
    .summary-price {
        font-size: 20px;
    }
    
    .summary-grid {
        padding: 12px;
    }
    
    .summary-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
        padding: 10px 0;
    }
    
    .summary-label {
        flex: none;
        width: 100%;
        font-size: 12px;
        margin-bottom: 2px;
    }
    
    .summary-value {
        flex: none;
        width: 100%;
        text-align: left;
        font-size: 13px;
        padding-left: 10px;
        border-left: 2px solid #cbbfa9;
        justify-content: flex-start;
    }
    
    .submit-btn {
        min-width: 100%;
        padding: 14px 20px;
        font-size: 14px;
    }
    
    .price-vat {
        font-size: 10px;
    }
    
    /* Installation row mobile portrait fix */
    .summary-row#summary-installation-row {
        flex-direction: row !important;
        align-items: flex-start !important;
        gap: 5px;
        padding: 10px 0;
    }
    
    .summary-row#summary-installation-row .summary-label {
        flex: 0 0 45% !important;
        font-size: 12px;
        margin-bottom: 0 !important;
    }
    
    .summary-row#summary-installation-row .summary-value {
        flex: 0 0 50% !important;
        font-size: 13px;
        text-align: right !important;
        padding-left: 0 !important;
        border-left: none !important;
        justify-content: flex-end !important;
    }
    
    /* Delivery row mobile portrait fix */
    .summary-row#summary-delivery-row {
        flex-direction: row !important;
        align-items: flex-start !important;
        gap: 5px;
        padding: 10px 0;
    }
    
    .summary-row#summary-delivery-row .summary-label {
        flex: 0 0 45% !important;
        font-size: 12px;
        margin-bottom: 0 !important;
    }
    
    .summary-row#summary-delivery-row .summary-value {
        flex: 0 0 50% !important;
        font-size: 13px;
        text-align: right !important;
        padding-left: 0 !important;
        border-left: none !important;
        justify-content: flex-end !important;
    }
}

/* Extra small mobile */
@media (max-width: 360px) {
    .summary-row#summary-installation-row,
    .summary-row#summary-delivery-row {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 5px;
    }
    
    .summary-row#summary-installation-row .summary-label,
    .summary-row#summary-delivery-row .summary-label {
        flex: none !important;
        width: 100% !important;
        margin-bottom: 2px;
    }
    
    .summary-row#summary-installation-row .summary-value,
    .summary-row#summary-delivery-row .summary-value {
        flex: none !important;
        width: 100% !important;
        text-align: left !important;
        padding-left: 10px;
        border-left: 2px solid #cbbfa9;
        justify-content: flex-start !important;
    }
}

/* ===== DARK MODE SUPPORT ===== */
@media (prefers-color-scheme: dark) {
    .summary-container {
        background: #1e1e1e;
    }
    
    .summary-title {
        color: #ffffff;
    }
    
    .summary-price {
        background: #2d2d2d;
        color: #cbbfa9;
    }
    
    .summary-price span {
        color: #ffffff;
    }
    
    .summary-grid {
        background: #2a2a2a;
        border-color: #404040;
    }
    
    .summary-row {
        border-bottom-color: #404040;
    }
    
    .summary-row:hover {
        background-color: #333333;
    }
    
    .summary-label {
        color: #b0b0b0;
    }
    
    .summary-value {
        color: #e0e0e0;
    }
    
    .summary-divider {
        border-top-color: #404040;
    }
    
    .price-vat {
        color: #b0b0b0;
    }
}

/* ===== ANIMATIONS ===== */
.summary-row {
    transition: all 0.2s ease;
}

.submit-btn {
    transition: all 0.3s ease;
}

/* ===== PRINT STYLES ===== */
@media print {
    .submit-container {
        display: none;
    }
    
    .summary-container {
        padding: 20px;
    }
    
    .summary-grid {
        break-inside: avoid;
        background: none;
        border: 1px solid #ccc;
    }
}
</style>

<script>

jQuery(document).ready(function($) {
    
    function isDev() {
        return window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    }

    let isSubmitting = false;

    // Helper: get pane count
    function getWindowCount() {
        return window.getWindowPaneCount ? window.getWindowPaneCount() : 1;
    }

    // ===== INSTALLATION DISPLAY =====
    function getInstallationDisplay() {
        const val = $('input[name="window_installation_type"]:checked').val();
        if (!val) return '—';
        const cnt = getWindowCount();
        const map = {
            'collection': 'Supply Only – Collection',
            'delivery': 'Supply Only – Delivery',
            'prepared_opening': 'Installed into Prepared Opening',
            'remove_existing': 'Remove Existing & Install'
        };
        let text = map[val] || val;
        if (val === 'prepared_opening') text += ' (+£' + (cnt*200) + ')';
        else if (val === 'remove_existing') text += ' (+£' + ((cnt*200)+550) + ')';
        else if (val === 'delivery') {
            const dp = parseFloat($('#delivery_price').val()) || 0;
            if (dp > 0) text += ' (+£' + dp.toFixed(2) + ')';
            else if (dp === 0 && $('#delivery_bespoke').val() !== '1') text += ' (FREE)';
        }
        return text;
    }

    // ===== DELIVERY INFO =====
    function getDeliveryInfo() {
        let info = { display: '—', price: 0, zone: '', distance: '', isBespoke: false };
        if (window.deliveryData) {
            if (window.deliveryData.bespoke) {
                info.isBespoke = true;
                info.display = 'Bespoke (call for quote)';
            } else {
                const price = parseFloat(window.deliveryData.price) || 0;
                info.price = price;
                const priceText = price === 0 ? 'FREE' : '£' + price.toFixed(2);
                const zone = window.deliveryData.zone || '';
                const distance = window.deliveryData.distance ? parseFloat(window.deliveryData.distance).toFixed(1) + ' miles' : '';
                info.zone = zone; info.distance = distance;
                if (zone && distance) info.display = priceText + ' (' + zone + ' - ' + distance + ', <span class="price-vat">INC. VAT</span>)';
                else if (zone) info.display = priceText + ' (' + zone + ', <span class="price-vat">INC. VAT</span>)';
                else info.display = priceText + ' <span class="price-vat">(INC. VAT)</span>';
            }
        } else {
            const price = parseFloat($('#delivery_price').val()) || 0;
            const isBespoke = $('#delivery_bespoke').val() === '1';
            if (isBespoke) { info.isBespoke = true; info.display = 'Bespoke (call for quote)'; }
            else {
                info.price = price;
                const priceText = price === 0 ? 'FREE' : '£' + price.toFixed(2);
                info.display = priceText + ' <span class="price-vat">(INC. VAT)</span>';
            }
        }
        return info;
    }

    // ===== POPULATE SUMMARY =====
    window.populateStep14 = function() {
        // Size
        const w = $('#window_width').val() || '';
        const h = $('#window_height').val() || '';
        $('#summary-size').text(w && h ? w + ' x ' + h : '—');
        $('#summary_size_field').val(w && h ? w + ' x ' + h : '—');

        // Panels
        const panelVal = $('input[name="window_panel_layout"]:checked').val();
        let panelText = '—';
        if (panelVal) {
            panelText = $('input[name="window_panel_layout"]:checked').closest('label').find('.option-name').text().trim();
            if (!panelText) {
                const map = { '2_left':'2 Panels Left','2_right':'2 Panels Right','3_left':'3 Panels Left','3_right':'3 Panels Right','4_left':'4 Panels Left','4_right':'4 Panels Right','5_left':'5 Panels Left','5_right':'5 Panels Right' };
                panelText = map[panelVal] || panelVal;
            }
        }
        $('#summary-panels').text(panelText);
        $('#summary_panels_field').val(panelText);

        // Opening Direction
        const openDir = $('input[name="open_direction"]:checked').val();
        const openText = openDir ? (openDir === 'inwards' ? 'Inwards' : 'Outwards') : '—';
        $('#summary-opening').text(openText);
        $('#summary_opening_field').val(openText);

        // Outside Colour
        const outCol = $('input[name="window_colour"]:checked').val();
        const outRal = $('#custom_colour_select').val();
        let outText = '—';
        const colourNames = { 'white':'White','anthracite_grey':'Anthracite Grey','black':'Black' };
        if (outCol === 'custom_ral' && outRal) outText = outRal;
        else if (outCol && outCol !== 'custom_ral') outText = colourNames[outCol] || outCol;
        else if (outCol) outText = colourNames[outCol] || outCol;
        if (outCol === 'white') outText += ' (RAL 9016)';
        else if (outCol === 'anthracite_grey') outText += ' (RAL 7016)';
        else if (outCol === 'black') outText += ' (RAL 9005)';
        $('#summary-outside-colour').text(outText);
        $('#summary_outside_colour_field').val(outText);

        // Inside Colour
        const inCol = $('input[name="inside_colour"]:checked').val();
        const inRal = $('#custom_inside_colour_select').val();
        let inText = '—';
        if (inCol === 'custom_ral' && inRal) inText = inRal;
        else if (inCol && inCol !== 'custom_ral') inText = colourNames[inCol] || inCol;
        if (inCol === 'white') inText += ' (RAL 9016)';
        else if (inCol === 'anthracite_grey') inText += ' (RAL 7016)';
        else if (inCol === 'black') inText += ' (RAL 9005)';
        $('#summary-inside-colour').text(inText);
        $('#summary_inside_colour_field').val(inText);

        // Handle
        const hCol = $('input[name="handle_colour"]:checked').val();
        const hMap = { 'white':'White','chrome':'Chrome','black':'Black','black_white':'Black and White' };
        $('#summary-handle-colour').text(hMap[hCol] || hCol || '—');
        $('#summary_handle_colour_field').val(hMap[hCol] || hCol || '—');

        // Glass
        const gl = $('input[name="glass_upgrade"]:checked').val();
        let glText = '—';
        if (gl === 'no_thanks') glText = 'Standard Glass';
        else if (gl) {
            glText = $('input[name="glass_upgrade"]:checked').closest('.glass-option-card').find('.option-name').text().trim() ||
                     ({ 'self_cleaning':'Self-cleaning glass','integral_blinds':'Integral blinds','obscure_glass':'Obscure glass','saint_gobain_12':'Saint-Gobain Planitherm 1.2' }[gl] || gl);
        }
        $('#summary-glass').text(glText);
        $('#summary_glass_field').val(glText);

        // Trickle Vents
        const tv = $('input[name="trickle_vents"]:checked').val();
        $('#summary-trickle-vents').text(tv === 'yes_trickle' ? 'Yes, Add Trickle Vent' : 'No');
        $('#summary_trickle_vents_field').val(tv === 'yes_trickle' ? 'Yes, Add Trickle Vent' : 'No');

        // Cill
        const cill = $('input[name="cill"]:checked').val();
        const cillMap = { 'none':'No Cill','150mm-aluminium-cill':'150mm Aluminium Cill','150mm-upvc-cill':'150mm uPVC Cill' };
        $('#summary-cill').text(cillMap[cill] || cill || '—');
        $('#summary_cill_field').val(cillMap[cill] || cill || '—');

        // Postcode
        const pc = $('#postcode').val() || '—';
        $('#summary-postcode').text(pc);
        $('#summary_postcode_field').val(pc);

        // Installation
        const instDisplay = getInstallationDisplay();
        $('#summary-installation').text(instDisplay);
        $('#summary_installation_field').val(instDisplay);

        // Access
        const acc = $('input[name="access_issues"]:checked').val();
        let accText = 'No';
        if (acc === 'yes_access') {
            const desc = $('#access_description').val().trim();
            accText = desc ? 'Yes, ' + desc : 'Yes (please describe)';
        }
        $('#summary-access').text(accText);
        $('#summary_access_field').val(accText);

        // Delivery Row
        if ($('#summary-delivery-row').length === 0) {
            $('<div class="summary-row" id="summary-delivery-row"><span class="summary-label">Delivery:</span><span class="summary-value" id="summary-delivery">—</span></div>').insertAfter('#summary-installation-row');
        }
        const delInfo = getDeliveryInfo();
        $('#summary-delivery').html(delInfo.display);
        $('#summary-delivery-row').toggleClass('bespoke-delivery', delInfo.isBespoke);

        // Total Price – use the main JS's last calculated value
        const total = window.lastCalculatedPrice || '0.00';
        $('#summary-total-price').text(total);
        $('#submit-price').html('£' + total + ' <span class="price-vat">(inc. VAT)</span>');
        $('#summary_total_price_field').val(total);
    };

    // ===== SUBMISSION =====
    window.submitBuilderForm = function() {
        if (isSubmitting) return;
        const isBespoke = (window.deliveryData && window.deliveryData.bespoke) || $('#delivery_bespoke').val() === '1';
        if (isBespoke) { alert('Bespoke delivery required. Please call our sales team.'); return; }

        isSubmitting = true;
        const $btn = $('#submit-btn');
        $btn.prop('disabled', true).addClass('loading');
        $btn.html('<span class="loading-spinner"></span> Submitting…');

        window.populateStep14();
        const formData = $('#window-builder-form').serialize();
        let data = formData + '&builder_checkout=0';
        if (window.editMode) {
            const cartKey = window.editCartKey || $('#cart_item_key_field').val();
            if (!cartKey) { alert('Cart key missing'); resetSubmitButton(); return; }
            data += '&edit_mode=1&cart_item_key=' + encodeURIComponent(cartKey);
        }

        $.ajax({
            url: windowBuilderData.ajaxUrl,       // <-- corrected global
            type: 'POST',
            data: {
                action: 'process_window_builder', // your AJAX action
                form_data: data,
                security: windowBuilderData.nonce
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = windowBuilderData.cartUrl;
                } else {
                    alert('Error: ' + (response.data?.message || 'Unknown error'));
                    resetSubmitButton();
                }
            },
            error: function() { alert('Network error'); resetSubmitButton(); }
        });
    };

    function resetSubmitButton() {
        isSubmitting = false;
        const btn = $('#submit-btn');
        btn.prop('disabled', false).removeClass('loading');
        const total = $('#summary-total-price').text() || '0.00';
        btn.html((window.editMode ? 'UPDATE CART' : 'ADD TO CART') + ' - £' + total + ' <span class="price-vat">(inc. VAT)</span>');
    }

    // Events
    $(document).on('stepChanged', function(e, idx) {
        if (idx === 13) setTimeout(window.populateStep14, 200);
    });
    $('#submit-btn').on('click', function(e) { e.preventDefault(); window.submitBuilderForm(); });
    $(document).on('deliveryDataUpdated', function() {
        if ($('.wizard-step.active').data('step') == 14) window.populateStep14();
    });
});

</script>