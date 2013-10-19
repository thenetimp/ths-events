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
		'post', // where to add the box: on "post", "page", or "link" page
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
    
    if(isset($_POST['post_category']) && in_array(4, $_POST['post_category']))
    {
        $wpdb->show_errors();
        
        if($_POST['action'] == 'editpost')
        {
            $data = array(
                'venue_ID' => 1, //$_POST['event_venue'],
                'type' => $_POST['event_type'],
                'date_start' => $_POST['date_start'],
                'time_start' => $_POST['time_start'],
                'date_end' => $_POST['date_end'],
                'time_end' => $_POST['time_end'],
                'members_only_event' => $_POST['members_only_event'],
                'max_participants' => $_POST['max_participants'],
                'members_price' => $_POST['members_price'],
                'public_price' => $_POST['public_price']
            );

            $wpdb->update(EVENTS_TABLE_EVENTS, $data, array(
                'post_ID' => $_POST['post_ID']
            ));
        }
        else
        {
            $wpdb->insert(EVENTS_TABLE_EVENTS, array(
                'post_ID' => $_POST['post_ID'],
                'venue_ID' => 1,  //$_POST['event_venue'],
                'type' => $_POST['event_type'],
                'date_start' => $_POST['date_start'],
                'time_start' => $_POST['time_start'],
                'date_end' => $_POST['date_end'],
                'time_end' => $_POST['time_end'],
                'members_only_event' => $_POST['members_only_event'],
                'max_participants' => $_POST['max_participants'],
                'member_price' => $_POST['member_price'],
                'public_price' => $_POST['public_price']
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

function the_event()
{
    global $eventQuery;
    
    return $eventQuery->the_event();
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
    global $event;
    return '0';
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

function the_event_venue_address()
{
    global $event;
    echo $event->address;
}





