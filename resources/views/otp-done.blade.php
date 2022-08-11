<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>
        <div class="mb-4 text-sm text-green-600">
            Congrats, you cracked the code!
        </div>

        <form method="GET" action="{{ route('otp') }}">
            @csrf
               <x-button class="">
                    Try Again!
                </x-button>
        </form>
    </x-auth-card>
</x-guest-layout>
