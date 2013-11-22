<?php
    
namespace events\classes;


class EventQuery {
    
    protected $events = array();
    protected $pastEvents = array();
    protected $eventMarker = -1;
    protected $pastEventMarker = -1;
    protected $arrayKeys = array();
    protected $days = array('Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6);
    protected $queryTime = null;
    protected $today = null;
    protected $monthStartTime = null;
    protected $nextMonthStartTime = null;
    
    public function __construct($queryDate = "")
    {
        $this->setQueryDate($queryDate);
    }
    
    public function setQueryDate($queryDate = "")
    {
        if(!$queryDate) throw new Exception("No Query Date specified");

        // Clear the events array
        $this->events = array();

        // Set the query time to today
        $this->queryTime = time();
        
        // Get today's date
        $this->today = $this->queryTime;

        // If a date is passed then recalculate the time.
        if($queryDate != "")
        {
            // print 'test';
            $this->queryTime = strtotime($queryDate);
        }

        $this->monthStartTime =  mktime(0, 0, 0, date("m", $this->queryTime), date("d", $this->queryTime),   date("Y", $this->queryTime));
        $this->nextMonthStartTime =  mktime(0, 0, 0, date("m", $this->queryTime)+1, date("d", $this->queryTime),   date("Y", $this->queryTime));

        // Generate the events.
        $this->generateEvents();
        
        // sort the events array by keys

        ksort($this->events);
        $this->arrayKeys = array_keys($this->events);

        if(count($this->events) > 0) $this->eventMarker = 0;
    }

    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Generate events.
     */
    protected function generateEvents()
    {

        global $wpdb;
        
        $monthStartDate = date('Y-m-d', $this->monthStartTime);
        $nextMonthStartDate = date('Y-m-d', $this->nextMonthStartTime);

        // get the event.
        $sql = $wpdb->prepare("SELECT v.*,p.*,e.*,v.ID as venue_venue_id,p.ID as post_post_id FROM wp_events e, wp_posts p, wp_events_venues v WHERE p.post_type='ths-event' and e.post_ID = p.ID AND e.venue_ID = v.ID AND (e.type = 'single' AND (e.date_start >= %s AND e.date_start < %s) OR (e.type <> 'single' AND (e.date_end = '00-00-00' OR e.date_end < %s )))", $monthStartDate, $nextMonthStartDate, $nextMonthStartDate);
        
        // get the reqults from the SQL.
        $results = $wpdb->get_results($sql);

        foreach($results as $event)
        {
            switch($event->type)
            {
                case 'yearly': 
                    $this->evaluateYearlyEvent($event);
                    break;
                case 'monthly': 
                    $this->evaluateMonthlyEvent($event);
                    break;
                case 'weekly': 
                    $this->evaluateWeeklyEvent($event);
                    break;
                case 'daily': 
                    $this->evaluateDailyEvent($event);
                    break;
                default:
                    $this->evaluateEvent($event);
            }
        }
    }

    /**
     *
     */
    protected function evaluateEvent($event)
    {
        if(strtotime($event->date_start) < time())
        {
            $this->pastEvents[strtotime($event->date_start . " " . $event->time_start) . '-' . $event->post_ID] = $event;
            
        }
        else
        {
            $event->date_event = $event->date_start;
            $this->events[strtotime($event->date_start . " " . $event->time_start) . '-' . $event->post_ID] = $event;
        }
    }

    /**
     *
     */
    protected function evaluateYearlyEvent($event)
    {
    }

    /**
     *
     */
    protected function evaluateMonthlyEvent($event)
    {
        // Get the day of the month.
        // $day = date('d', strtotime($event->date_start));
    }

    /**
     *
     */
    protected function evaluateWeeklyEvent($event)
    {
        // Get the day of the week.
        $day = date('l', strtotime($event->date_start));
        $eventDates = $this->getDates(date('Y-m-1', $this->queryTime),
                        date('Y-m-28', $this->queryTime),
                        array($this->days[$day]));

        // Run through the events and store them in the objects recurring array.
        foreach($eventDates as $eventDate)
        {
            $tmp = clone $event;
            $tmp->date_event = $eventDate->format('Y-m-d');
            
            if(strtotime($tmp->date_event) < time())
            {
                $this->pastEvents[strtotime($tmp->date_event . " " . $tmp->time_start) . '-' . $event->post_ID] = $tmp;
                
            }
            else
            {
                $this->events[strtotime($tmp->date_event . " " . $tmp->time_start) . '-' . $event->post_ID] = $tmp;
                
            }
        }
    }

    /**
     *
     */
    protected function evaluateDailyEvent($event)
    {
        
    }

    /**
     *
     */
    protected function getDates($start_date, $end_date, $days){
        
        // parse the $start_date and $end_date string values
        $stime=new \DateTime($start_date);
        $etime=new \DateTime($end_date);

        // make a copy so we can increment by day    
        $ctime = clone $stime;
        $results = array();
        while( $ctime <= $etime ){

            $dow=$ctime->format("w");
            // assumes $days is array containing integers for Sun (0) - Sat (6)
            if( in_array($dow, $days) ){ 
                // make a copy to return in results
                $results[]=clone $ctime;
            }
            // incrememnt by 1 day
            $ctime->modify("+1 days");
        }

        return $results;
    }
    
    /**
     *
     */
    public function have_events()
    {
        // If the eventMarker hasn't been set to 0 then return false
        if($this->eventMarker == -1) return false;
        
        if($this->eventMarker == count($this->events))
        {
            return false;
        } 

        return true;
    }
    /**
     *
     */
    public function have_past_events()
    {
        // If the eventMarker hasn't been set to 0 then return false
        if($this->pastEventMarker == -1) return false;
        
        if($this->pastEventMarker == count($this->pastEvents))
        {
            return false;
        } 

        return true;
    }
    
    /**
     * Return the event at the current mark
     */
    public function the_event($post_id = false)
    {
        global $event;

        if($post_id)
        {
            foreach($this->events as $eventCheck)
            {
                if($eventCheck->post_ID == $post_id)
                {
                    $event = $eventCheck;
                }
            }
        }
        else
        {
            $event = $this->events[$this->arrayKeys[$this->eventMarker++]];
        
            $totalEvents = count($this->events);
            if($this->eventMarker > $totalEvents) $this->eventMarker = $totalEvents - 1;
        }
    }

    /**
     * Return the event at the current mark
     */
    public function the_past_event()
    {
        global $event;

        $event = $this->events[$this->arrayKeys[$this->pastEventMarker++]];
        
        $totalEvents = count($this->events);
        if($this->pastEventMarker > $totalEvents) $this->pastEventMarker = $totalEvents - 1;
    }

    
    /**
     * Rewind the event mark to a specified place otherwise return to the beginning.
     * If the specified mark is a number greater than the number of events
     * then set it to the last event mark
     */
    public function rewind_event($eventMarker = 0)
    {
        $totalEvents = count($this->events);
        if($eventMarker > $totalEvents) $eventMarker = $totalEvents - 1;
        $this->eventMarker = $eventMarker;
    }

    /**
     * Rewind the past event mark to a specified place otherwise return to the beginning.
     * If the specified mark is a number greater than the number of events
     * then set it to the last event mark
     */
    public function rewind_past_event($eventMarker = 0)
    {
        $totalEvents = count($this->pastEvents);
        if($eventMarker > $totalEvents) $eventMarker = $totalEvents - 1;
        $this->pastEventMarker = $eventMarker;
    }

}