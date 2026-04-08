@php
    $faqs = Home::getFaqs();
    $firstFaqId = $faqs->first()->id ?? null;
@endphp

<section class="bg-[#f3fbf6] py-20 md:py-28">
    <div class="container mx-auto px-4">
        <div class="grid gap-8 lg:grid-cols-[0.95fr_1.35fr] lg:gap-12">
            <div class="lg:sticky lg:top-28 lg:self-start">
                <span class="inline-flex items-center rounded-full border border-[#9ad9af] bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-[#1f7a45]">
                    {{ __("Frequently asked questions") }}
                </span>
                <h2 class="mt-6 max-w-xl text-4xl font-bold leading-tight text-[#112119] md:text-5xl">
                    {{ __("Clear answers for teams running WhatsApp at scale") }}
                </h2>
                <p class="mt-5 max-w-lg text-lg leading-8 text-[#527060]">
                    {{ __("Everything here is written for real operators: lead capture, inbox routing, campaign sending, and account safety.") }}
                </p>

                <div class="mt-8 rounded-[2rem] border border-[#cfe9d8] bg-white p-7 shadow-[0_24px_80px_rgba(44,122,68,0.08)]">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#dff6e7] text-xl text-[#1f7a45]">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#5f8b6d]">
                                {{ __("Need more help?") }}
                            </p>
                            <p class="text-lg font-bold text-[#112119]">
                                {{ __("Talk to the team") }}
                            </p>
                        </div>
                    </div>
                    <p class="mt-5 text-base leading-7 text-[#527060]">
                        {{ __("If your flow is more complex than a quick FAQ can solve, contact us and we will help you map the right setup.") }}
                    </p>
                    <a href="{{ url('contact') }}" class="mt-6 inline-flex items-center gap-2 rounded-full bg-[#1f7a45] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#176338]">
                        <span>{{ __("Contact sales") }}</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <div class="space-y-4" x-data="{ open: {{ $firstFaqId ?? 'null' }} }">
                @foreach($faqs as $index => $faq)
                    <div class="overflow-hidden rounded-[2rem] border border-[#cfe9d8] bg-white shadow-[0_18px_60px_rgba(44,122,68,0.06)]">
                        <button
                            type="button"
                            class="flex w-full items-start justify-between gap-4 px-6 py-6 text-left md:px-8"
                            x-on:click="open === {{ $faq->id }} ? open = null : open = {{ $faq->id }}"
                        >
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#6ea882]">
                                    {{ sprintf('FAQ %02d', $index + 1) }}
                                </p>
                                <h3 class="mt-3 text-xl font-bold leading-8 text-[#112119]">
                                    {{ $faq->title }}
                                </h3>
                            </div>
                            <span
                                class="mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-[#b6e2c4] bg-[#effaf2] text-[#1f7a45] transition"
                                :class="{ 'bg-[#1f7a45] text-white border-[#1f7a45]': open === {{ $faq->id }} }"
                            >
                                <i class="fas" :class="open === {{ $faq->id }} ? 'fa-minus' : 'fa-plus'"></i>
                            </span>
                        </button>
                        <div
                            x-ref="faq_{{ $faq->id }}"
                            class="h-0 overflow-hidden transition-all duration-500"
                            :style="open === {{ $faq->id }} ? 'height:' + $refs['faq_{{ $faq->id }}'].scrollHeight + 'px' : ''"
                        >
                            <div class="border-t border-[#e4f3e8] px-6 pb-7 pt-5 md:px-8">
                                <div class="max-w-3xl text-base leading-8 text-[#527060]">
                                    {!! $faq->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
