@extends('layouts.guest')
@section('guest.content')
<div class="container mx-auto flex justify-center items-center min-h-screen">
    <div class="w-full max-w-xs border-t-4 border-teal rounded-b pt-3 rounded shadow-md">

        <h2 class="text-center text-grey-darker p-3">Welcome Back</h2>
        <form class="px-4 pt-6 pb-8" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            <div class="mb-4">

                <input class="bg-grey-lighter appearance-none border-2 border-grey-lighter hover:border-teal-light rounded w-full py-2 px-4 text-grey-darker"
                    id="email" type="email" name="email" value="{{ old('email') }}" placeholder="jane@example.com" required autofocus>

                @if ($errors->has('email'))
                <p class="text-red text-xs italic mt-3">{{ $errors->first('email')}}</p>
                @endif
            </div>

            <div class="mb-6">
                <input type="password" class="bg-grey-lighter appearance-none border-2 border-grey-lighter hover:border-teal-light rounded w-full py-2 px-4 text-grey-darker"
                    id="password" name="password" placeholder="******************" required>

                @if ($errors->has('password'))
                <p class="text-red text-xs italic mt-3">{{ $errors->first('password')}}</p>
                @endif
            </div>

            <div class="flex mb-6 justify-between">
                <label class="block text-grey font-bold hover:text-teal">
                    <input class="mr-2" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="text-xs"> Remember me </span>
                </label>

                <a class="font-bold text-xs text-grey hover:text-teal self-center" href="{{ route('password.request') }}">
                Forgot Password?
                </a>
            </div>

            <div class="flex w-full">
                <button class="bg-teal hover:bg-teal-light text-white font-bold py-2 px-4 rounded-full w-full" type="submit"> Login </button>
            </div>
        </form>
        <p class="text-center text-grey-dark text-xs bg-grey-lighter py-6">
            New to Lara Survey? <a href="{{ route('register') }}" class="font-bold text-grey-darkest text-sm">Create a FREE Account</a>
        </p>
    </div>
</div>
@endsection
