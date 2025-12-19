<div class="bg-gray-50 dark:bg-gray-900 min-h-screen pb-20 transition-colors duration-300">
    <!-- Hero Section -->
    <div class="relative h-96 bg-gray-900">
        <img 
            src="{{ $studio->image ? Storage::url($studio->image) : 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80' }}" 
            alt="{{ $studio->name }}" 
            class="w-full h-full object-cover opacity-60"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-8 max-w-7xl mx-auto">
            <a href="{{ route('studios.index') }}" class="text-white/80 hover:text-white flex items-center mb-4 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Resources
            </a>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">{{ $studio->name }}</h1>
            <div class="flex items-center text-white/90">
                <span class="text-2xl font-semibold text-primary-500">₹{{ number_format($studio->hourly_rate) }}</span>
                <span class="text-sm ml-1 opacity-80">/ hour</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-10 relative z-10 space-y-8 studio-details-container">
        <!-- Description and Amenities with Sidebar (80/20 split) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content (80%) -->
            <div class="md:col-span-2">
                <!-- Description -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8 transition-colors duration-300">
                    <h2 class="text-2xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent mb-4">About this Resource</h2>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                        {{ $studio->description }}
                    </p>
                </div>

                <!-- Features / Amenities -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8 mt-8 transition-colors duration-300">
                    <h2 class="text-2xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent mb-6">Features & Amenities</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @if($studio->amenities)
                            @foreach($studio->amenities as $amenity)
                                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600">
                                    <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-300 mr-4">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $amenity }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No specific amenities listed.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar (Booking CTA) (20%) -->
            <div class="md:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sticky top-6 border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    <h3 class="text-xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent mb-4">Ready to Record?</h3>
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-300">Capacity</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $studio->capacity }} People</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-300">Opening Hours</span>
                            <span class="font-medium text-gray-900 dark:text-white">9:00 AM - 10:00 PM</span>
                        </div>
                    </div>
                    
                    <a 
                        href="{{ route('studios.book', $studio) }}"
                        class="block w-full bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white text-center py-4 rounded-xl font-bold text-lg shadow-lg shadow-black/30 hover:shadow-xl hover:shadow-black/40 hover:scale-[1.02] transition-all duration-300 border-none"
                    >
                        Book Now
                    </a>
                    <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-4">
                        Instant confirmation • Secure payment
                    </p>
                </div>
            </div>
        </div>

        <!-- Gallery (Full Width) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8 transition-colors duration-300" x-data="{ 
            activeSlide: 0, 
            slides: {{ json_encode(collect($studio->gallery)->map(fn($img) => Storage::url($img))->toArray()) }},
            lightboxOpen: false
        }">
            <h2 class="text-2xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent mb-6">Gallery</h2>
            
            <!-- Grid View -->
            <div class="grid grid-cols-3 gap-4">
                <template x-for="(slide, index) in slides" :key="index">
                    <div 
                        class="relative group rounded-xl overflow-hidden cursor-pointer bg-gray-900 aspect-square"
                        @click="activeSlide = index; lightboxOpen = true"
                    >
                        <img 
                            :src="slide" 
                            class="w-full h-full object-cover group-hover:opacity-90 transition-opacity"
                            :alt="'Gallery image ' + (index + 1)"
                        >
                        <!-- Hover overlay with zoom icon -->
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="slides.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
                No images available in gallery.
            </div>

            <!-- Lightbox Modal -->
            <div 
                x-show="lightboxOpen" 
                class="studio-lightbox fixed inset-0 z-[9999] flex items-center justify-center p-4"
                x-transition.opacity
                @keydown.escape.window="lightboxOpen = false"
                @keydown.arrow-left.window="activeSlide = activeSlide === 0 ? slides.length - 1 : activeSlide - 1"
                @keydown.arrow-right.window="activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1"
                @click.self="lightboxOpen = false"
            >
                <!-- Close Button at Top -->
                <button 
                    @click="lightboxOpen = false" 
                    class="absolute top-16 right-16 md:top-16 md:right-16 z-[100] bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full p-3 md:p-4 transition-all shadow-lg group border border-white/20 hover:border-white/40"
                    title="Close (ESC)"
                >
                    <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Content Container (centered vertically) -->
                <div class="relative flex items-center justify-center gap-4 md:gap-6 w-full h-full px-4">
                    <!-- Left Arrow -->
                    <button 
                        @click="activeSlide = activeSlide === 0 ? slides.length - 1 : activeSlide - 1"
                        class="flex-shrink-0 z-[100] bg-black/50 hover:bg-black/70 backdrop-blur-sm rounded-full p-3 md:p-4 transition-all shadow-lg group"
                        title="Previous (←)"
                    >
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <!-- Image Container -->
                    <div class="relative max-w-4xl max-h-[70vh] w-full">
                        <img 
                            :src="slides[activeSlide]" 
                            class="w-full h-full object-contain rounded-lg shadow-2xl"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                        >
                    </div>

                    <!-- Right Arrow -->
                    <button 
                        @click="activeSlide = activeSlide === slides.length - 1 ? 0 : activeSlide + 1"
                        class="flex-shrink-0 z-[100] bg-black/50 hover:bg-black/70 backdrop-blur-sm rounded-full p-3 md:p-4 transition-all shadow-lg group"
                        title="Next (→)"
                    >
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
