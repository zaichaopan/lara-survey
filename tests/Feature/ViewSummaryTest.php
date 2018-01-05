<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function only_author_can_view_survey_summary()
    {
    }

    public function author_can_view_surrvey_summary()
    {
    }
}
