<?php
/**
 * Plugin Name: Calendar Planner
 * Plugin URI: www.vakantiehuiscadzand.nl/
 * Description: A plugin created to plan in people who want to visit vakantiehuis cadzand
 * Version: 1.0
 * Author: Rick Siepelinga
 * Author URI: http://24585.hosts1.ma-cloud.nl/bewijzenmap/periode2.1/onepage/
 **/

require_once('settings-page.php');
require_once('create-db.php');
require_once('form.php');

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// On activation of plugin, run this function
function activatePlugin()
{
    $createDB = new CreateDB();
    $createDB->installDatabase();

    $settingsPage = new SettingsPage();
    $settingsPage->addPluginPage();


//    Set option on default
    if (!get_option('summer_price')) {
        update_option('summer_price', 99, 0);
    }

    if (!get_option('late_price')) {
        update_option('late_price', 89, 0);
    }

    if (!get_option('low_price')) {
        update_option('low_price', 69, 0);
    }
}

/*
 * Register the function so that it gets called on once the plugin is activated.
 */
register_activation_hook(__FILE__, 'activatePlugin');

// Agenda shortcode
function calendar_shortcode()
{
    return '<div id="calendar"></div>';
}

add_shortcode('calendar_plugin', 'calendar_shortcode');

// Form shortcode
function form_shortcode()
{
    $form = new FormDate();
    $form->createForm();
}

add_shortcode('calendar_form', 'form_shortcode');


// Enqueue dates

function enqueueDates()
{

    //  Fullcalendar CSS
    $dir_core_css = plugins_url('calendar-planner/fullcalendar/packages/core/main.css');
    $dir_daygrid_css = plugins_url('calendar-planner/fullcalendar/packages/daygrid/main.css');
    $dir_bootstrap_css = plugins_url('calendar-planner/fullcalendar/packages/bootstrap/main.css');

    wp_register_style( 'calendar-core', $dir_core_css);
    wp_register_style( 'calendar-day', $dir_daygrid_css);
    wp_register_style( 'calendar-bootstrap', $dir_bootstrap_css);

    wp_enqueue_style('calendar-core');
    wp_enqueue_style('calendar-day');
    wp_enqueue_style('calendar-bootstrap');


    // Fullcalendar Javascript
    $dir = plugins_url('calendar-planner/js/calendar.js');
    $dir_core = plugins_url('calendar-planner/fullcalendar/packages/core/main.js');
    $dir_day = plugins_url('calendar-planner/fullcalendar/packages/daygrid/main.js');
    $dir_bootstrap = plugins_url('calendar-planner/fullcalendar/packages/bootstrap/main.js');

    wp_enqueue_script('calendar', $dir, array('jquery'), '', 'true');
    wp_enqueue_script('calendar-core', $dir_core, '', '', 'true');
    wp_enqueue_script('calendar-day', $dir_day, '', '', 'true');
    wp_enqueue_script('calendar-bootstrap', $dir_bootstrap, '', '', 'true');

    wp_localize_script('calendar', 'calendarScript', array(
        'pluginsUrl' => plugins_url('', __FILE__),
    ));

    //      Jquery css
    $dir_jquery_css = plugins_url('calendar-planner/jquery/jquery-ui.css');

    wp_register_style( 'jquery-css', $dir_jquery_css);

    wp_enqueue_style('jquery-css');

    //      Form css
    $dir_form_css = plugins_url('calendar-planner/css/form.css');

    wp_register_style( 'booking-form', $dir_form_css);

    wp_enqueue_style('booking-form');

    //      Table css
    $dir_table_css = plugins_url('calendar-planner/css/table.css');

    wp_register_style( 'booking-table', $dir_table_css);

    wp_enqueue_style('booking-table');


    //    Dates js,
    $dir_dates = plugins_url('calendar-planner/js/dates.js');

    wp_enqueue_script('dates', $dir_dates, array('jquery'), '', 'true');

    //    Add the var "pluginsUrl" to give the javascript easy access to the directory
    wp_localize_script('dates', 'datesScript', array(
        'pluginsUrl' => plugins_url('', __FILE__),
    ));

    //  Prices
    $dir_prices = plugins_url('calendar-planner/js/prices.js');

    wp_enqueue_script('prices', $dir_prices, array('jquery'), '', 'true');

    //  Add the var "pluginsUrl" to give the javascript easy access to the directory
    wp_localize_script('prices', 'priceScript', array(
        'pluginsUrl' => plugins_url('', __FILE__),
    ));

    // Jquery Ui
    $dir_jquery = plugins_url('calendar-planner/jquery/jquery-ui.js');

    wp_enqueue_script('jquery-ui', $dir_jquery, '', '', 'false');

    // Moment js
    $dir_moment = plugins_url('calendar-planner/moment/moment.js');

    wp_enqueue_script('moment', $dir_moment, '', '', 'true');

}

add_action('wp_enqueue_scripts', 'enqueueDates');
add_action('admin_enqueue_scripts', 'enqueueDates');
