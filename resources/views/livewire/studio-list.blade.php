<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <!-- <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Available Studios</h1>
                <p class="mt-2 text-gray-600">Book your perfect recording space</p>
            </div> -->

            <!-- Search and Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6 transition-colors duration-300">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <label for="search" class="sr-only">Search</label>
                        <input 
                            type="text" 
                            wire:model.live="search" 
                            id="search"
                            placeholder="Search resources..."
                            class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-colors duration-300"
                        >
                    </div>

                    <!-- Sort Options -->
                    <div class="flex gap-2">
                        <button 
                            wire:click="setSortBy('hourly_rate')"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-wait"
                            class="px-4 py-2 border rounded-lg text-sm font-medium {{ $sortBy === 'hourly_rate' ? 'bg-primary-50 dark:bg-primary-900/30 border-primary-500 text-primary-600 dark:text-primary-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors duration-300"
                        >
                            Price {{ $sortBy === 'hourly_rate' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                        <button 
                            wire:click="setSortBy('name')"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-wait"
                            class="px-4 py-2 border rounded-lg text-sm font-medium {{ $sortBy === 'name' ? 'bg-primary-50 dark:bg-primary-900/30 border-primary-500 text-primary-600 dark:text-primary-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors duration-300"
                        >
                            Name {{ $sortBy === 'name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                        <button 
                            wire:click="setSortBy('capacity')"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-wait"
                            class="px-4 py-2 border rounded-lg text-sm font-medium {{ $sortBy === 'capacity' ? 'bg-primary-50 dark:bg-primary-900/30 border-primary-500 text-primary-600 dark:text-primary-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors duration-300"
                        >
                            Capacity {{ $sortBy === 'capacity' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Studios Grid -->
            @if($studios->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($studios as $studio)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col h-full border border-gray-100 dark:border-gray-700">
                            <a href="{{ route('studios.show', $studio->id) }}" class="block group flex-shrink-0">
                                <!-- Studio Image -->
                                @if($studio->image)
                                    <img 
                                        src="{{ Storage::url($studio->image) }}" 
                                        alt="{{ $studio->name }}" 
                                        class="w-full h-48 object-cover group-hover:opacity-90 transition-opacity"
                                    >
                                @else
                                    <div class="h-48 bg-gradient-to-br from-primary-300 to-primary-600 flex items-center justify-center group-hover:opacity-90 transition-opacity">
                                        <span class="text-6xl">🎵</span>
                                    </div>
                                @endif

                                <!-- Studio Details -->
                                <div class="p-6 pb-0">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-primary-500 transition-colors">{{ $studio->name }}</h3>
                                </div>
                            </a>
                            
                            <div class="px-6 pb-6 pt-2 flex-grow flex flex-col">
                                <div class="flex-grow">
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">{{ $studio->description }}</p>

                                    <!-- Pricing -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <span class="text-3xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent">₹{{ number_format($studio->hourly_rate) }}</span>
                                            <span class="text-gray-500 dark:text-gray-400 text-sm">/hour</span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Capacity</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $studio->capacity }} people</p>
                                        </div>
                                    </div>

                                    <!-- Amenities -->
                                    @if($studio->amenities && count($studio->amenities) > 0)
                                        <div class="mb-4">
                                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Amenities:</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_slice($studio->amenities, 0, 3) as $amenity)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-300">
                                                        {{ $amenity }}
                                                    </span>
                                                @endforeach
                                                @if(count($studio->amenities) > 3)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                        +{{ count($studio->amenities) - 3 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Book Now Button -->
                                <a 
                                    href="{{ route('studios.book', $studio->id) }}" 
                                    class="block w-full text-center bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white shadow-md shadow-black/30 hover:shadow-lg hover:shadow-black/40 hover:scale-[1.02] font-semibold py-3 px-4 rounded-lg transition-all duration-300 mt-4 mb-4 border-none"
                                >
                                    Book Now
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center transition-colors duration-300">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No resources found</h3>
                    <p class="text-gray-600 dark:text-gray-300">Try adjusting your search criteria</p>
                </div>
                @endif
            
            <!-- Social Media Section -->
            @php
                $socialLinks = App\Models\Setting::get('social_media_links', []);
                $socialIcons = [
                    'facebook' => ['icon' => '<svg fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>', 'color' => '#1877F2'],
                    'instagram' => ['icon' => '<svg fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/></svg>', 'color' => '#E4405F'],
                    'linkedin' => ['icon' => '<svg fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>', 'color' => '#0A66C2'],
                    'youtube' => ['icon' => '<svg fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>', 'color' => '#FF0000'],
                    'tiktok' => ['icon' => '<svg fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>', 'color' => '#000000'],
                    'whatsapp' => ['icon' => '<svg fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>', 'color' => '#25D366'],
                ];
            @endphp

            @if(!empty($socialLinks))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 border border-gray-100 dark:border-gray-700 transition-colors duration-300" style="margin-top: 100px;">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Connect With Us</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Follow us on social media for updates and behind-the-scenes content</p>
                        
                        <div class="flex items-center justify-center gap-4 flex-wrap">
                            @foreach($socialLinks as $link)
                                @if(isset($link['platform']) && isset($link['url']) && isset($socialIcons[$link['platform']]))
                                    <a href="{{ $link['url'] }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="social-icon social-{{ $link['platform'] }} hover:opacity-80 transition-opacity"
                                       style=""
                                       title="{{ ucfirst($link['platform']) }}">
                                        {!! str_replace('w-5 h-5', 'w-8 h-8', $socialIcons[$link['platform']]['icon']) !!}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
