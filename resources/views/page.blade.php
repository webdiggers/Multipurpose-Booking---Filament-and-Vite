<x-layouts.app>
    <div class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Header Section -->
        <div class="relative h-[300px] w-full overflow-hidden">
            <div class="absolute inset-0">
                @if($page->header_image)
                    <img src="{{ \Storage::url($page->header_image) }}" alt="{{ $page->title }}" class="h-full w-full object-cover object-center">
                @else
                    <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" alt="Background" class="h-full w-full object-cover object-center">
                @endif
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div class="relative h-full flex items-center justify-center text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight">{{ $page->title }}</h1>
            </div>
        </div>

        <!-- Content Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="prose prose-lg text-gray-600 dark:text-gray-300 max-w-none">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</x-layouts.app>
