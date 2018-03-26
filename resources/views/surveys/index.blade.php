@extends('layouts.app')

@section('content')
    @forelse ($surveys->chunk(3) as $chunk)
    <div class="px-2 mt-8">
        <div class="flex">
        @foreach ($chunk as $survey)
            <div class="w-1/3 px-2">
                <div class="bg-white h-64 rounded px-6 py-6 flex flex-col justify-between">
                    <div class="text-center flex justify-between">
                        <p class="font-bold text-grey-darker">Web Framework Survey</p>
                        <a  href="{{route('surveys.show', $survey)}}" class="no-underline px-6 py-1 border text-sm border-teal text-teal rounded-full">View</a>
                    </div>

                    <div class="italic text-grey-dark text-sm">
                        No Description provided! Lorem ipsum, dolor sit amet consectetur adipisicing elit. Harum dolores tempora ad minima sequi
                        voluptatem saepe dolore quibusdam maxime ab.
                    </div>
                    <div class="border-grey border-t mt-2 pt-4 flex text-center justify-between">
                        <div>
                            <div class="text-sm text-grey-darker py-2">Participants</div>
                            <div class="text-grey-darkest text-center text-xl">10K</div>
                        </div>
                        <div>
                            <div class="text-sm text-grey-darker py-2">Questions</div>
                            <div class="text-grey-darkest text-center text-xl">10</div>
                        </div>
                        <div>
                            <div class="text-sm text-grey-darker py-2">Status</div>
                            <div class="text-grey-darkest text-center text-xl">Closed</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
    @empty
    <p>No surveys</p>
    @endforelse
@endsection
