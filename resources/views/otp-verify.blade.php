<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>
        
        @if (session('status') == 'code-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A One Time Code has been sent to the email address you provided.') }}
        </div>
        @endif

        <div class="mb-4 text-sm text-gray-600">
            Please enter the One Time Code below.
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('otp.check') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$email" required />
            </div>

            <!-- Code -->
            <div>
                <x-label for="code" :value="__('Code')" />

                <x-input id="code" class="block mt-1 w-full" type="number" name="code" :value="$code" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
               <x-button class="ml-3">
                    Verify Code
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
