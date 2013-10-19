<input type="hidden" name="event_id">
<p class="field">
    <label>Event Frequency:</label><br />
    <select name="event_type">
        <option <?php echo (isset($event) && $event->type == 'single') ? 'SELECTED' : ''; ?> value="single">One time</option>
        <option <?php echo (isset($event) && $event->type == 'weekly') ? 'SELECTED' : ''; ?> value="weekly">Weekly</option>
    </select>
</p>
<p class="field">
    <label>Date Start:</label><br />
    <input type="text" name="date_start" class="mydatepicker" value="<?php 
        echo (isset($event)) ? $event->date_start : $today; ?>" />
</p>
<p class="field">
    <label>Time Start:</label><br />
    <select name="time_start">
        <?php for($i=8; $i <=20; $i++): ?>
            <option value="<?php echo sprintf("%02d", $i); ?>:00"><?php echo sprintf("%02d", $i); ?>:00</option>
            <?php if($i < 20): ?>
            <option value="<?php echo sprintf("%02d", $i); ?>:15"><?php echo sprintf("%02d", $i); ?>:14</option>
            <option value="<?php echo sprintf("%02d", $i); ?>:30"><?php echo sprintf("%02d", $i); ?>:30</option>
            <option value="<?php echo sprintf("%02d", $i); ?>:45"><?php echo sprintf("%02d", $i); ?>:45</option>
            <?php endif; ?>
        <?php endfor; ?>
    </select>
</p>
<p class="field">
    <label>Date End:</label><br />
    <input type="text" name="date_end" class="mydatepicker" value="<?php 
        echo (isset($event)) ? $event->date_end : $today; ?>" />
</p>
<p class="field">
    <label>Time End:</label><br />
    <select name="time_end">
        <?php for($i=0; $i <=24; $i++): ?>
            <option value="<?php echo sprintf("%02d", $i); ?>:00"><?php echo sprintf("%02d", $i); ?>:00</option>
            <option value="<?php echo sprintf("%02d", $i); ?>:15"><?php echo sprintf("%02d", $i); ?>:14</option>
            <option value="<?php echo sprintf("%02d", $i); ?>:30"><?php echo sprintf("%02d", $i); ?>:30</option>
            <option value="<?php echo sprintf("%02d", $i); ?>:45"><?php echo sprintf("%02d", $i); ?>:45</option>
        <?php endfor; ?>
    </select>
</p>
<p class="field">
    <label>Public Event:</label><br />
    <select name="members_only_event">
        <option <?php echo (isset($event) && !$event->members_only_event) ? 'SELECTED' : ''; ?> value="0">Open to Public</option>
        <option <?php echo (isset($event) && $event->members_only_event) ? 'SELECTED' : ''; ?> value="1">Members Only</option>
    </select>
</p>
<p class="field">
    <label>Max Allowed Participants:</label><br />
    <select name="max_participants">
        <option value="">Please Select</option>
        <?php for($i=5; $i<21; $i++): ?>
        <option <?php echo (isset($event) && $event->max_participants == $i) ? 'SELECTED' : ''; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
        <?php endfor; ?>
    </select>
</p>
<p class="field">
    <label>Member Price:</label><br />
    <input type="text" name="members_price" value="<?php 
        echo (isset($event)) ? $event->members_price : "0" ?>" />
</p>
<p class="field">
    <label>Public Price:</label><br />
    <input type="text" name="public_price" value="<?php 
        echo (isset($event)) ? $event->public_price : "0" ?>" />
</p>
<p class="field">
    <label>Host:</label><br />
    <input type="text" name="members_price" value="<?php 
        echo (isset($event)) ? $event->host : "" ?>" />
</p>
<p class="field">
    <label>Host's Email:</label><br />
    <input type="text" name="members_price" value="<?php 
        echo (isset($event)) ? $event->host_email : "" ?>" />
</p>
<p class="field">
    <label>Venue:</label><br /> 
    <select name="event_venue">
        <?php foreach($event_venues as $venue): ?>
        <option <?php echo ((isset($event) && isset($venue)) && $event->venue_ID == $venue->ID) ? 'SELECTED' : ''; ?> value="<?php echo $venue->ID; ?>"><?php echo $venue->name; ?></option>
        <?php endforeach; ?>
    </select>
</p>