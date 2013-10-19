<?php
/**
 * @package THS Events
 * @version 1.0
 */
/*
Plugin Name: THS Events
Description: A simple plugin for adding single or recurring events
Author: James Andrews
Version: 1.0
*/

define("EVENTS_TABLE_EVENTS", $wpdb->prefix . 'events');
define("EVENTS_TABLE_POSTS", $wpdb->prefix . 'posts');
define("EVENTS_TABLE_VENUES", $wpdb->prefix . 'events_venues');

require_once(dirname(__FILE__) . '/classes/EventQuery.class.php');
require_once(dirname(__FILE__) . '/classes/EventWidget.class.php');

$time = (isset($_REQUEST['date'])) ? strtotime($_REQUEST['date']) : time();

$counter = 0;
$eventQuery = new \events\classes\EventQuery(date('Y-m-d H:i:s', $time));

require_once(dirname(__FILE__) . '/functions.php');

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 413, 274);
add_image_size( 'thumb-event-widget', 155, 190);

add_filter('the_title', '\events\functions\update_event_title', 10, 2);
add_action('admin_init', '\events\functions\admin_init');
add_action('admin_footer', 'my_admin_footer');
add_action( 'widgets_init', '\events\functions\register_widgets' ); 

if(is_admin())
{
    // add a callback function to save any data a user enters in
    add_action('admin_menu', '\events\functions\admin_boxes');
    add_action('save_post','\events\functions\save_event');
}

function my_admin_footer() {
    ?>
    <script type="text/javascript">
    (function ($)
    {
        $(document).ready(function(){
            $('.mydatepicker').datepicker({'dateFormat': 'yy-mm-dd'});
        });
    }
    ( jQuery ));
    </script>
    <?php
}
