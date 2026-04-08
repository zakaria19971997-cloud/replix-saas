@php
    $blogs = Home::getRecentBlogs(null, 3);
@endphp

<section class="bg-white py-20 md:py-28">
    <div class="container mx-auto px-4">
        <div class="mb-12 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex items-center rounded-full border border-[#9ad9af] bg-[#effaf2] px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-[#1f7a45]">
                    {{ __("Blog") }}
                </span>
                <h2 class="mt-6 text-4xl font-bold leading-tight text-[#112119] md:text-5xl">
                    {{ __("Articles for teams building serious WhatsApp operations") }}
                </h2>
                <p class="mt-4 max-w-2xl text-lg leading-8 text-[#527060]">
                    {{ __("Playbooks, campaign ideas, support workflows, and practical guidance for turning WhatsApp into a real revenue channel.") }}
                </p>
            </div>
            <a href="{{ url('blogs') }}" class="inline-flex items-center gap-2 self-start rounded-full border border-[#b7dfc2] bg-white px-6 py-3 text-sm font-semibold text-[#1f7a45] transition hover:border-[#1f7a45] hover:bg-[#effaf2]">
                <span>{{ __("Browse all articles") }}</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            @foreach($blogs as $blog)
                <article class="flex h-full flex-col overflow-hidden rounded-[2rem] border border-[#d4eadb] bg-white shadow-[0_20px_70px_rgba(44,122,68,0.08)] transition hover:-translate-y-1 hover:shadow-[0_28px_90px_rgba(44,122,68,0.12)]">
                    <a href="{{ url('blogs/'.$blog->slug) }}" class="block overflow-hidden">
                        <img
                            class="h-64 w-full object-cover transition duration-700 hover:scale-105"
                            src="{{ !empty($blog->thumbnail) ? Media::url($blog->thumbnail) : theme_public_asset('images/blog/blog-wide.png') }}"
                            alt="{{ $blog->title }}"
                        >
                    </a>
                    <div class="flex flex-1 flex-col p-7">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex rounded-full bg-[#effaf2] px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-[#1f7a45]">
                                {{ $blog->category->name ?? __("Blog") }}
                            </span>
                            <span class="text-sm text-[#6c8577]">
                                {{ $blog->created ? \Carbon\Carbon::createFromTimestamp($blog->created)->format('d M Y') : '' }}
                            </span>
                        </div>

                        <h3 class="mt-5 text-2xl font-bold leading-9 text-[#112119]">
                            <a href="{{ url('blogs/'.$blog->slug) }}" class="transition hover:text-[#1f7a45]">
                                {{ $blog->title }}
                            </a>
                        </h3>

                        <p class="mt-4 flex-1 text-base leading-8 text-[#527060]">
                            {{ Str::limit(strip_tags($blog->desc), 150) }}
                        </p>

                        <a href="{{ url('blogs/'.$blog->slug) }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-[#1f7a45] transition hover:text-[#176338]">
                            <span>{{ __("Read article") }}</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
