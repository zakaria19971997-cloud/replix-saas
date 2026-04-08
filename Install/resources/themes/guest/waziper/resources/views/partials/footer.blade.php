<section class="pt-16 overflow-hidden border-t bg-white" style="border-color: #ecfdf3;">
    <div class="container px-4 mx-auto">
        <div class="pb-9 border-b" style="border-color: #ecfdf3;">
            <div class="flex flex-wrap items-center justify-between -m-4">
                <div class="w-auto p-4">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <span class="inline-flex items-center justify-center rounded-full w-11 h-11" style="background: #dcfce7;">
                            <i class="fa-brands fa-whatsapp text-xl" style="color: #15803d;"></i>
                        </span>
                        <div>
                            <div class="text-2xl font-bold leading-none text-gray-900">Waziper</div>
                            <div class="text-xs text-gray-500 mt-1">WhatsApp Marketing Tool</div>
                        </div>
                    </a>
                </div>

                <ul class="flex flex-wrap -m-4 md:-m-6 p-4">
                    <li class="p-4 md:p-6"><a class="font-medium tracking-tight text-gray-700" href="{{ url('') }}">{{ __("Home") }}</a></li>
                    <li class="p-4 md:p-6"><a class="font-medium tracking-tight text-gray-700" href="{{ url('') }}#features">{{ __("Features") }}</a></li>
                    <li class="p-4 md:p-6"><a class="font-medium tracking-tight text-gray-700" href="{{ url('pricing') }}">{{ __("Pricing") }}</a></li>
                    <li class="p-4 md:p-6"><a class="font-medium tracking-tight text-gray-700" href="{{ url('faqs') }}">{{ __("FAQs") }}</a></li>
                    <li class="p-4 md:p-6"><a class="font-medium tracking-tight text-gray-700" href="{{ url('contact') }}">{{ __("Contact") }}</a></li>
                </ul>

                <div class="w-auto p-4">
                    <div class="flex flex-wrap items-center -m-4">
                        @if(get_option("social_page_facebook", ""))
                            <div class="w-auto p-4"><a class="text-gray-800 hover:text-blue-600" href="{{ get_option('social_page_facebook') }}" target="_blank" rel="noopener"><i class="fab fa-facebook fa-lg"></i></a></div>
                        @endif
                        @if(get_option("social_page_instagram", ""))
                            <div class="w-auto p-4"><a class="text-gray-800 hover:text-pink-500" href="{{ get_option('social_page_instagram') }}" target="_blank" rel="noopener"><i class="fab fa-instagram fa-lg"></i></a></div>
                        @endif
                        @if(get_option("social_page_x", ""))
                            <div class="w-auto p-4"><a class="text-gray-800 hover:text-gray-800" href="{{ get_option('social_page_x') }}" target="_blank" rel="noopener"><i class="fab fa-x-twitter fa-lg"></i></a></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 pb-6">
            <div class="flex flex-wrap justify-between items-center -m-4">
                <div class="w-auto p-4">
                    <p class="tracking-tight text-gray-600">© {{ date('Y') }}, {{ __("All Rights Reserved") }}</p>
                </div>
                <div class="w-auto p-4">
                    <div class="flex flex-wrap">
                        <div class="w-auto p-4"><a class="tracking-tight text-gray-600" href="{{ url('privacy-policy') }}">{{ __("Privacy Policy") }}</a></div>
                        <div class="w-auto p-4"><a class="tracking-tight text-gray-600" href="{{ url('terms-of-service') }}">{{ __("Terms & Conditions") }}</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
