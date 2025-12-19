<x-layouts.app>
    <div class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        @php
            $page = \App\Models\Page::where('slug', 'about')->first();
            $headerImage = $page?->header_image;
            $contentImage = $page?->content_image;
            $content = $page?->content;
        @endphp

        <!-- Header Section -->
        <div class="relative h-[300px] w-full overflow-hidden">
            <div class="absolute inset-0">
                @if($headerImage)
                    <img src="{{ \Storage::url($headerImage) }}" alt="Page Header" class="h-full w-full object-cover object-center">
                @else
                    <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" alt="Background" class="h-full w-full object-cover object-center">
                @endif
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div class="relative h-full flex items-center justify-center text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight">About Us</h1>
            </div>
        </div>

        <!-- Content Section (Image | Text)-->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24" style="">
            <div class="flex flex-col lg:flex-row gap-8 items-center">
                <!-- Left: Image -->
                <div class="w-full lg:w-1/2 relative rounded-2xl overflow-hidden shadow-2xl h-[500px]">
                    @if($contentImage)
                        <img src="{{ \Storage::url($contentImage) }}" alt="Studio Interior" class="w-full h-full object-cover">
                    @else
                        <img src="https://images.unsplash.com/photo-1598653222000-6b7b7a552625?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" alt="Facility Interior" class="w-full h-full object-cover">
                    @endif
                </div>
                
                <!-- Right: Text -->
                <div class="w-full lg:w-1/2">
                    <div class="prose prose-lg text-gray-600 dark:text-gray-300 space-y-6">
                        @if($content)
                            {!! $content !!}
                        @else
                            <p>
                                We are more than just a booking platform; we are a premium facility management solution designed for professionals. Our mission is to provide a seamless, inspiring environment where your work can thrive without compromise.
                            </p>
                            <p>
                                Equipped with top-tier amenities and professionally designed spaces, we cater to freelancers, businesses, and creative professionals alike. Whether you're hosting a meeting, conducting a workshop, or needing a quiet workspace, our dedicated team is here to support you every step of the way.
                            </p>
                            <p>
                                At {{ App\Models\Setting::get('company_name', 'Company Name') }}, we believe that great results start with a great environment. Come experience the difference.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-white dark:bg-gray-800 py-24 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row gap-12 text-center">
                    <!-- Happy Customers -->
                    <div class="w-full md:w-1/3 flex flex-col items-center p-6 bg-gray-50 dark:bg-gray-700 rounded-2xl border border-gray-100 dark:border-gray-600 shadow-sm hover:shadow-md transition-all">
                        <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center mb-4 text-indigo-600 dark:text-indigo-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">500+</h3>
                        <p class="text-lg text-gray-600 dark:text-gray-300 font-medium">Happy Customers</p>
                    </div>

                    <!-- Number of Bookings -->
                    <div class="w-full md:w-1/3 flex flex-col items-center p-6 bg-gray-50 dark:bg-gray-700 rounded-2xl border border-gray-100 dark:border-gray-600 shadow-sm hover:shadow-md transition-all" >
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mb-4 text-purple-600 dark:text-purple-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">1,200+</h3>
                        <p class="text-lg text-gray-600 dark:text-gray-300 font-medium">Total Bookings</p>
                    </div>

                    <!-- Rating -->
                    <div class="w-full md:w-1/3 flex flex-col items-center p-6 bg-gray-50 dark:bg-gray-700 rounded-2xl border border-gray-100 dark:border-gray-600 shadow-sm hover:shadow-md transition-all">
                        <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center mb-4 text-yellow-600 dark:text-yellow-400">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                        <h3 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">4.9/5</h3>
                        <p class="text-lg text-gray-600 dark:text-gray-300 font-medium">Average Rating</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 border border-gray-100 dark:border-gray-700 my-4 transition-colors duration-300">
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
</x-layouts.app>
