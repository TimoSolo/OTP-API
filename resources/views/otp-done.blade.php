<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>
        <div class="mb-4 text-sm text-green-600 text-center">
            Congrats, you cracked the code!
        </div>
        <img class="m-auto" width="250" src="https://cdn.dribbble.com/users/756147/screenshots/2494603/unlock_animaiton.gif" />
        <form class="flex items-center justify-center" method="GET" action="{{ route('otp') }}">
            @csrf
               <x-button >
                    Try Again!
                </x-button>
        </form>
    </x-auth-card>
</x-guest-layout>
