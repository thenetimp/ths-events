<?php

// Require the wordpress loader.

include_once './../../../wp-load.php';

include_once(dirname(__FILE__) . '/functions.php');

// Toggle the users RSVP.
\events\functions\toggle_user_rsvp($_GET['event_id'], $_GET['date_event']);