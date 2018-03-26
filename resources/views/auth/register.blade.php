@extends('layouts.guest')

@section('guest.content')
<div class="container flex mx-auto justify-center items-center min-h-screen">
    <div class="w-full max-w-xs border-t-4 border-teal rounded-b pt-3 rounded shadow-md">

        <div class="text-grey-dark text-center">
            <h2>Create a FREE account</h2>
            <p class="pb-3 pt-1 text-sm">...and build your survey in seconds</p>
        </div>

        <form class="px-4 pt-6 pb-8" method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}

            <div class="mb-4">
                <input class="bg-grey-lighter appearance-none border-2 border-grey-lighter hover:border-purple rounded w-full py-2 px-4 text-grey-darker"
                       id="name"
                       type="text"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="Username"
                       autofocus>

                    @if ($errors->has('name'))
                        <p class="text-red text-xs italic mt-3">{{ $errors->first('name')}}</p>
                    @endif
            </div>

            <div class="mb-4">
                <input class="bg-grey-lighter appearance-none border-2 border-grey-lighter hover:border-purple rounded w-full py-2 px-4 text-grey-darker"
                       id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="Email"
                       autofocus>

                @if ($errors->has('email'))
                <p class="text-red text-xs italic mt-3">{{ $errors->first('email')}}</p>
                @endif
            </div>

            <div class="mb-6">
                <input type="password"
                       class="bg-grey-lighter appearance-none border-2 border-grey-lighter hover:border-purple rounded w-full py-2 px-4 text-grey-darker"
                       id="password" name="password"
                       placeholder="Password"
                       required>

                @if ($errors->has('password'))
                <p class="text-red text-xs italic mt-3">{{ $errors->first('password')}}</p>
                @endif
            </div>

            <div class="mb-6">
                <input type="password"
                       class="bg-grey-lighter appearance-none border-2 border-grey-lighter hover:border-purple rounded w-full py-2 px-4 text-grey-darker"
                       id="password-confirm"
                       name="password_confirmation"
                       placeholder="Confirm password"
                       required>
            </div>

            <div class="flex w-full">
                <button class="bg-teal hover:bg-teal-light text-white font-bold py-2 px-4 rounded-full w-full" type="submit">
                   Register
                </button>
            </div>
        </form>
        <p class="text-center text-grey-darker text-xs bg-grey-lighter py-6">
            Alreay have an account? <a href="{{ route('login') }}" class="font-bold text-grey-darkest text-sm">Login</a>
        </p>

    </div>
</div>
@endsection
