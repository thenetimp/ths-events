<?php
    
namespace events\functions;

/**
 *
 * Register all new widgets
 *
 */
function register_widgets()
{
    register_widget('\events\classes\widgets\EventWidget');    
}

function create_event_post_type() {
    register_post_type( 'ths-event',
        array(
            'labels' => array(
                'name' => __( 'Events' ),
                'singular_name' => __( 'Event' ),
                'add_new_item'       =>  __('Add New Event')
            ),
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'revisions'
            ),
            'public' => true,
            'has_archive' => true,
            'menu_position' => 6    
        )
    );
    
    // flush_rewrite_rules();
}

/**
 *
 * New Admin box for posts page.
 *
 */
function admin_boxes() {

	add_meta_box(
		'admin-event-management', // id of the <div> we'll add
		'Event Details', //title
		'\events\functions\event_meta_view', // callback function that will echo the box content
		'ths-event', // where to add the box: on "post", "page", or "link" page
		'side', // put it in the side bar
		'low' // put it high on the page.
	);
}

/**
 *
 * Display the form for events in the new admin box
 *
 */
function event_meta_view()
{
    global $wpdb;

    $today = date('Y-m-d', time());

    $event = $wpdb->get_row($wpdb->prepare('SELECT * FROM '  . EVENTS_TABLE_EVENTS . ' WHERE post_ID= %s', $_GET['post']));

    $event_venues = $wpdb->get_results('SELECT * FROM ' . EVENTS_TABLE_VENUES);

    include_once(dirname(__FILE__) . '/views/admin_event_meta.php');
}

function save_event()
{
    global $wpdb;


    if(isset($_POST['post_type']) && $_POST['post_type'] == 'ths-event')
    {
        // check if the post is attached to an event.
        $result = $wpdb->get_row($wpdb->prepare("SELECT count(*) as count FROM " . EVENTS_TABLE_EVENTS . " WHERE post_ID = %s", $_POST['post_ID']));

        if($result->count == 1)
        {

            $data = array(
                'venue_ID' => 1, //$_POST['event_venue'],
                'type' => $_POST['event_type'],
                'date_start' => $_POST['date_start'],
                'time_start' => $_POST['time_start'],
                'date_end' => $_POST['date_end'],
                'time_end' => $_POST['time_end'],
                'members_only_event' => $_POST['members_only_event'],
                'max_participants' => (isset($_POST['max_participants']) && $_POST['max_participants'] != "") ? $_POST['max_participants'] : 0,
                'members_price' => (isset($_POST['members_price']) && $_POST['members_price'] != "") ? $_POST['members_price'] : 0,
                'public_price' => (isset($_POST['public_price']) && $_POST['public_price'] != "") ? $_POST['public_price'] : 0
            );

            $wpdb->update(EVENTS_TABLE_EVENTS, $data, array(
                'post_ID' => $_POST['post_ID']
            ));
        }
        else
        {
          $result =   $wpdb->insert(EVENTS_TABLE_EVENTS, array(
                'post_ID' => $_POST['post_ID'],
                'venue_ID' => 1,  //$_POST['event_venue'],
                'type' => $_POST['event_type'],
                'date_start' => $_POST['date_start'],
                'time_start' => $_POST['time_start'],
                'date_end' => $_POST['date_end'],
                'time_end' => $_POST['time_end'],
                'members_only_event' => $_POST['members_only_event'],
                'max_participants' => (isset($_POST['max_participants']) && $_POST['max_participants'] != "") ? $_POST['max_participants'] : 0,
                'members_price' => (isset($_POST['members_price']) && $_POST['members_price'] != "") ? $_POST['members_price'] : 0,
                'public_price' => (isset($_POST['public_price']) && $_POST['public_price'] != "") ? $_POST['public_price'] : 0,
                'created_at' => date('Y-m-d H:i:s')
            ));
        }
    }
}

function enqueue_admin_styles_and_admin_scripts()
{

	wp_enqueue_style( 'style-jqui', plugins_url() . '/ths-events/css/ui-lightness/jquery-ui-1.10.3.custom.min.css' );
    
    // Include necessary Javascripts
    wp_enqueue_script( 'script-jqui', plugins_url() .
        '/ths-events/js/jquery-ui-1.10.3.custom.min.js', array('jquery'), '2.0.3', true );
}

function admin_init()
{
    enqueue_admin_styles_and_admin_scripts();
}

function update_event_title($title="", $id="")
{
    if(in_category(array('events'), $id))
    {
        // If we have an ID then we want
        if($id != "")
        {
            // get the time if the date is set
            $time = (isset($_REQUEST['date'])) ? strtotime($_REQUEST['date']) : time();
            
            // Set the month
            $month = date('F Y', $time);
            
            $title = $title . '<small> - ' . $month . '</small>';
        }
    }
    
    // Return 
    return $title;
}

function next_month()
{

    $time = (isset($_REQUEST['date'])) ? strtotime($_REQUEST['date']) : time();
    $nextMonth = mktime(0, 0, 0, date("m", $time)+1, date("d", $time),   date("Y", $time));
    return date('Y-m', $nextMonth);
}

function last_month()
{
    $time = (isset($_REQUEST['date'])) ? strtotime($_REQUEST['date']) : time();
    $lastMonth = mktime(0, 0, 0, date("m", $time)-1, date("d", $time),   date("Y", $time));
    return date('Y-m', $lastMonth);
}

function next_month_string()
{
    $time = (isset($_REQUEST['date'])) ? strtotime($_REQUEST['date']) : time();
    $nextMonth = mktime(0, 0, 0, date("m", $time)+1, date("d", $time),   date("Y", $time));
    return date('F Y', $nextMonth);
}

function last_month_string()
{
    $time = (isset($_REQUEST['date'])) ? strtotime($_REQUEST['date']) : time();
    $lastMonth = mktime(0, 0, 0, date("m", $time)-1, date("d", $time),   date("Y", $time));
    return date('F Y', $lastMonth);
}

function have_events()
{
    global $eventQuery;
    return $eventQuery->have_events();
}

function the_event($post_ID = false)
{
    global $eventQuery;
    
    return $eventQuery->the_event($post_ID);
}

function rewind_event($mark)
{
    global $eventQuery;
    return $eventQuery->rewind_event($mark);
}

function the_event_title()
{
    global $event;
    echo $event->post_title;
}

function the_event_start_datetime($delimiter = " ")
{
    global $event;
    
    // Recurring events use $date_event for their date_start
    // So first define the datetime with the event datetime
    // then check to see if it's got date_event defined and change
    // datetime if it is set.
    $datetime = $event->date_start;
    if(isset($event->date_event)) $datetime = $event->date_event;
    $datetime .= $delimiter . $event->time_start;
    
    // Return the datetime
    echo $datetime;
}

// function the_event_excerpt()
// {
//     echo apply_filters('the_event_excerpt', get_the_event_excerpt());    
// }
// 
// function get_the_event_excerpt()
// {
//     
// }


function the_event_end_time()
{
    global $event;
    
    echo $event->time_end;
}

function the_event_post()
{
    global $event;
    echo $event->post_content;
}

function the_event_excerpt()
{
    global $event;
    echo $event->post_excerpt;
}

function the_event_max_participants()
{
    global $event;
    
    echo ($event->max_participants == 0) ? "Unlimited" : $event->max_participants;    
}

function the_event_participant_count()
{
    global $wpdb;
    global $event;

    $result = $wpdb->get_row($wpdb->prepare('SELECT count(*) as count FROM ' . EVENTS_TABLE_RESERVATION . ' WHERE event_rsvp = true and event_date = %s and event_id= %s', $event->date_event, $event->ID));

    echo $result->count;
}

function display_participants()
{
    global $event;
    if($event->max_participants > 0) return true;
    return false;
}


function display_members_price()
{
    if($event->members_price > 0) return true;
    return false;  
}

function display_public_price()
{
    if($event->public_price > 0) return true;
    return false;  
}

function toggle_user_rsvp($event_id=0, $date_event=false)
{
    global $wpdb;

    $wp_user = wp_get_current_user();

    if($event_id == 0 || $date_event==false)
    {
        echo "go away";
        exit();
    }
    
    if(!$wp_user)
    {
        echo "go away";
        exit();
    }
    
    // Get the status of the user RSVP
    $rsvp_status = \events\functions\check_user_rsvp($event_id, $date_event);

    $wpdb->show_errors();

    // Figure out if we are updating or inserting a new record.
    if($rsvp_status && ($rsvp_status->event_rsvp == true || $rsvp_status->event_rsvp == false))
    {
        $wpdb->show_errors();
        $wpdb->update(EVENTS_TABLE_RESERVATION, 
            array('event_rsvp' => (($rsvp_status->event_rsvp) ? 0 : 1)),
            array('ID' => $rsvp_status->ID)
        );
    }
    else
    {
        $wpdb->insert(EVENTS_TABLE_RESERVATION, 
            array(
                'event_ID' => $event_id,
                'user_ID' => $wp_user->ID,
                'event_date' => $date_event,
                'event_rsvp' => TRUE
            )
        );
    }

    return true;
}

function check_user_rsvp($event_id=0, $date_event=false)
{
    global $wpdb;
    $wp_user = wp_get_current_user();
    
    $sql = $wpdb->prepare('SELECT * FROM ' . EVENTS_TABLE_RESERVATION . ' WHERE user_ID= %s AND event_ID = %s AND event_date = %s', $wp_user->ID, $event_id, $date_event);

    $response = $wpdb->get_row($sql);
    return $response;
}

function is_rsvp_for_event($event_id=0, $date_event=false)
{
    $response = \events\functions\check_user_rsvp($event_id, $date_event);

    if ($response == "") return FALSE;
    return $response->event_rsvp;
}

function the_event_members_price()
{
    global $event;
    echo $event->members_price;
}

function the_event_public_price()
{
    global $event;
    echo $event->public_price;
}

function the_event_venue()
{
    global $event;
    echo $event->name;
}

function get_the_event_ID()
{
    global $event;
    return $event->ID;
}

function the_event_venue_address()
{
    global $event;
    echo $event->address;
}

function the_event_rsvp_url($event_id=0, $date_event=false)
{
    global $event;
    echo plugins_url('ths-events') . '/processor.php?event_id=' . urlencode($event_id) . '&date_event=' . urlencode($date_event);
}
