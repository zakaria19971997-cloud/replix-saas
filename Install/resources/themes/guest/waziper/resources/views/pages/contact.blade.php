<section class="bg-[#f3fbf6] py-20 md:py-28">
    <div class="container mx-auto px-4">
        <div class="mx-auto max-w-3xl text-center">
            <span class="inline-flex items-center rounded-full border border-[#9ad9af] bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-[#1f7a45]">
                {{ __("Contact us") }}
            </span>
            <h1 class="mt-6 text-4xl font-bold leading-tight text-[#112119] md:text-6xl">
                {{ __("Talk to the team behind your WhatsApp workflow") }}
            </h1>
            <p class="mt-5 text-lg leading-8 text-[#527060]">
                {{ __("Reach out for onboarding help, pricing questions, infrastructure guidance, or support with a production WhatsApp setup.") }}
            </p>
        </div>

        <div class="mt-14 grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-[2rem] border border-[#cfe9d8] bg-white p-8 shadow-[0_24px_80px_rgba(44,122,68,0.08)] md:p-10">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="rounded-[1.5rem] border border-[#e1f1e6] bg-[#f9fefb] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#6ea882]">{{ __("Website") }}</p>
                        <a href="{{ get_option('contact_company_website', '#') }}" target="_blank" class="mt-4 block text-lg font-bold leading-8 text-[#112119] transition hover:text-[#1f7a45]">
                            {{ get_option('contact_company_website', 'https://yourcompany.com') }}
                        </a>
                    </div>
                    <div class="rounded-[1.5rem] border border-[#e1f1e6] bg-[#f9fefb] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#6ea882]">{{ __("Email") }}</p>
                        <p class="mt-4 text-lg font-bold leading-8 text-[#112119]">
                            {{ get_option('contact_email', 'support@yourcompany.com') }}
                        </p>
                    </div>
                    <div class="rounded-[1.5rem] border border-[#e1f1e6] bg-[#f9fefb] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#6ea882]">{{ __("Phone") }}</p>
                        <p class="mt-4 text-lg font-bold leading-8 text-[#112119]">
                            {{ get_option('contact_phone_number', '+1 234 567 890') }}
                        </p>
                    </div>
                    <div class="rounded-[1.5rem] border border-[#e1f1e6] bg-[#f9fefb] p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#6ea882]">{{ __("Working hours") }}</p>
                        <p class="mt-4 text-lg font-bold leading-8 text-[#112119]">
                            {{ get_option('contact_working_hours', 'Mon - Fri: 09:00 AM - 06:00 PM') }}
                        </p>
                    </div>
                </div>

                <div class="mt-5 rounded-[1.75rem] border border-[#e1f1e6] bg-[#f9fefb] p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#6ea882]">{{ __("Address") }}</p>
                    <p class="mt-4 text-lg font-bold leading-8 text-[#112119]">
                        {{ get_option('contact_location', '123 Main Street, City, Country') }}
                    </p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#1f7a45] via-[#176338] to-[#0f4d2a] p-8 text-white shadow-[0_30px_100px_rgba(18,92,48,0.28)] md:p-10">
                <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-16 -left-16 h-52 w-52 rounded-full bg-[#9de2b4]/20 blur-2xl"></div>
                <div class="relative">
                    <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-white/85">
                        {{ __("Why contact us") }}
                    </span>
                    <h2 class="mt-6 text-3xl font-bold leading-tight md:text-4xl">
                        {{ __("Get practical guidance, not generic support replies") }}
                    </h2>
                    <p class="mt-5 text-base leading-8 text-white/80">
                        {{ __("We help teams deploy WhatsApp marketing and service workflows with the right structure from day one.") }}
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="rounded-[1.5rem] border border-white/15 bg-white/10 p-5 backdrop-blur-sm">
                            <h3 class="text-lg font-semibold">{{ __("Onboarding and setup") }}</h3>
                            <p class="mt-2 text-sm leading-7 text-white/80">{{ __("Need help configuring inboxes, agents, automations, or WhatsApp numbers.") }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/15 bg-white/10 p-5 backdrop-blur-sm">
                            <h3 class="text-lg font-semibold">{{ __("Pricing and scale") }}</h3>
                            <p class="mt-2 text-sm leading-7 text-white/80">{{ __("We can help map the right plan for your sending volume and team structure.") }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/15 bg-white/10 p-5 backdrop-blur-sm">
                            <h3 class="text-lg font-semibold">{{ __("Operational workflows") }}</h3>
                            <p class="mt-2 text-sm leading-7 text-white/80">{{ __("Talk through lead qualification, nurture flows, support handoff, and campaign design.") }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
