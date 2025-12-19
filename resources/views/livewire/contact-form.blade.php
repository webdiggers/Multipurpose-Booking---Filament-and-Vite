<div>
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-8">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
            <div class="relative">
                <input type="text" wire:model="first_name" id="first-name" class="peer block w-full border-0 border-b-2 border-gray-200 dark:border-gray-600 bg-transparent py-3 px-0 text-gray-900 dark:text-white placeholder:text-transparent focus:border-indigo-600 dark:focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-colors" placeholder="First Name">
                <label for="first-name" class="absolute top-3 -translate-y-8 scale-75 transform text-sm text-gray-500 dark:text-gray-400 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:left-0 peer-focus:-translate-y-8 peer-focus:scale-75 peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">First name</label>
                @error('first_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="relative">
                <input type="text" wire:model="last_name" id="last-name" class="peer block w-full border-0 border-b-2 border-gray-200 dark:border-gray-600 bg-transparent py-3 px-0 text-gray-900 dark:text-white placeholder:text-transparent focus:border-indigo-600 dark:focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-colors" placeholder="Last Name">
                <label for="last-name" class="absolute top-3 -translate-y-8 scale-75 transform text-sm text-gray-500 dark:text-gray-400 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:left-0 peer-focus:-translate-y-8 peer-focus:scale-75 peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Last name</label>
                @error('last_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
            <div class="relative">
                <input type="email" wire:model="email" id="email" class="peer block w-full border-0 border-b-2 border-gray-200 dark:border-gray-600 bg-transparent py-3 px-0 text-gray-900 dark:text-white placeholder:text-transparent focus:border-indigo-600 dark:focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-colors" placeholder="Email">
                <label for="email" class="absolute top-3 -translate-y-8 scale-75 transform text-sm text-gray-500 dark:text-gray-400 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:left-0 peer-focus:-translate-y-8 peer-focus:scale-75 peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Email address</label>
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="relative">
                <input type="tel" wire:model="phone" id="phone" class="peer block w-full border-0 border-b-2 border-gray-200 dark:border-gray-600 bg-transparent py-3 px-0 text-gray-900 dark:text-white placeholder:text-transparent focus:border-indigo-600 dark:focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-colors" placeholder="Phone">
                <label for="phone" class="absolute top-3 -translate-y-8 scale-75 transform text-sm text-gray-500 dark:text-gray-400 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:left-0 peer-focus:-translate-y-8 peer-focus:scale-75 peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">Phone number</label>
                @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="relative">
            <textarea wire:model="message" id="message" rows="4" class="peer block w-full border-0 border-b-2 border-gray-200 dark:border-gray-600 bg-transparent py-3 px-0 text-gray-900 dark:text-white placeholder:text-transparent focus:border-indigo-600 dark:focus:border-indigo-400 focus:ring-0 sm:text-sm sm:leading-6 transition-colors" placeholder="Message"></textarea>
            <label for="message" class="absolute top-3 -translate-y-8 scale-75 transform text-sm text-gray-500 dark:text-gray-400 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:left-0 peer-focus:-translate-y-8 peer-focus:scale-75 peer-focus:text-indigo-600 dark:peer-focus:text-indigo-400">How can we help you?</label>
            @error('message') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="pt-4">
            <button type="submit" wire:loading.attr="disabled" class="group relative flex w-full justify-center rounded-xl bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 px-8 py-4 text-sm font-semibold text-white shadow-lg shadow-black/30 transition-all duration-300 hover:shadow-xl hover:shadow-black/40 hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed border-none">
                <span wire:loading.remove>Send Message</span>
                <span wire:loading>Sending...</span>
            </button>
        </div>
    </form>
</div>
