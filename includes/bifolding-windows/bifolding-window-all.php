<?php
/**
 * Bifolding Window - All Functionality
 *
 * This file contains all window builder related functions:
 * - Configuration mappings
 * - Remove default WooCommerce add-to-cart button
 * - Add wizard data to cart items
 * - Delivery calculation AJAX handler
 * - Window builder submission AJAX handler (Step 14)
 * - Custom price override
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================
 * PANEL MAPPINGS & CONFIGURATIONS FOR WINDOWS
 * ============================================================
 */

$bifolding_window_panel_map = array(
    '2_left'  => '2 Panels Left',
    '2_right' => '2 Panels Right',
    '3_left'  => '3 Panels Left',
    '3_right' => '3 Panels Right',
    '4_left'  => '4 Panels Left',
    '4_right' => '4 Panels Right',
    '5_left'  => '5 Panels Left',
    '5_right' => '5 Panels Right',
);

$bifolding_window_colour_map = array(
    'anthracite_grey' => array('name' => 'Anthracite Grey', 'ral' => '7016'),
    'black'           => array('name' => 'Black', 'ral' => '9005'),
    'white'           => array('name' => 'White', 'ral' => '9016'),
);

$bifolding_window_handle_map = array(
    'white'       => 'White',
    'chrome'      => 'Chrome',
    'black'       => 'Black',
    'black_white' => 'Black and White', // আপনার step‑6 ফাইল অনুযায়ী যোগ করা হয়েছে
);

$bifolding_window_glass_map = array(
    'self_cleaning'    => 'Self-cleaning glass',
    'integral_blinds'  => 'Integral blinds',
    'obscure_glass'    => 'Obscure glass',
    'saint_gobain_12'  => 'Saint-Gobain Planitherm 1.2',
    'no_thanks'        => 'Standard Glass',
);

$bifolding_window_install_map = array(
    'collection'        => 'Supply Only – Collection',
    'delivery'          => 'Supply Only – Delivery',
    'prepared_opening'  => 'Installed into Prepared Opening',
    'remove_existing'   => 'Remove Existing & Install',
);

/**
 * ============================================================
 * HELPER FUNCTION FOR WINDOWS
 * ============================================================
 */
if (!function_exists('get_window_pane_count')) {
    function get_window_pane_count($panel) {
        if (empty($panel)) return 1;
        preg_match('/(\d+)/', $panel, $matches);
        return isset($matches[1]) ? intval($matches[1]) : 1;
    }
}

/**
 * ============================================================
 * 1. REMOVE DEFAULT ADD TO CART BUTTON
 * ============================================================
 */
add_action('init', 'bifolding_window_remove_add_to_cart_button');
function bifolding_window_remove_add_to_cart_button() {
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
}

/**
 * ============================================================
 * 2. ADD WIZARD DATA TO CART ITEM FOR WINDOWS
 * ============================================================
 */
add_filter('woocommerce_add_cart_item_data', 'bifolding_window_add_wizard_data_to_cart', 10, 3);
function bifolding_window_add_wizard_data_to_cart($cart_item_data, $product_id, $variation_id) {
    global $bifolding_window_panel_map, $bifolding_window_colour_map, $bifolding_window_handle_map, 
           $bifolding_window_glass_map, $bifolding_window_install_map;

    // যদি আমরা ইতিমধ্যে AJAX হ্যান্ডলার থেকে wizard_data সেট করে ফেলি, তাহলে আর পরিবর্তন করব না
    if (isset($cart_item_data['wizard_data']['product_type']) && $cart_item_data['wizard_data']['product_type'] === 'window') {
        return $cart_item_data;
    }

    // Get form data
    if (isset($_POST['form_data'])) {
        parse_str($_POST['form_data'], $form_data);
    } else {
        $form_data = $_POST;
    }

    if (empty($form_data) || !isset($form_data['product_type']) || $form_data['product_type'] !== 'window') {
        return $cart_item_data;
    }

    $wizard_data = array();
    $wizard_data['product_type'] = 'window';

    // Collect window data (এখন সঠিক ফিল্ড নাম ব্যবহার করছি)
    if (isset($form_data['width']) && isset($form_data['height'])) {
        $wizard_data['width'] = intval($form_data['width']);
        $wizard_data['height'] = intval($form_data['height']);
    }

    // প্যানেল (window_panel_layout – step‑2)
    if (isset($form_data['window_panel_layout'])) {
        $wizard_data['panels'] = isset($bifolding_window_panel_map[$form_data['window_panel_layout']]) 
            ? $bifolding_window_panel_map[$form_data['window_panel_layout']] 
            : $form_data['window_panel_layout'];
    }

    // বাইরের রং (window_colour – step‑4)
    if (isset($form_data['window_colour'])) {
        if ($form_data['window_colour'] === 'custom_ral' && !empty($form_data['custom_colour_select'])) {
            $wizard_data['colour'] = $form_data['custom_colour_select'];
            $wizard_data['ral'] = str_replace('RAL ', '', $form_data['custom_colour_select']);
        } else {
            $colour = isset($bifolding_window_colour_map[$form_data['window_colour']]) 
                ? $bifolding_window_colour_map[$form_data['window_colour']] 
                : array('name' => $form_data['window_colour'], 'ral' => $form_data['window_colour']);
            $wizard_data['colour'] = $colour['name'];
            $wizard_data['ral'] = $colour['ral'];
        }
    }

    // ভিতরের রং (inside_colour – step‑5)
    if (isset($form_data['inside_colour'])) {
        if ($form_data['inside_colour'] === 'custom_ral' && !empty($form_data['custom_inside_colour_select'])) {
            $wizard_data['inside_colour'] = $form_data['custom_inside_colour_select'];
        } else {
            $wizard_data['inside_colour'] = $form_data['inside_colour'];
        }
    }

    // গ্লাস (glass_upgrade – step‑7)
    if (isset($form_data['glass_upgrade'])) {
        $wizard_data['glass'] = isset($bifolding_window_glass_map[$form_data['glass_upgrade']]) 
            ? $bifolding_window_glass_map[$form_data['glass_upgrade']] 
            : $form_data['glass_upgrade'];
    }

    // হ্যান্ডেল (handle_colour – step‑6)
    if (isset($form_data['handle_colour'])) {
        $wizard_data['handle'] = isset($bifolding_window_handle_map[$form_data['handle_colour']]) 
            ? $bifolding_window_handle_map[$form_data['handle_colour']] 
            : $form_data['handle_colour'];
    }

    // ট্রিকল ভেন্ট (trickle_vents – step‑8)
    if (isset($form_data['trickle_vents'])) {
        $wizard_data['trickle_vents'] = $form_data['trickle_vents'];
    }

    // সিল (cill – step‑9)
    if (isset($form_data['cill'])) {
        $wizard_data['cill'] = $form_data['cill'];
    }

    // ওপেনিং ডিরেকশন (open_direction – step‑3)
    if (isset($form_data['open_direction'])) {
        $wizard_data['opening_direction'] = $form_data['open_direction'];
    }

    // পোস্টকোড (postcode – step‑10)
    if (isset($form_data['postcode'])) {
        $wizard_data['postcode'] = sanitize_text_field($form_data['postcode']);
    }

    // ইনস্টলেশন টাইপ (window_installation_type – step‑11)
    if (isset($form_data['window_installation_type'])) {
        $wizard_data['installation_type'] = isset($bifolding_window_install_map[$form_data['window_installation_type']]) 
            ? $bifolding_window_install_map[$form_data['window_installation_type']] 
            : $form_data['window_installation_type'];
        $wizard_data['installation_type_value'] = $form_data['window_installation_type'];
    }

    // অ্যাক্সেস ইস্যু (access_issues – step‑12)
    if (isset($form_data['access_issues'])) {
        $wizard_data['access_issues'] = $form_data['access_issues'];
    }
    if (isset($form_data['access_description'])) {
        $wizard_data['access_description'] = sanitize_textarea_field($form_data['access_description']);
    }

    // কাস্টমার ইনফরমেশন (step‑13)
    if (isset($form_data['first_name'])) {
        $wizard_data['first_name'] = sanitize_text_field($form_data['first_name']);
    }
    if (isset($form_data['last_name'])) {
        $wizard_data['last_name'] = sanitize_text_field($form_data['last_name']);
    }
    if (isset($form_data['email_address'])) {
        $wizard_data['email'] = sanitize_email($form_data['email_address']);
    }
    if (isset($form_data['mobile_number'])) {
        $wizard_data['phone'] = sanitize_text_field($form_data['mobile_number']);
    }

    $wizard_data['unique_id'] = uniqid('window_', true);
    $wizard_data['timestamp'] = time();

    $cart_item_data['wizard_data'] = $wizard_data;
    $cart_item_data['unique_key'] = md5(serialize($wizard_data) . time() . rand(1000, 9999));

    return $cart_item_data;
}

/**
 * ============================================================
 * 3. DELIVERY CALCULATION AJAX HANDLER
 * ============================================================
 */
add_action('wp_ajax_check_delivery', 'bifolding_window_check_delivery');
add_action('wp_ajax_nopriv_check_delivery', 'bifolding_window_check_delivery');
function bifolding_window_check_delivery() {
    check_ajax_referer('window_builder_ajax', 'security');

    $postcode = sanitize_text_field($_POST['postcode']);
    if (empty($postcode)) {
        wp_send_json_error(['message' => 'Postcode required']);
    }

    // delivery-calculator.php থেকে ক্লাস লোড করা
    require_once get_stylesheet_directory() . '/delivery-calculator.php';
    $calculator = new Door_Delivery_Calculator();
    $result = $calculator->calculate_delivery($postcode);
    wp_send_json_success($result);
}

/**
 * ============================================================
 * 4. WINDOW BUILDER SUBMISSION (Step 14) AJAX HANDLER
 * ============================================================
 */
add_action('wp_ajax_process_window_builder', 'bifolding_window_process_submission');
add_action('wp_ajax_nopriv_process_window_builder', 'bifolding_window_process_submission');
function bifolding_window_process_submission() {
    check_ajax_referer('window_builder_ajax', 'security');

    // ফর্ম ডাটা পার্স
    parse_str($_POST['form_data'], $form_data);

    $product_id   = isset($form_data['product_id']) ? absint($form_data['product_id']) : 0;
    $variation_id = isset($form_data['variation_id']) ? absint($form_data['variation_id']) : 0;
    $cart_product_id = $variation_id ? $variation_id : $product_id;
    if (!$cart_product_id) {
        wp_send_json_error(['message' => 'প্রোডাক্ট পাওয়া যায়নি।']);
    }

    // উইন্ডোর সকল কনফিগারেশন ডাটা
    $wizard_data = [
        'product_type'        => 'window',
        'width'               => intval($form_data['width'] ?? 0),
        'height'              => intval($form_data['height'] ?? 0),
        'panels'              => sanitize_text_field($form_data['window_panel_layout'] ?? ''),
        'colour'              => sanitize_text_field($form_data['window_colour'] ?? ''),
        'inside_colour'       => sanitize_text_field($form_data['inside_colour'] ?? ''),
        'handle'              => sanitize_text_field($form_data['handle_colour'] ?? ''),
        'glass'               => sanitize_text_field($form_data['glass_upgrade'] ?? ''),
        'trickle_vents'       => sanitize_text_field($form_data['trickle_vents'] ?? ''),
        'cill'                => sanitize_text_field($form_data['cill'] ?? ''),
        'opening_direction'   => sanitize_text_field($form_data['open_direction'] ?? ''),
        'installation_type'   => sanitize_text_field($form_data['window_installation_type'] ?? ''),
        'access_issues'       => sanitize_text_field($form_data['access_issues'] ?? ''),
        'access_description'  => sanitize_textarea_field($form_data['access_description'] ?? ''),
        'postcode'            => sanitize_text_field($form_data['postcode'] ?? ''),
        'delivery_price'      => floatval($form_data['delivery_price'] ?? 0),
        'delivery_bespoke'    => sanitize_text_field($form_data['delivery_bespoke'] ?? '0'),
        'first_name'          => sanitize_text_field($form_data['first_name'] ?? ''),
        'last_name'           => sanitize_text_field($form_data['last_name'] ?? ''),
        'email'               => sanitize_email($form_data['email_address'] ?? ''),
        'phone'               => sanitize_text_field($form_data['mobile_number'] ?? ''),
        'final_price'         => floatval($form_data['final_price'] ?? 0),
    ];

    // বাইরের রং কাস্টম RAL হলে
    if ($wizard_data['colour'] === 'custom_ral' && !empty($form_data['custom_colour_select'])) {
        $wizard_data['colour'] = $form_data['custom_colour_select'];
        $wizard_data['ral']    = str_replace('RAL ', '', $form_data['custom_colour_select']);
    } else {
        $colour_map = [
            'anthracite_grey' => ['name' => 'Anthracite Grey', 'ral' => '7016'],
            'black'           => ['name' => 'Black', 'ral' => '9005'],
            'white'           => ['name' => 'White', 'ral' => '9016'],
        ];
        if (isset($colour_map[$wizard_data['colour']])) {
            $wizard_data['colour'] = $colour_map[$wizard_data['colour']]['name'];
            $wizard_data['ral']    = $colour_map[$wizard_data['colour']]['ral'];
        }
    }

    // ভেতরের রং কাস্টম RAL হলে
    if ($wizard_data['inside_colour'] === 'custom_ral' && !empty($form_data['custom_inside_colour_select'])) {
        $wizard_data['inside_colour'] = $form_data['custom_inside_colour_select'];
    }

    // কার্ট আইটেম ডাটা
    $cart_item_data = [
        'wizard_data'        => $wizard_data,
        'unique_key'         => md5(serialize($wizard_data) . time()),
        'custom_final_price' => $wizard_data['final_price'],
    ];

    // এডিট মোড – পুরোনো আইটেম সরান
    $edit_mode     = !empty($form_data['edit_mode']);
    $cart_item_key = sanitize_text_field($form_data['cart_item_key'] ?? '');
    if ($edit_mode && $cart_item_key) {
        WC()->cart->remove_cart_item($cart_item_key);
    }

    // পুরোনো ফিল্টার সরিয়ে দিন (যাতে আমাদের ডাটা নষ্ট না হয়)
    remove_filter('woocommerce_add_cart_item_data', 'bifolding_window_add_wizard_data_to_cart', 10);

    // কার্টে প্রোডাক্ট যোগ করুন
    $added = WC()->cart->add_to_cart($cart_product_id, 1, 0, [], $cart_item_data);

    if ($added) {
        wp_send_json_success(['message' => 'কার্টে যোগ হয়েছে']);
    } else {
        wp_send_json_error(['message' => 'কার্টে যোগ করা যায়নি।']);
    }
}

/**
 * ============================================================
 * 5. CUSTOM PRICE OVERRIDE (final_price)
 * ============================================================
 */
add_action('woocommerce_before_calculate_totals', 'bifolding_window_custom_price', 20, 1);
function bifolding_window_custom_price($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    foreach ($cart->get_cart() as $cart_item) {
        if (isset($cart_item['custom_final_price']) && $cart_item['custom_final_price'] > 0) {
            $cart_item['data']->set_price($cart_item['custom_final_price']);
        }
    }
}