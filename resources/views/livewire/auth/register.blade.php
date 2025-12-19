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
                    Create your account
                </p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                <form wire:submit.prevent="register">
                    <div class="space-y-4">
                        
                        <!-- Dynamic Fields -->
                        @if($registrationMethod === 'full')
                            @if(in_array('first_name', $shownFields))
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        First Name @if(in_array('first_name', $requiredFields)) <span class="text-red-500">*</span> @endif
                                    </label>
                                    <input type="text" wire:model="first_name" id="first_name" class="mt-1 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('first_name') border-red-500 @enderror caret-primary-500">
                                    @error('first_name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            @if(in_array('last_name', $shownFields))
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Last Name @if(in_array('last_name', $requiredFields)) <span class="text-red-500">*</span> @endif
                                    </label>
                                    <input type="text" wire:model="last_name" id="last_name" class="mt-1 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('last_name') border-red-500 @enderror caret-primary-500">
                                    @error('last_name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        @endif

                        <!-- Email (Always Required) -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" wire:model="email" id="email" required class="mt-1 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('email') border-red-500 @enderror caret-primary-500">
                            @error('email') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        @if($registrationMethod === 'full')
                            @if(in_array('phone', $shownFields))
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Phone Number @if(in_array('phone', $requiredFields)) <span class="text-red-500">*</span> @endif
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm">+91</span>
                                        </div>
                                        <input type="text" wire:model="phone" id="phone" maxlength="10" class="block w-full pl-12 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('phone') border-red-500 @enderror caret-primary-500">
                                    </div>
                                    @error('phone') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            @if(in_array('address', $shownFields))
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Address @if(in_array('address', $requiredFields)) <span class="text-red-500">*</span> @endif
                                    </label>
                                    <textarea wire:model="address" id="address" rows="2" class="mt-1 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('address') border-red-500 @enderror caret-primary-500"></textarea>
                                    @error('address') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                @if(in_array('city', $shownFields))
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            City @if(in_array('city', $requiredFields)) <span class="text-red-500">*</span> @endif
                                        </label>
                                        <input type="text" wire:model="city" id="city" class="mt-1 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('city') border-red-500 @enderror caret-primary-500">
                                        @error('city') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                @if(in_array('state', $shownFields))
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            State @if(in_array('state', $requiredFields)) <span class="text-red-500">*</span> @endif
                                        </label>
                                        <input type="text" wire:model="state" id="state" class="mt-1 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('state') border-red-500 @enderror caret-primary-500">
                                        @error('state') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    </div>
                                @endif
                            </div>

                            @if(in_array('country', $shownFields))
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Country @if(in_array('country', $requiredFields)) <span class="text-red-500">*</span> @endif
                                    </label>
                                    <input type="text" wire:model="country" id="country" class="mt-1 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('country') border-red-500 @enderror caret-primary-500">
                                    @error('country') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        @endif

                        <!-- Password (Always Required) -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" wire:model="password" id="password" required class="mt-1 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 @error('password') border-red-500 @enderror caret-primary-500">
                            @error('password') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" wire:model="password_confirmation" id="password_confirmation" required class="mt-1 block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500">
                        </div>

                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md shadow-black/30 text-sm font-medium text-white bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 hover:shadow-lg hover:shadow-black/40 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="register">Create Account</span>
                            <span wire:loading wire:target="register">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating...
                            </span>
                        </button>

                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Already have an account? 
                                <a href="{{ route('login') }}" class="font-medium text-primary-500 hover:text-primary-600 transition-colors">
                                    Sign in here
                                </a>
                            </p>
                        </div>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-primary-500 dark:hover:text-primary-500 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Studios
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
