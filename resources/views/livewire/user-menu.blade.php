<div class="relative ml-3" x-data="{ open: false }">
    <div>
        <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center max-w-xs rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-transform duration-200 hover:scale-105" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
            <span class="sr-only">Open user menu</span>
            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-lg shadow-md border-2 border-white dark:border-gray-700 shadow-black/30">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        </button>
    </div>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
         class="origin-top-right absolute right-0 mt-2 w-[400px] rounded-xl shadow-2xl py-2 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50 divide-y divide-gray-100 dark:divide-gray-700" 
         role="menu" 
         aria-orientation="vertical" 
         aria-labelledby="user-menu-button" 
         tabindex="-1"
         style="display: none;">
        
        <!-- User Info -->
        <div class="px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50">
            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate font-medium">{{ auth()->user()->phone }}</p>
        </div>
        
        <!-- Menu Items -->
        <div class="py-1">
            <a href="{{ route('profile') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-300 transition-colors" role="menuitem" tabindex="-1">
                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-primary-500 dark:text-gray-500 dark:group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profile
            </a>
            <a href="{{ route('my-bookings') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-300 transition-colors" role="menuitem" tabindex="-1">
                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-primary-500 dark:text-gray-500 dark:group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                My Bookings
            </a>
        </div>
        
        <div class="py-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-400 transition-colors" role="menuitem" tabindex="-1">
                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-red-500 dark:text-gray-500 dark:group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
