<?php

use Carbon\Carbon;

if (!function_exists('shortTimeDiff')) {
    function shortTimeDiff(Carbon $time): string
    {
        return str_replace(
            [' seconds ago', ' minutes ago', ' hours ago', ' days ago', ' weeks ago', ' months ago', ' years ago'],
            ['s', 'm', 'h', 'd', 'w', 'mo', 'y'],
            $time->diffForHumans()
        );
    }
}
