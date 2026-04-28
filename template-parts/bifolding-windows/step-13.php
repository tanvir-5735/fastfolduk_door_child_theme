<?php
/**
 * Template part for Customer Information Step in Window Builder
 * Step 13: Complete the form to view your window quote
 *
 * @package Astra Child
 */
?>

<!-- Step 13: Customer Information Form -->
<div class="wizard-step" data-step="13">
    <div class="step-container customer-container">
        
        <!-- Header with title -->
        <div class="step-title customer-header">
            <h2 class="customer-title">Complete the form to view your window quote</h2>
        </div>

        <!-- Features with tickboxes (checkboxes) -->
        <div class="customer-features">
            <div class="feature-item">
                <label class="feature-checkbox-label">
                    <input type="checkbox" class="feature-checkbox" checked disabled>
                    <span class="feature-checkmark"></span>
                    <span class="feature-text">10 year guarantee</span>
                </label>
            </div>
            <div class="feature-item">
                <label class="feature-checkbox-label">
                    <input type="checkbox" class="feature-checkbox" checked disabled>
                    <span class="feature-checkmark"></span>
                    <span class="feature-text">Download your quote</span>
                </label>
            </div>
            <div class="feature-item">
                <label class="feature-checkbox-label">
                    <input type="checkbox" class="feature-checkbox" checked disabled>
                    <span class="feature-checkmark"></span>
                    <span class="feature-text">Secure checkout</span>
                </label>
            </div>
        </div>

        <!-- Horizontal Divider -->
        <hr class="customer-divider">

        <!-- Customer Form - Two column layout -->
        <div class="customer-form-wrapper">
            <form class="customer-form" id="customer-info-form-windows">
                
                <!-- First Row: First Name and Last Name side by side -->
                <div class="form-row">
                    <div class="form-field">
                        <label for="first_name">FIRST NAME</label>
                        <input 
                            type="text" 
                            name="first_name" 
                            id="first_name" 
                            class="customer-input" 
                            placeholder="John" 
                            value="" 
                            required
                        >
                    </div>

                    <div class="form-field">
                        <label for="last_name">LAST NAME</label>
                        <input 
                            type="text" 
                            name="last_name" 
                            id="last_name" 
                            class="customer-input" 
                            placeholder="Smith" 
                            value="" 
                            required
                        >
                    </div>
                </div>

                <!-- Second Row: Mobile Number and Email side by side -->
                <div class="form-row">
                    <div class="form-field">
                        <label for="mobile_number">MOBILE NUMBER</label>
                        <input 
                            type="tel" 
                            name="mobile_number" 
                            id="mobile_number" 
                            class="customer-input" 
                            placeholder="e.g. 07935566384" 
                            value="" 
                            required
                        >
                    </div>

                    <div class="form-field">
                        <label for="email_address">EMAIL ADDRESS</label>
                        <input 
                            type="email" 
                            name="email_address" 
                            id="email_address" 
                            class="customer-input" 
                            placeholder="sales@fastfolduk.co.uk" 
                            value="" 
                            required
                        >
                    </div>
                </div>

                <!-- Marketing Consent Text (no checkbox) -->
                <div class="form-consent">
                    <p class="consent-text">By completing this form you agree to receive marketing communications</p>
                </div>

                <!-- Hidden field to store form completion status -->
                <input type="hidden" name="customer_info_complete" id="customer_info_complete" value="0">
            </form>
        </div>

    </div>
</div>

<style>
/* ================================
   Customer Information Step Styles
   ================================ */

.customer-container {
    max-width: 1400px;
    padding: 20px;
}

.customer-header {
    text-align: left;
    margin-bottom: 30px;
}

.customer-title {
    font-size: 32px;
    color: #222;
    font-weight: 600;
    margin: 0;
    line-height: 1.3;
}

.customer-features {
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
    margin: 0 0 30px 0;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.feature-checkbox {
    color: #0CBB07;
    font-size: 20px;
    font-weight: normal;
}

.feature-text {
    font-size: 16px;
    color: #FFF;
    font-weight: 500;
}

.customer-divider {
    border: none;
    border-top: 1px solid #e0e0e0;
    margin: 30px 0;
}

.customer-form-wrapper {
    max-width: 100%;
    margin: 0;
}

.customer-form {
    width: 100%;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 25px;
}

.form-field {
    display: flex;
    flex-direction: column;
}

.form-field label {
    font-size: 14px;
    font-weight: 600;
    color: #FFF;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.customer-input {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: none;
    border-bottom: 1px solid #ddd;
    color: #000;
    outline: none;
    transition: all 0.25s ease;
}

.customer-input:hover {
    border-bottom-color: #9c7b4b;
}

.customer-input:focus {
    border-bottom-color: #9c7b4b;
    box-shadow: none;
}

.customer-input::placeholder {
    color: #aaa;
    font-weight: 400;
    opacity: 1;
}

.form-consent {
    margin-top: 30px;
}

.consent-text {
    font-size: 14px;
    color: #FFF;
    line-height: 1.5;
    margin: 0;
}

.customer-input.error {
    border-bottom-color: #dc3545;
}

@media (max-width: 768px) {
    .customer-container {
        padding: 30px 15px 40px;
    }
    .customer-title {
        font-size: 26px;
    }
    .customer-features {
        gap: 20px;
        flex-direction: column;
    }
    .feature-item {
        width: 100%;
    }
    .form-row {
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    .customer-input {
        font-size: 15px;
    }
    .consent-text {
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .customer-title {
        font-size: 24px;
    }
    .feature-text {
        font-size: 14px;
    }
    .form-field label {
        font-size: 13px;
    }
}
</style>

<script>
/**
 * Step 13 – Customer Information Form (Windows Builder)
 * Saves window price when entering the previous step,
 * restores it when this step becomes active.
 */
jQuery(document).ready(function($) {
    
    function getCurrentStep() {
        if (typeof window.currentStep !== 'undefined') return window.currentStep;
        return $('.wizard-step.active').index();
    }

    function isCustomerFormComplete() {
        const firstName = $('#first_name').val().trim();
        const lastName = $('#last_name').val().trim();
        const mobile = $('#mobile_number').val().trim();
        const email = $('#email_address').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const mobileRegex = /^[0-9]{10,11}$/;
        return firstName && lastName && mobile && email &&
               emailRegex.test(email) && mobileRegex.test(mobile.replace(/\s/g, ''));
    }

    function updateNextButtonState() {
        if (getCurrentStep() !== 12) return;
        const isComplete = isCustomerFormComplete();
        const $nextBtn = $('.next-step');
        if (isComplete) {
            $nextBtn.removeClass('inactive').prop('disabled', false);
            $('#customer_info_complete').val('1');
        } else {
            $nextBtn.addClass('inactive').prop('disabled', true);
            $('#customer_info_complete').val('0');
        }
        // Also trigger main wizard validation to sync
        if (typeof validateCurrentStep === 'function') validateCurrentStep();
    }

    // Listen to inputs
    $('#first_name, #last_name, #mobile_number, #email_address').on('input', updateNextButtonState);

    // Initial check
    setTimeout(function() {
        if (getCurrentStep() === 12) updateNextButtonState();
    }, 300);

    // On entering this step
    $(document).on('stepChanged', function(e, stepIndex) {
        if (stepIndex === 12) updateNextButtonState();
    });
});
</script>