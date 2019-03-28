<?php

function getTime($num){
    if (!is_integer($num))
        return "Time not formatted correctly";

    $num = time() - $num;
    
    if ($num < 45){
        $num = "right now";
    }
    else if ($num < 300){
        $num = "less than 5 minutes ago";
    }
    else if ($num < 900){
        $num = "less than 15 minutes ago";
    }
    else if ($num < 1800){
        $num = "less than a half an hour ago";
    }
    else if ($num < 3600){
        $num = "less than an hour ago";
    }
    else if ($num < 7200){
        $num = "less than a day ago";
    }
    else if ($num < 50400){
        $daysAway = ceil($num / 7200);
        $num = "less than " . (Int)$daysAway . " days ago";
    }
    else {
        $num = "more than a week ago";
    }

    return $num;
}
