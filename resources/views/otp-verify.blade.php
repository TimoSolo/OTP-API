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

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <div class="mb-4 text-sm text-gray-600">
            Please enter the One Time Code below.
        </div>

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

                <x-input id="code" class="block mt-1 w-full" type="number" name="code" :value="$code" autofocus />
            </div>

            <div class="flex mt-4">
                <div class="flex-1">
                    <x-button class="float-left" style="background-color: rgb(107, 114, 128) !important" name="resend" value="resend" formaction="{{ route('otp.send') }}">
                        Send New Code
                    </x-button>
                </div>
                <div>
                    <x-button class="float-right ml-3" name="verify" value="verify">
                        Verify Code
                    </x-button>
                </div>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>