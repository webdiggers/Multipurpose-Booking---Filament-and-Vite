<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent">Profile Settings</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">Manage your account information</p>
                </div>

                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="mb-4 p-4 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-700 text-primary-600 dark:text-primary-300 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    <form wire:submit.prevent="updateProfile" class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                            <input 
                                type="text" 
                                wire:model="name" 
                                id="name"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500"
                            >
                            @error('name') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                            <input 
                                type="email" 
                                wire:model="email" 
                                id="email"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500"
                            >
                            @error('email') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    wire:model="phone" 
                                    id="phone"
                                    @if($registrationType !== 'full') disabled @endif
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500 {{ $registrationType !== 'full' ? 'bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 cursor-not-allowed' : '' }}"
                                >
                                @if($registrationType !== 'full')
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            @if($registrationType !== 'full')
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Phone number cannot be changed as it is used for login.</p>
                            @endif
                            @error('phone') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        @if($registrationType === 'full')
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                <input 
                                    type="text" 
                                    wire:model="address" 
                                    id="address"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500"
                                >
                                @error('address') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- City -->
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                                    <input 
                                        type="text" 
                                        wire:model="city" 
                                        id="city"
                                        class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500"
                                    >
                                    @error('city') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- State -->
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State</label>
                                    <input 
                                        type="text" 
                                        wire:model="state" 
                                        id="state"
                                        class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500"
                                    >
                                    @error('state') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Country -->
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                                    <input 
                                        type="text" 
                                        wire:model="country" 
                                        id="country"
                                        class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500"
                                    >
                                    @error('country') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        @if($registrationType === 'email' || $registrationType === 'full')
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password (Optional)</label>
                                <input 
                                    type="password" 
                                    wire:model="password" 
                                    id="password"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500"
                                    placeholder="Leave blank to keep current password"
                                >
                                @error('password') <span class="text-red-600 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                                <input 
                                    type="password" 
                                    wire:model="password_confirmation" 
                                    id="password_confirmation"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 caret-primary-500"
                                >
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex items-center justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white rounded-lg font-medium hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 shadow-md shadow-black/30 hover:shadow-lg hover:shadow-black/40 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 border-none"
                            >
                                <span wire:loading.remove>Save Changes</span>
                                <span wire:loading>
                                    <svg class="animate-spin h-4 w-4 text-white inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
