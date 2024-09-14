<?php
// get the current time in minutes and hours
$current_time = date('H:i');

// extract the minutes from the current time
$current_minutes = date('i', strtotime($current_time));

// check if the current minutes are between 0 and 03
if ($current_minutes >= 0 && $current_minutes <= 03) {
    echo "Ad is being injected.";
} else {
    echo "Normal program is currently on air.";
}

?>