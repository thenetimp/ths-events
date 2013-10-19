<?php
    
namespace events\classes\widgets;

// Widget Docs found here http://codex.wordpress.org/Widgets_API#Developing_Widgets_on_2.8.2B

/**
 *
 * check if the sidebar is active
 *
 */
class EventWidget extends \WP_Widget {

    /**
     *
     * check if the sidebar is active
     *
     */
	public function __construct()
    {
        parent::__construct(
    		'widget-event-widget', // Base ID
    		__('Upcoming Events', 'text_domain'), // Name
    		array( 'description' => __( 'Upcoming events displayed with icons.', 'text_domain' )));
	}
    
    /**
     *
     * widget function
     *
     */
	public function widget( $args="", $instance="")
    {
        global $eventQuery;

        // get the events from the query object
        $events = $eventQuery->getEvents();

        // Make sure we have next months events 
        $time = time();
        $eventTime = mktime(0, 0, 0, date("m", $time)+1, 1,date("y", $time));
        $eventQuery->setQueryDate(date('Y-m-d', $eventTime));
        $moreEvents = $eventQuery->getEvents();
        
        // Get 2 moths of events
        $events = array_merge($events, $moreEvents);
        ksort($events);
        
        $count = 0;
        
        // Send the output of the widget.
		echo $args['before_widget'];
		echo $args['before_title'];
        
        echo '<span class="locale locale-en">Upcoming Events <small class="pull-right"><a href="/tokyo-hackerspace-events">More Events</a></small></span>';
        echo '<span class="locale locale-jp">今後のイベント <small class="pull-right"><a href="/tokyo-hackerspace-events">その他のイベント</a></small></span>';
        
		echo $args['after_title'];
        echo '<div class="row">';

        foreach($events as $event)
        {
            echo '<div class="col-md-3">';
            echo '<div class="thumbnail event">';

            // Send the thumbnail.
            if ( has_post_thumbnail($event->post_ID) ) { 
                echo get_the_post_thumbnail($event->post_ID, 'thumb-event-widget');
            } else {
                echo '<img data-src="holder.js/155x190" alt="">';
            }

            echo '<div class="caption">';
            echo '<h3 class="event-name"><a href="#">' . $event->post_title . '</a></h3>';
            echo $event->date_event . ' ' . substr($event->time_start, 0,5);
            echo '</div>';
            echo '</div>';
            echo '</div>';
            if($count++ == 3) break;
        }
        echo '</div>';
		echo $args['after_widget'];
	}    
    
    
}