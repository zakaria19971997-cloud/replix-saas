@php
    $pricing = \Pricing::plansWithFeatures();
    $planTypes = \Modules\AdminPlans\Facades\Plan::getTypes();
    $minCol = 3;
@endphp

<section x-data="{ type: {{ array_key_first($planTypes) }} }" class="relative pt-24 pb-40" style="overflow: visible; background: linear-gradient(180deg, #f3fbf5 0%, #ffffff 100%); z-index: 20;">
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none opacity-60" style="background-image: radial-gradient(circle at 15% 20%, rgba(34, 197, 94, 0.10), transparent 28%), radial-gradient(circle at 85% 15%, rgba(22, 163, 74, 0.10), transparent 25%), radial-gradient(circle at 50% 100%, rgba(110, 231, 183, 0.12), transparent 35%);"></div>

    <div class="relative container px-4 mx-auto" style="overflow: visible; z-index: 20;">
        <div class="md:max-w-4xl mx-auto text-center mb-14">
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold mb-6" style="background: #dcfce7; color: #166534;">
                <i class="fa-brands fa-whatsapp mr-2"></i>
                Pricing built for WhatsApp growth
            </span>
            <h2 class="mb-6 text-6xl md:text-7xl xl:text-8xl font-bold font-heading tracking-px-n leading-tight">
                Pick the plan that matches your message volume and team size
            </h2>
            <p class="text-lg text-gray-600 font-medium leading-relaxed md:max-w-3xl mx-auto">
                Clean pricing, scalable automation, and enough operational headroom to run support, sales, and campaign workflows from one place.
            </p>
        </div>

        <div class="flex justify-center mb-12">
            <div class="inline-flex items-center rounded-full p-2 border" style="background: #ffffff; border-color: #bbf7d0; box-shadow: 0 20px 40px rgba(21, 128, 61, 0.08);">
                @foreach($planTypes as $typeKey => $typeLabel)
                    <button
                        type="button"
                        class="px-5 py-2 rounded-full font-semibold transition"
                        :class="type == {{ $typeKey }} ? '' : 'text-gray-600'"
                        :style="type == {{ $typeKey }} ? 'background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); color:#fff;' : 'background: transparent;'"
                        x-on:click="type={{ $typeKey }}"
                    >
                        {{ __($typeLabel) }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex flex-wrap -m-4 items-stretch" style="overflow: visible;">
            @foreach($planTypes as $typeKey => $typeLabel)
                @php
                    $plans = $pricing[$typeKey] ?? [];
                    $planCount = count($plans);
                @endphp

                @foreach($plans as $index => $plan)
                    @php $isFreePlan = $plan['free_plan']; @endphp
                    <div class="w-full lg:w-1/3 p-4" x-show="type == {{ $typeKey }}" x-transition style="display:none;">
                        <div class="relative h-full" style="overflow: visible; z-index: 30;">
                        <div class="relative h-full rounded-[2rem] border overflow-hidden" style="border-color: {{ !empty($plan['featured']) ? '#22c55e' : '#dcfce7' }}; background: #ffffff; box-shadow: {{ !empty($plan['featured']) ? '0 26px 60px rgba(22, 163, 74, 0.16)' : '0 20px 50px rgba(15, 23, 42, 0.05)' }};">
                            @if(!empty($plan['featured']))
                                <div class="absolute top-5 right-5 z-10">
                                    <span class="inline-flex items-center rounded-full px-4 py-2 text-xs font-bold uppercase tracking-wide" style="background: #dcfce7; color: #166534;">
                                        Most popular
                                    </span>
                                </div>
                            @endif

                            <div class="p-8 pb-6" style="background: {{ !empty($plan['featured']) ? 'linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%)' : 'linear-gradient(180deg, #f8fffa 0%, #ffffff 100%)' }};">
                                <div class="mb-6">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide" style="background: #ecfdf3; color: #15803d;">
                                        {{ __($plan['name'] ?? '-') }}
                                    </span>
                                </div>

                                <p class="mb-6 text-gray-500 font-medium leading-relaxed min-h-[56px]">
                                    {{ __($plan['desc'] ?? '') }}
                                </p>

                                <div class="mb-8">
                                    <div class="flex items-end gap-3 mb-2">
                                        <h3 class="text-gray-900 font-bold leading-none" style="font-size: 58px; line-height: 0.95;">
                                            @if($isFreePlan)
                                                {{ price(0) }}
                                            @else
                                                {{ price($plan['price'] ?? 0) }}
                                            @endif
                                        </h3>
                                        <span class="text-gray-400 font-semibold mb-2" style="font-size: 18px;">/{{ strtolower($typeLabel) }}</span>
                                    </div>
                                    <p class="text-gray-500 font-medium" style="font-size: 16px;">{{ __("Billed") }} {{ $typeLabel }}</p>
                                </div>

                                @if($isFreePlan)
                                    <a href="{{ route('payment.index', $plan['id_secure']) }}" class="mb-8 py-4 px-6 w-full font-semibold rounded-xl transition ease-in-out duration-200 text-center block border" style="border-color: #16a34a; color: #166534; background: #ffffff;">
                                        {{ __("Start for Free") }}
                                    </a>
                                @else
                                    <a href="{{ route('payment.index', $plan['id_secure']) }}" class="mb-8 py-4 px-6 w-full font-semibold rounded-xl text-white transition ease-in-out duration-200 text-center block" style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);">
                                        {{ __("Choose Plan") }}
                                    </a>
                                @endif
                            </div>

                            <div class="px-8 pb-8">
                                <div class="rounded-[1.5rem] p-5 mb-6" style="background: #f7fcf8;">
                                    <div class="text-sm font-semibold text-gray-700 mb-1">Included in this plan</div>
                                    <div class="text-xs text-gray-500">Channels, limits, and automation controls for day-to-day WhatsApp operations.</div>
                                </div>

                                <ul class="space-y-4">
                                    @foreach($plan['features'] ?? [] as $feature)
                                        <li class="flex items-start gap-3">
                                            <span class="mt-0.5 inline-flex items-center justify-center w-6 h-6 rounded-full {{ $feature['check'] ? '' : '' }}" style="background: {{ $feature['check'] ? '#dcfce7' : '#f3f4f6' }}; color: {{ $feature['check'] ? '#15803d' : '#6b7280' }};">
                                                <i class="fa-solid {{ $feature['check'] ? 'fa-check' : 'fa-minus' }} text-[11px]"></i>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between gap-3">
                                                    <p class="font-semibold text-gray-800 leading-normal">{{ __($feature['label'] ?? $feature) }}</p>
                                                    @if(($feature['display'] ?? null) !== null && ($feature['display'] ?? '') !== '')
                                                        <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" style="background: #ecfdf3; color: #166534;">
                                                            {{ $feature['display'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if(!empty($feature['subfeature']))
                                                <div
                                                    x-data="{ open: false, timer: null, panelStyle: '' }"
                                                    class="relative ml-1"
                                                >
                                                    <div
                                                        x-ref="trigger"
                                                        @mouseenter="
                                                            clearTimeout(timer);
                                                            open = true;
                                                            $nextTick(() => {
                                                                const rect = $refs.trigger.getBoundingClientRect();
                                                                const width = Math.min(360, window.innerWidth - 32);
                                                                const left = Math.max(16, Math.min(rect.right - width, window.innerWidth - width - 16));
                                                                panelStyle = `position: fixed; top: ${rect.bottom + 12}px; left: ${left}px; width: ${width}px; max-height: 420px; z-index: 999999;`;
                                                            });
                                                        "
                                                        @mouseleave="timer = setTimeout(() => open = false, 120)"
                                                        class="w-5 h-5 flex items-center justify-center rounded-full text-xs transition cursor-pointer z-20 relative"
                                                        style="background: #dcfce7; color: #166534;"
                                                    ><i class="fa-light fa-info"></i></div>
                                                    <div
                                                        x-show="open"
                                                        @mouseenter="clearTimeout(timer); open = true"
                                                        @mouseleave="timer = setTimeout(() => open = false, 120)"
                                                        x-bind:style="panelStyle + ' overflow-y: auto; border-color: #dcfce7; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);'"
                                                        class="rounded-xl border bg-white text-gray-800 p-4 shadow-xl"
                                                        x-transition
                                                    >
                                                        @foreach($feature['subfeature'] as $tabGroup)
                                                            <div class="mb-5 last:mb-0">
                                                                <div class="font-semibold text-xs uppercase tracking-wide text-gray-500 mb-3 text-left">
                                                                    {{ __($tabGroup['tab_name']) }}
                                                                </div>
                                                                <ul class="text-sm space-y-2 text-left">
                                                                    @foreach($tabGroup['items'] as $sub)
                                                                        <li class="flex min-w-[260px] items-start justify-between gap-3 rounded-xl border px-3 py-2" style="border-color: #ecfdf3; background: #f7fcf8;">
                                                                            <div class="flex items-start gap-2 min-w-0 flex-1">
                                                                                <span class="mt-0.5 w-5 h-5 flex shrink-0 items-center justify-center rounded-full" style="background: {{ $sub['check'] ? '#dcfce7' : '#f3f4f6' }}; color: {{ $sub['check'] ? '#15803d' : '#6b7280' }};">
                                                                                    <i class="fa-solid {{ $sub['check'] ? 'fa-check' : 'fa-xmark' }} text-[11px]"></i>
                                                                                </span>
                                                                                <span class="min-w-0 leading-5 text-gray-800">{{ __($sub['label']) }}</span>
                                                                            </div>
                                                                            @if(($sub['display'] ?? null) !== null && ($sub['display'] ?? '') !== '')
                                                                                <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold" style="background: #dcfce7; color: #166534;">
                                                                                    {{ $sub['display'] }}
                                                                                </span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        </div>
                    </div>
                @endforeach

                @for($i = $planCount; $i < $minCol; $i++)
                    <div class="hidden lg:block lg:w-1/3 p-4" x-show="type == {{ $typeKey }}" style="display:none;"></div>
                @endfor
            @endforeach
        </div>

        <div class="mt-14 text-center">
            <p class="mb-4 text-sm text-gray-500 text-center font-medium leading-relaxed">
                {{ __("Trusted by secure payment service") }}
            </p>
            <div class="flex flex-wrap gap-2 justify-center opacity-80">
                <div class="w-auto"><img class="h-24" src="{{ theme_public_asset('logos/brands/stripe.svg') }}" alt="Stripe"></div>
                <div class="w-auto"><img class="h-24" src="{{ theme_public_asset('logos/brands/amex.svg') }}" alt="Amex"></div>
                <div class="w-auto"><img class="h-24" src="{{ theme_public_asset('logos/brands/mastercard.svg') }}" alt="Mastercard"></div>
                <div class="w-auto"><img class="h-24" src="{{ theme_public_asset('logos/brands/paypal.svg') }}" alt="Paypal"></div>
                <div class="w-auto"><img class="h-24" src="{{ theme_public_asset('logos/brands/visa.svg') }}" alt="Visa"></div>
                <div class="w-auto"><img class="h-24" src="{{ theme_public_asset('logos/brands/apple-pay.svg') }}" alt="Apple Pay"></div>
            </div>
        </div>
    </div>
</section>
