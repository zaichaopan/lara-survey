<?php

namespace App;

class Summary extends StrategyFinder
{
    const AVAILABLE_TYPES = [
       'user_answer' => 'App\Summaries\UserAnswer',
        'break_down' => 'App\Summaries\Breakdown',
        'default' => 'App\Summaries\Breakdown'
    ];
}
