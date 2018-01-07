@component('mail::message')
# Introduction

{{ $invitation->message }}

@component('mail::button', ['url' => route('surveys.completions.create', [
    'survey' => $invitation->survey_id,
    'token' => $invitation->token
    ])])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
