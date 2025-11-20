<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>jestli — Reset Password</title>
  @vite('resources/css/app.css')
</head>

<body class="bg-darkGray min-h-screen flex items-center justify-end p-6 mr-20 text-white font-light">

  <img src="{{ asset('image/kucing.png') }}" 
       class="absolute left-0 bottom-0 h-auto w-2/3" 
       alt="Decorative Image">

  <div class="bg-darkDeep rounded-2xl shadow-lg overflow-hidden flex w-dvh">

    {{-- FORM FORGOT PASSWORD --}}
    <form method="POST" action="{{ route('password.email') }}" class="p-10 flex flex-col justify-center gap-4">
      @csrf

      <h2 class="text-xl font-semibold mb-4">Forgot Password</h2>

      <p class="text-sm text-gray-400 w-72">
        Enter your email and we’ll send you a reset password link.
      </p>

      {{-- EMAIL --}}
      <div class="mt-4 space-y-2 flex flex-col">
        <p>Email</p>
        <input type="email" name="email" 
               class="bg-darkest rounded-md w-72 border-0 focus:border-tealPrimary focus:ring-tealPrimary" 
               value="{{ old('email') }}"
               required autofocus>
      </div>

      {{-- SUCCESS MESSAGE --}}
      @if (session('status'))
        <div class="text-green-400 text-xs mt-1">
          {{ session('status') }}
        </div>
      @endif

      {{-- ERROR MESSAGE --}}
      @error('email')
        <div class="text-red-400 text-xs mt-1">
          {{ $message }}
        </div>
      @enderror

      {{-- SUBMIT BUTTON --}}
      <button type="submit"
              class="font-medium mt-6 w-72 text-center px-5 py-2 bg-tealPrimary text-white rounded-md">
        Send Reset Link
      </button>

      {{-- BACK LINK --}}
      <p class="text-xs text-gray-400 w-72 text-center mt-3">
        Remember your password?
        <a href="{{ route('login') }}" class="text-blue-600">Login</a>
      </p>
    </form>

  </div>
</body>
</html>
