<?php

use Carbon\Carbon;

if (!function_exists('shortTimeDiff')) {
    function shortTimeDiff(Carbon $time): string
    {
        // Get the human-readable time difference
        $humanReadableTime = $time->diffForHumans();
        
        $timeAbbreviations = [
            ' second ago' => 's ago',
            ' seconds ago' => 's ago',
            ' minute ago' => 'm ago',
            ' minutes ago' => 'm ago',
            ' hour ago' => 'h ago',
            ' hours ago' => 'h ago',
            ' day ago' => 'd ago',
            ' days ago' => 'd ago',
            ' week ago' => 'w ago',
            ' weeks ago' => 'w ago',
            ' month ago' => 'mo ago',
            ' months ago' => 'mo ago',
            ' year ago' => 'y ago',
            ' years ago' => 'y ago',
        ];

        return str_replace(array_keys($timeAbbreviations), array_values($timeAbbreviations), $humanReadableTime);
    }
}

