<ul>
 @foreach($question->summary() as $optionSummary)
    <li>{{ $optionSummary->option}}: {{$optionSummary->chosenCount}} chosen {{$optionSummary->chosenInPercentage}}</li>
 @endforeach
</ul>
