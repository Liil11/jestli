@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-6">

    {{-- Intro Text --}}
    <div class="mb-10 space-y-4">
        <h1 class="text-2xl font-bold text-white">Contact</h1>
        <p class="text-gray-300 leading-relaxed text-justify">
            A space where users can send feedback, suggestions, or complaints to help improve Jestli. Users can report issues, share concerns, and propose new ideas, allowing the team to respond and continuously make the platform better and more reliable for the community.
        </p>
    </div>

    {{-- Form Section --}}
    <div class="space-y-3">
        <h2 class="text-xl font-semibold text-white">Write your question here</h2>

        {{-- Form Container --}}
        <div class="bg-[#0f0f0f] p-6 rounded-lg border border-grayComp shadow-sm">
            <form action="" method="POST">
                @csrf


                <textarea 
                    name="message" 
                    rows="6" 
                    class="w-full bg-black/30 border border-grayComp rounded-lg p-4 text-white focus:outline-none focus:border-tealPrimary focus:ring-1 focus:ring-tealPrimary resize-none placeholder-gray-500"
                    placeholder="Type your message here..."></textarea>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-tealPrimary hover:bg-teal-600 text-white font-medium py-2 px-8 rounded-md transition duration-200">
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection