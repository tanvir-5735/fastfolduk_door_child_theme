<?php
/**
 * Template part for Postcode / Zip Code Step in Window Builder
 * Step 10: What's your postcode? (with delivery calculation)
 *
 * @package Astra Child
 */
?>

<!-- Step 10: Postcode Input with Delivery Calculator -->
<div class="wizard-step" data-step="10">
    <div class="step-container postcode-container">
        <div class="step-title postcode-title">
            <h2>What's your postcode?</h2>
            <p class="postcode-description">Please supply your full postcode for delivery calculation</p>
        </div>
       
        <div class="postcode-input-wrapper">
            <input 
                type="text" 
                name="postcode" 
                id="postcode" 
                class="postcode-input" 
                placeholder="SW41AB" 
                value="" 
                maxlength="8"
                autocomplete="postal-code"
            >
            
            <!-- Delivery preview container -->
            <div id="delivery-preview-container" class="delivery-preview" style="display: none;"></div>
            
            <!-- Hidden fields to store delivery data -->
            <input type="hidden" name="delivery_price" id="delivery_price" value="0">
            <input type="hidden" name="delivery_zone" id="delivery_zone" value="">
            <input type="hidden" name="delivery_distance" id="delivery_distance" value="0">
            <input type="hidden" name="delivery_bespoke" id="delivery_bespoke" value="0">
        </div>
    </div>
</div>

<style>
/* ================================
   Postcode Step Styles (same as door)
   ================================ */
.postcode-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px 20px 60px;
}

.postcode-title {
    text-align: center;
    margin-bottom: 40px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.postcode-title h2 {
    font-size: 32px;
    color: #222;
    font-weight: 600;
    margin: 0 0 10px 0;
    line-height: 1.3;
}

.postcode-description {
    font-size: 18px;
    color: #666;
    margin: 0;
    line-height: 1.5;
}

.postcode-input-wrapper {
    max-width: 500px;
    margin: 0 auto;
}

.postcode-input {
    width: 100%;
    padding: 18px 20px;
    font-size: 18px;
    border: 1px solid #9f988c;
    background: #f7f5ef;
    color: #333;
    outline: none;
    transition: all 0.25s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.postcode-input:hover {
    border-color: #9c7b4b;
}

.postcode-input:focus {
    border-color: #9c7b4b;
    box-shadow: 0 0 0 3px rgba(156, 123, 75, 0.15);
}

.postcode-input::placeholder {
    color: #aaa;
    text-transform: none;
    font-weight: 400;
    opacity: 1;
}

.delivery-preview {
    margin-top: 25px;
    padding: 20px;
    background: #ffffff;
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    font-size: 15px;
}

.delivery-preview .loading {
    color: #666;
    font-style: italic;
    text-align: center;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 8px;
}

.delivery-result {
    padding: 15px;
    border-radius: 8px;
}

.delivery-result.free-delivery {
    background: #e8f5e8;
    border-left: 4px solid #4caf50;
}

.delivery-result.paid-delivery {
    background: #f8f8f8;
    border-left: 4px solid #cbbfa9;
}

.delivery-result > div {
    margin: 8px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.delivery-result .delivery-zone {
    font-weight: 600;
    color: #1a1a1a;
    border-bottom: 1px dashed #ddd;
    padding-bottom: 8px;
    margin-bottom: 8px;
}

.delivery-result .delivery-price {
    font-weight: 700;
    color: #1a1a1a;
    font-size: 18px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 2px solid #e0e0e0;
}

.vat-text {
    font-size: 10px;
    font-weight: 400;
    color: #666;
    margin-left: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    vertical-align: super;
    white-space: nowrap;
}

.delivery-result.free-delivery .delivery-price {
    color: #2e7d32;
}

.bespoke-warning {
    padding: 20px;
    background: #fff3e0;
    border-left: 4px solid #ff9800;
    border-radius: 8px;
    color: #333;
}

.bespoke-warning strong {
    color: #d32f2f;
    display: block;
    margin-bottom: 12px;
    font-size: 18px;
}

.bespoke-warning .contact-info {
    background: #fff;
    padding: 12px 15px;
    border-radius: 6px;
    margin-top: 10px;
}

.delivery-error {
    padding: 15px;
    background: #ffebee;
    border-left: 4px solid #f44336;
    border-radius: 8px;
    color: #c62828;
}

@media (max-width: 768px) {
    .postcode-container {
        padding: 30px 15px 40px;
    }
    .postcode-title h2 {
        font-size: 26px;
    }
    .postcode-description {
        font-size: 16px;
    }
    .postcode-input {
        font-size: 16px;
        padding: 15px 18px;
    }
    .delivery-result > div {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    .vat-text {
        font-size: 8px;
    }
}

@media (max-width: 480px) {
    .vat-text {
        font-size: 7px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    
    let isAjaxInProgress = false;
    let lastCheckedPostcode = '';
    let debounceTimer = null;
    
    // ===== FIXED: use the global windowBuilderData object =====
    const ajaxUrl = windowBuilderData.ajaxUrl;
    const nonce   = windowBuilderData.nonce;
    
    function triggerPriceUpdate() {
        if (typeof window.updatePrice === 'function') {
            window.updatePrice();
        }
        if (typeof window.updateDrawer === 'function') {
            window.updateDrawer();
        }
        if (typeof window.updateSummary === 'function') {
            window.updateSummary();
        }
        // For window builder, if there's a step 14 summary, use populateStep14
        if (typeof window.populateStep14 === 'function') {
            window.populateStep14();
        }
        $(document).trigger('deliveryDataUpdated');
        $(document).trigger('priceUpdated');
        
        if (window.isDev && window.isDev()) {
            console.log('Window delivery price update triggered');
        }
    }
    
    function checkDelivery(postcode) {
        if (postcode === lastCheckedPostcode && !isAjaxInProgress) {
            return;
        }
        
        const $previewContainer = $('#delivery-preview-container');
        const $nextBtn = $('.next-step');
        const $nextFooterBtn = $('.next-footer-btn');
        
        $previewContainer.empty().show();
        $previewContainer.html('<div class="loading">Checking delivery for ' + postcode + '...</div>');
        
        isAjaxInProgress = true;
        lastCheckedPostcode = postcode;
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'check_delivery',
                postcode: postcode,
                security: nonce
            },
            success: function(response) {
                $previewContainer.empty();
                
                if (response.success) {
                    const data = response.data;
                    window.deliveryData = data;
                    
                    $('#delivery_price').val(data.price);
                    $('#delivery_zone').val(data.zone);
                    $('#delivery_distance').val(data.distance);
                    $('#delivery_bespoke').val(data.bespoke ? '1' : '0');
                    
                    if (data.bespoke) {
                        const message = data.message || 'Bespoke delivery required for ' + data.zone;
                        $previewContainer.html(
                            '<div class="bespoke-warning">' +
                            '<strong>⚠️ Bespoke Delivery Required</strong>' +
                            '<div>' + message + '</div>' +
                            '<div class="contact-info">📞 Call us: <strong>01234 567890</strong></div>' +
                            '</div>'
                        );
                        $nextBtn.addClass('inactive').prop('disabled', true);
                        if ($nextFooterBtn.length) $nextFooterBtn.addClass('inactive').prop('disabled', true);
                        triggerPriceUpdate();
                    } else {
                        const priceText = data.price === 0 ? 'FREE' : '£' + parseFloat(data.price).toFixed(2) + ' <span class="vat-text">(inc. VAT)</span>';
                        const zoneClass = data.price === 0 ? 'free-delivery' : 'paid-delivery';
                        const distanceDisplay = data.distance ? parseFloat(data.distance).toFixed(1) + ' miles' : '—';
                        let deliveryDays = '';
                        if (data.price === 0) deliveryDays = '(2-3 working days)';
                        else if (data.price <= 150) deliveryDays = '(3-5 working days)';
                        else if (data.price <= 250) deliveryDays = '(5-7 working days)';
                        else deliveryDays = '(7-10 working days)';
                        
                        $previewContainer.html(
                            '<div class="delivery-result ' + zoneClass + '">' +
                            '<div class="delivery-zone">📍 Zone: <strong>' + data.zone + '</strong></div>' +
                            '<div class="delivery-distance">📏 Distance: <strong>' + distanceDisplay + '</strong></div>' +
                            '<div class="delivery-price">🚚 Delivery Cost: <strong>' + priceText + '</strong> ' + deliveryDays + '</div>' +
                            '</div>'
                        );
                        $nextBtn.removeClass('inactive').prop('disabled', false);
                        if ($nextFooterBtn.length) $nextFooterBtn.removeClass('inactive').prop('disabled', false);
                        triggerPriceUpdate();
                    }
                } else {
                    $previewContainer.html('<div class="delivery-error">❌ ' + (response.data.message || 'Error checking postcode') + '</div>');
                    $nextBtn.addClass('inactive').prop('disabled', true);
                    if ($nextFooterBtn.length) $nextFooterBtn.addClass('inactive').prop('disabled', true);
                    window.deliveryData = null;
                    $('#delivery_price').val('0');
                    $('#delivery_zone').val('');
                    $('#delivery_distance').val('0');
                    $('#delivery_bespoke').val('0');
                    triggerPriceUpdate();
                }
            },
            error: function() {
                $previewContainer.html('<div class="delivery-error">❌ Network error. Please try again.</div>');
                $nextBtn.addClass('inactive').prop('disabled', true);
                if ($nextFooterBtn.length) $nextFooterBtn.addClass('inactive').prop('disabled', true);
                window.deliveryData = null;
                if (window.isDev && window.isDev()) console.log('AJAX Error');
            },
            complete: function() {
                isAjaxInProgress = false;
            }
        });
    }
    
    $('#postcode').on('input', function() {
        let val = $(this).val().toUpperCase().replace(/\s+/g, '');
        $(this).val(val);
        if (debounceTimer) clearTimeout(debounceTimer);
        if (val.length === 0) {
            $('#delivery-preview-container').fadeOut();
            $('.next-step, .next-footer-btn').addClass('inactive').prop('disabled', true);
            window.deliveryData = null;
            $('#delivery_price').val('0');
            $('#delivery_zone').val('');
            $('#delivery_distance').val('0');
            $('#delivery_bespoke').val('0');
            triggerPriceUpdate();
            return;
        }
        debounceTimer = setTimeout(function() {
            checkDelivery(val);
        }, 800);
    });
    
    $('#postcode').on('blur', function() {
        let val = $(this).val().trim().toUpperCase().replace(/\s+/g, '');
        if (val.length > 3 && val.length <= 7) {
            val = val.substring(0, val.length - 3) + ' ' + val.substring(val.length - 3);
        } else if (val.length === 7) {
            val = val.substring(0, val.length - 3) + ' ' + val.substring(val.length - 3);
        }
        if ($(this).val() !== val) $(this).val(val);
    });
    
    const initialPostcode = $('#postcode').val().trim();
    if (initialPostcode.length > 0) {
        setTimeout(function() {
            checkDelivery(initialPostcode.replace(/\s+/g, ''));
        }, 500);
    }
    
    if (window.editMode && window.editData && window.editData.postcode) {
        $('#postcode').val(window.editData.postcode);
        setTimeout(function() {
            $('#postcode').trigger('input');
        }, 1000);
    }
    
    window.getDeliveryData = function() {
        return window.deliveryData || {
            price: parseFloat($('#delivery_price').val()) || 0,
            zone: $('#delivery_zone').val() || '',
            distance: parseFloat($('#delivery_distance').val()) || 0,
            bespoke: $('#delivery_bespoke').val() === '1'
        };
    };
    
    window.getDeliveryCost = function() {
        const data = window.getDeliveryData();
        if (data.bespoke) return 'Bespoke (call for quote)';
        if (data.price === 0) return 'FREE';
        return '£' + data.price.toFixed(2) + ' (inc. VAT)';
    };
    
    window.getDeliveryPrice = function() {
        const data = window.getDeliveryData();
        return data.bespoke ? 0 : data.price;
    };
    
    window.isDeliveryAllowed = function() { return !window.getDeliveryData().bespoke; };
    window.isBespokeDelivery = function() { return window.getDeliveryData().bespoke; };
    
    window.isDev = function() {
        return window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    };
    
    if (window.isDev()) console.log('Window Step 10 (Postcode) initialized');
});
</script>