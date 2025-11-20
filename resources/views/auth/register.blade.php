<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>jestli — Create your account</title>
  @vite('resources/css/app.css')
</head>

<body class="bg-darkGray min-h-screen flex items-center justify-end p-6 mr-20 text-white font-light">

  <img src="{{ asset('image/kucing.png') }}" 
       class="absolute left-0 bottom-0 h-auto w-2/3" 
       alt="Decorative Image">

  <div class="bg-darkDeep rounded-2xl shadow-lg overflow-hidden flex w-dvh">

    {{-- FORM REGISTER --}}
    <form method="POST" action="{{ route('register') }}" class="p-10 flex flex-col justify-center gap-4">
      @csrf

      <div class="space-y-2 flex flex-col">

        {{-- NAME --}}
        <p class="text-[15px]">Username</p>
        <input type="text" name="name"
               class="bg-darkest rounded-md w-72 border-0 focus:border-tealPrimary focus:ring-tealPrimary"
               required>

        {{-- EMAIL --}}
        <p class="text-[15px]">Email</p>
        <input type="email" name="email"
               class="bg-darkest rounded-md w-72 border-0 focus:border-tealPrimary focus:ring-tealPrimary"
               required>

        {{-- PASSWORD --}}
        <p class="text-[15px]">Password</p>
        <input type="password" name="password"
               class="bg-darkest rounded-md w-72 border-0 focus:border-tealPrimary focus:ring-tealPrimary"
               required>

        {{-- CONFIRM PASSWORD --}}
        <p class="text-[15px]">Confirm Password</p>
        <input type="password" name="password_confirmation"
               class="bg-darkest rounded-md w-72 border-0 focus:border-tealPrimary focus:ring-tealPrimary"
               required>

      </div>

      {{-- ERROR MESSAGE --}}
      @if ($errors->any())
        <div class="text-red-400 text-xs mt-2">
          {{ $errors->first() }}
        </div>
      @endif

      <button type="submit"
              class="font-medium mt-8 w-72 text-center px-5 py-2 bg-tealPrimary text-white rounded-md">
        Sign Up
      </button>

      <div class="flex items-center gap-3 w-72 mt-3">
        <div class="flex-1 h-px bg-gray-600"></div>
        <span class="text-gray-400 text-xs">OR</span>
        <div class="flex-1 h-px bg-gray-600"></div>
      </div>

      <p class="text-xs text-gray-400 text-center w-72">
        Already have an account?
        <a href="{{ route('login') }}" class="text-blue-600">Login</a>
      </p>

    </form>
  </div>
</body>
</html>
