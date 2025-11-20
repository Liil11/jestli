<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>jestli — Share your moments</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-darkGray min-h-screen flex items-center justify-end p-6 mr-20 text-white font-light">
  <img src="{{ asset('image/kucing.png') }}" class="absolute left-0 bottom-0 h-auto w-2/3" alt="Decorative Image">


  <div class=" bg-darkDeep rounded-2xl shadow-lg overflow-hidden flex w-dvh">
    

    <div class="p-10 flex flex-col justify-center gap-4 ">
      <div class="space-y-2 flex flex-col">
        <p>Username</p>
        <input type="text" class="bg-darkest rounded-md w-72 border-0 focus:border-tealPrimary focus:ring-tealPrimary">
        <p>Password</p>
        <input type="password" class="bg-darkest rounded-md w-72 border-0 focus:border-tealPrimary focus:ring-tealPrimary">
        <a href="" class="text-xs text-blue-600 w-fit ml-auto">Forgot Password?</a>
      </div>
      


      @if (Route::has('login'))
        <div class="flex flex-col gap-2">
          
          @auth
            <a href="{{ route('dashboard') }}" class="mt-10 bg-cyan-500 text-white rounded-md w-72 py-2">Go to Feed</a>
          @else
            <a href="{{ route('login') }}" class="font-medium mt-10 block w-72 text-center px-5 py-2 bg-tealPrimary text-white rounded-md">Login</a>

          @endauth
          
        </div>
        
      @endif
      
      <div class="flex items-center gap-3 w-72 mt-2">
        <div class="flex-1 h-px bg-gray-600"></div>
          <span class="text-gray-400 text-xs">OR</span>
        <div class="flex-1 h-px bg-gray-600"></div>
      </div>

      <p class="text-xs text-gray-400 text-center w-72">
      Didn't have an account?
      <a href="#" class="text-blue-600">Sign Up</a>
      </p>
    </div>
  </div>
</body>
</html>
