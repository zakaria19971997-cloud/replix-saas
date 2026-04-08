@php
    $modules = [
        [
            'title' => 'AI Smart Reply',
            'icon' => 'fa-sparkles',
            'desc' => 'Generate fast, context-aware WhatsApp replies so agents can handle conversations with more consistency and less manual effort.',
        ],
        [
            'title' => 'Auto Reply',
            'icon' => 'fa-reply-clock',
            'desc' => 'Trigger automatic responses for inbound messages, off-hours support, lead capture, and common request handling.',
        ],
        [
            'title' => 'Bulk Campaigns',
            'icon' => 'fa-bullhorn',
            'desc' => 'Launch high-volume WhatsApp campaigns to segmented audiences while keeping timing, delivery, and response flow under control.',
        ],
        [
            'title' => 'Chatbot',
            'icon' => 'fa-robot',
            'desc' => 'Build conversational flows that qualify leads, answer common questions, and move contacts into the next step automatically.',
        ],
        [
            'title' => 'Contacts',
            'icon' => 'fa-address-book',
            'desc' => 'Manage contact lists, segments, and campaign targets from one structured database built for WhatsApp operations.',
        ],
        [
            'title' => 'Export Participants',
            'icon' => 'fa-file-export',
            'desc' => 'Extract participants from WhatsApp groups for outreach, qualification, migration, and audience building workflows.',
        ],
        [
            'title' => 'Profile Info',
            'icon' => 'fa-id-card',
            'desc' => 'Review WhatsApp profile information quickly to enrich lead context and improve sales or support handoff quality.',
        ],
        [
            'title' => 'Reports',
            'icon' => 'fa-chart-column',
            'desc' => 'Track campaign output, reply activity, and workflow performance with reporting built for real operational decisions.',
        ],
    ];
@endphp

<section class="relative overflow-hidden" style="background: radial-gradient(circle at top left, rgba(134, 239, 172, 0.30), transparent 20%), linear-gradient(180deg, #effaf2 0%, #ffffff 100%);">
    <div class="container px-4 mx-auto pt-28 pb-24 md:pt-32 md:pb-28">
        <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold mb-7" style="background: #dcfce7; color: #166534;">
                    <i class="fa-brands fa-whatsapp mr-2"></i>
                    WhatsApp operations, campaigns, and automation
                </span>

                <h1 class="mb-6 text-6xl md:text-8xl xl:text-10xl font-bold font-heading tracking-px-n leading-none">
                    One WhatsApp workspace for campaigns, replies, bots, contacts, and reports.
                </h1>

                <p class="mb-10 text-lg text-gray-600 font-medium leading-relaxed md:max-w-2xl">
                    Built around the modules teams actually use every day: bulk outreach, smart replies, auto replies, chatbots, contacts, participant export, profile lookups, and reporting.
                </p>

                <div class="flex flex-wrap -m-2.5 mb-12">
                    <div class="w-full md:w-auto p-2.5">
                        <a href="{{ url('auth/signup') }}" class="block py-4 px-6 w-full text-white font-semibold rounded-xl transition ease-in-out duration-200" style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); box-shadow: 0 18px 38px rgba(34, 197, 94, 0.2);">
                            Create your workspace
                        </a>
                    </div>
                    <div class="w-full md:w-auto p-2.5">
                        <a href="{{ url('') }}#modules" class="block py-4 px-9 w-full font-semibold border rounded-xl transition ease-in-out duration-200" style="border-color: #bbf7d0; color: #166534; background: #ffffff;">
                            Explore modules
                        </a>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4 md:max-w-2xl">
                    <div class="rounded-3xl border px-5 py-5" style="border-color: #dcfce7; background: #ffffff;">
                        <div class="text-3xl font-bold mb-2" style="color: #15803d;">8</div>
                        <div class="text-sm text-gray-500">Operational modules included</div>
                    </div>
                    <div class="rounded-3xl border px-5 py-5" style="border-color: #dcfce7; background: #ffffff;">
                        <div class="text-3xl font-bold mb-2" style="color: #15803d;">1</div>
                        <div class="text-sm text-gray-500">Unified WhatsApp workflow</div>
                    </div>
                    <div class="rounded-3xl border px-5 py-5" style="border-color: #dcfce7; background: #ffffff;">
                        <div class="text-3xl font-bold mb-2" style="color: #15803d;">24/7</div>
                        <div class="text-sm text-gray-500">Automation and reply coverage</div>
                    </div>
                </div>
            </div>

            <div>
                <div class="rounded-[2rem] p-6 border" style="background: #ffffff; border-color: #dcfce7; box-shadow: 0 35px 70px rgba(6, 78, 59, 0.12);">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Live module stack</div>
                            <div class="text-xl font-bold text-gray-900">Waziper Workspace</div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background: #dcfce7; color: #166534;">
                            Product overview
                        </span>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-[1.5rem] p-5" style="background: #f7fcf8;">
                            <div class="text-sm text-gray-500 mb-2">Campaign control</div>
                            <div class="text-lg font-bold text-gray-900 mb-2">Bulk Campaigns</div>
                            <div class="text-sm text-gray-600">Segment contacts, send at scale, and track response flow.</div>
                        </div>
                        <div class="rounded-[1.5rem] p-5" style="background: #f7fcf8;">
                            <div class="text-sm text-gray-500 mb-2">Inbound handling</div>
                            <div class="text-lg font-bold text-gray-900 mb-2">AI + Auto Reply</div>
                            <div class="text-sm text-gray-600">Combine automation with faster human response quality.</div>
                        </div>
                        <div class="rounded-[1.5rem] p-5" style="background: #f7fcf8;">
                            <div class="text-sm text-gray-500 mb-2">Lead flow</div>
                            <div class="text-lg font-bold text-gray-900 mb-2">Chatbot + Contacts</div>
                            <div class="text-sm text-gray-600">Capture, qualify, and manage audience data in one place.</div>
                        </div>
                        <div class="rounded-[1.5rem] p-5" style="background: #f7fcf8;">
                            <div class="text-sm text-gray-500 mb-2">Visibility</div>
                            <div class="text-lg font-bold text-gray-900 mb-2">Reports + Export</div>
                            <div class="text-sm text-gray-600">Export participants and measure what the team actually ships.</div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-[1.5rem] px-5 py-4 text-white" style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);">
                        <div class="text-xs uppercase tracking-wide text-green-50 mb-2">Why it matters</div>
                        <div class="font-medium">This is not a single-feature WhatsApp tool. It is a workflow stack for outreach, inbound handling, contact operations, and reporting.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-white" id="modules">
    <div class="container px-4 mx-auto">
        <div class="md:max-w-4xl mx-auto text-center mb-16">
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold mb-6" style="background: #ecfdf3; color: #166534;">
                Product modules
            </span>
            <h2 class="mb-6 text-6xl md:text-7xl font-bold font-heading tracking-px-n leading-tight">
                Built from the WhatsApp features teams actually need
            </h2>
            <p class="text-lg text-gray-500 font-medium leading-relaxed">
                Every module supports a real operational job, from outreach and automation to contact management and reporting.
            </p>
        </div>

        <div class="flex flex-wrap -m-4">
            @foreach($modules as $module)
                <div class="w-full md:w-1/2 lg:w-1/4 p-4">
                    <div class="h-full rounded-4xl border p-8" style="border-color: #dcfce7; background: linear-gradient(180deg, #ffffff 0%, #f7fcf8 100%);">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl mb-6" style="background: #dcfce7; color: #15803d;">
                            <i class="fa-solid {{ $module['icon'] }} text-2xl"></i>
                        </div>
                        <h3 class="mb-4 text-2xl font-bold">{{ $module['title'] }}</h3>
                        <p class="text-gray-600 font-medium leading-relaxed">
                            {{ $module['desc'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-24 overflow-hidden" style="background: linear-gradient(180deg, #f5fbf7 0%, #ffffff 100%);">
    <div class="container px-4 mx-auto">
        <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
            <div>
                <div class="rounded-4xl border p-8" style="border-color: #dcfce7; background: #ffffff; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.06);">
                    <div class="space-y-5">
                        <div class="rounded-3xl p-6" style="background: #f7fcf8;">
                            <div class="text-sm text-gray-500 mb-2">Outbound flow</div>
                            <div class="text-2xl font-bold text-gray-900 mb-2">Contacts -> Bulk Campaigns</div>
                            <div class="text-sm text-gray-600">Segment contacts, launch campaigns, and control message flow without leaving the workspace.</div>
                        </div>
                        <div class="rounded-3xl p-6" style="background: #f7fcf8;">
                            <div class="text-sm text-gray-500 mb-2">Inbound flow</div>
                            <div class="text-2xl font-bold text-gray-900 mb-2">Chatbot -> Auto Reply -> AI Smart Reply</div>
                            <div class="text-sm text-gray-600">Handle common messages automatically, then assist agents with higher-quality responses when context matters.</div>
                        </div>
                        <div class="rounded-3xl p-6" style="background: #f7fcf8;">
                            <div class="text-sm text-gray-500 mb-2">Operations flow</div>
                            <div class="text-2xl font-bold text-gray-900 mb-2">Profile Info -> Reports -> Export Participants</div>
                            <div class="text-sm text-gray-600">Inspect lead context, review performance, and export WhatsApp group participants for new growth loops.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold mb-6" style="background: #dcfce7; color: #166534;">
                    How the modules work together
                </span>
                <h2 class="mb-6 text-6xl md:text-7xl font-bold font-heading tracking-px-n leading-tight">
                    Designed as one WhatsApp system, not a pile of disconnected tools
                </h2>
                <p class="mb-8 text-lg text-gray-500 font-medium leading-relaxed">
                    The value comes from how these modules connect: contacts feed campaigns, bots reduce manual load, AI supports agent replies, and reports show what is actually working.
                </p>

                <div class="space-y-4">
                    <div class="flex items-start rounded-3xl border px-5 py-5" style="border-color: #dcfce7; background: #ffffff;">
                        <i class="fa-solid fa-check mr-4 mt-1" style="color: #16a34a;"></i>
                        <span class="text-gray-700 font-medium">Run outbound WhatsApp campaigns with contact segmentation and participant export.</span>
                    </div>
                    <div class="flex items-start rounded-3xl border px-5 py-5" style="border-color: #dcfce7; background: #ffffff;">
                        <i class="fa-solid fa-check mr-4 mt-1" style="color: #16a34a;"></i>
                        <span class="text-gray-700 font-medium">Reduce response time using auto replies, chatbots, and AI-assisted answer generation.</span>
                    </div>
                    <div class="flex items-start rounded-3xl border px-5 py-5" style="border-color: #dcfce7; background: #ffffff;">
                        <i class="fa-solid fa-check mr-4 mt-1" style="color: #16a34a;"></i>
                        <span class="text-gray-700 font-medium">See performance clearly with reporting that connects activity, campaigns, and reply outcomes.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-white">
    <div class="container px-4 mx-auto">
        <div class="md:max-w-4xl mx-auto text-center mb-16">
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold mb-6" style="background: #ecfdf3; color: #166534;">
                Core use cases
            </span>
            <h2 class="mb-6 text-6xl md:text-7xl font-bold font-heading tracking-px-n leading-tight">
                Use the platform for growth, support, and daily WhatsApp operations
            </h2>
        </div>

        <div class="flex flex-wrap -m-4">
            <div class="w-full md:w-1/3 p-4">
                <div class="h-full rounded-4xl border p-8" style="border-color: #dcfce7; background: #ffffff;">
                    <h3 class="mb-4 text-2xl font-bold">Outbound Marketing</h3>
                    <p class="text-gray-600 font-medium leading-relaxed">
                        Build segmented contact lists, run bulk campaigns, track responses, and improve conversion using report data.
                    </p>
                </div>
            </div>
            <div class="w-full md:w-1/3 p-4">
                <div class="h-full rounded-4xl border p-8" style="border-color: #dcfce7; background: #ffffff;">
                    <h3 class="mb-4 text-2xl font-bold">Inbound Automation</h3>
                    <p class="text-gray-600 font-medium leading-relaxed">
                        Use chatbot and auto reply logic to handle repetitive inbound conversations without keeping agents on every message.
                    </p>
                </div>
            </div>
            <div class="w-full md:w-1/3 p-4">
                <div class="h-full rounded-4xl border p-8" style="border-color: #dcfce7; background: #ffffff;">
                    <h3 class="mb-4 text-2xl font-bold">Performance Visibility</h3>
                    <p class="text-gray-600 font-medium leading-relaxed">
                        Use profile lookups, reports, and participant export to move from scattered chats to measurable operational workflows.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-white">
    <div class="container px-4 mx-auto">
        <div class="rounded-4xl px-8 py-12 md:px-14 md:py-16 text-center overflow-hidden" style="background: linear-gradient(135deg, #052e16 0%, #065f46 60%, #064e3b 100%);">
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold mb-6" style="background: rgba(255,255,255,0.12); color: #dcfce7;">
                Ready to use these modules together?
            </span>
            <h2 class="mb-6 text-6xl md:text-7xl font-bold font-heading tracking-px-n leading-tight text-white">
                Build your WhatsApp workflow on one system
            </h2>
            <p class="mb-10 text-lg text-green-50 font-medium leading-relaxed md:max-w-3xl mx-auto">
                Launch campaigns, automate replies, manage contacts, and track performance from one WhatsApp workspace.
            </p>
            <div class="flex flex-wrap justify-center -m-2.5">
                <div class="w-full md:w-auto p-2.5">
                    <a href="{{ url('auth/signup') }}" class="block py-4 px-6 w-full text-white font-semibold rounded-xl transition ease-in-out duration-200" style="background: #22c55e;">
                        Create your workspace
                    </a>
                </div>
                <div class="w-full md:w-auto p-2.5">
                    <a href="{{ url('pricing') }}" class="block py-4 px-9 w-full font-semibold rounded-xl transition ease-in-out duration-200" style="background: rgba(255,255,255,0.12); color: #ffffff; border: 1px solid rgba(255,255,255,0.16);">
                        View pricing
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@include("partials.pricing")
@include("partials.faqs")
@include("partials.home-blog")
