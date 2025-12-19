<div>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden transition-colors duration-300">
        <!-- Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-10 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-primary-500 blur-[100px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-primary-600 blur-[100px]"></div>
        </div>

        <div class="max-w-md w-full space-y-8 relative z-10">
            <div class="flex flex-col items-center">
                
                    @if($logo = App\Models\Setting::get('company_logo'))
                                <img src="{{ Storage::url($logo) }}" alt="{{ App\Models\Setting::get('company_name', 'Studio Booking') }}" class="h-auto max-h-12 w-auto">
                            @else
                                <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600">
                                    {{ App\Models\Setting::get('company_name', 'Studio Booking') }}
                                </span>
                            @endif
                
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    Enter your phone number to get started
                </p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                @if (session()->has('message'))
                    <div class="mb-4 p-4 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-700 text-primary-600 dark:text-primary-300 rounded-lg">
                        {{ session('message') }}
                    </div>
                @endif

                @if (!$isCodeSent)
                    <!-- Phone Number Form -->
                    <form wire:submit.prevent="sendCode">
                        <div class="space-y-4">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Phone Number
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">+91</span>
                                    </div>
                                    <input 
                                        type="text" 
                                        wire:model="phone" 
                                        id="phone"
                                        placeholder="9876543210"
                                        maxlength="10"
                                        class="block w-full pl-12 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('phone') border-red-500 @enderror caret-primary-500"
                                    >
                                </div>
                                @error('phone') 
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <button 
                                type="submit" 
                                wire:loading.attr="disabled"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md shadow-black/30 text-sm font-medium text-white bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 hover:shadow-lg hover:shadow-black/40 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            >
                                <span wire:loading.remove wire:target="sendCode">Send Verification Code</span>
                                <span wire:loading wire:target="sendCode">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>

                            <div class="mt-4 text-center">
                                <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-primary-500 dark:hover:text-primary-500 transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                    Back to Studios
                                </a>
                            </div>
                        </div>
                    </form>
                @else
                    <!-- Verification Code Form -->
                    <form wire:submit.prevent="verifyCode">
                        <div class="space-y-4">
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Verification Code
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Enter the 4-digit code sent to +91{{ $phone }}
                                </p>
                                <input 
                                    type="text" 
                                    wire:model="verificationCode" 
                                    id="code"
                                    placeholder="1234"
                                    maxlength="4"
                                    class="mt-2 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm text-center text-2xl tracking-widest bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('verificationCode') border-red-500 @enderror caret-primary-500"
                                    autofocus
                                >
                                @error('verificationCode') 
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <button type="submit" 
                                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 shadow-md shadow-black/30 hover:shadow-lg hover:shadow-black/40 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                    wire:loading.attr="disabled">
                                    <span class="absolute left-0 inset-y-0 flex items-center pl-3" wire:loading.remove wire:target="verifyCode">
                                        <svg class="h-5 w-5 text-white/80 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    <span wire:loading.remove wire:target="verifyCode">Verify Phone Number</span>
                                    <span wire:loading wire:target="verifyCode">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Verifying...
                                    </span>
                                </button>
                                
                                <div class="mt-4 text-center">
                                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-primary-500 dark:hover:text-primary-500 transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                        Back to Studios
                                    </a>
                                </div>
                            </div>

                            <button 
                                type="button"
                                wire:click="resendCode"
                                wire:loading.attr="disabled"
                                class="w-full flex justify-center py-2 px-4 text-sm font-medium text-primary-500 hover:text-primary-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-colors"
                            >
                                <span wire:loading.remove wire:target="resendCode">Resend Code</span>
                                <span wire:loading wire:target="resendCode">
                                    <svg class="animate-spin h-4 w-4 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Resending...
                                </span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
```
