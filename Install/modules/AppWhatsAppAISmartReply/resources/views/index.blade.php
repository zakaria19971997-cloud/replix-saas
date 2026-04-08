@extends('layouts.app')

@section('content')
    <div class="compose position-absolute l-0 t-0 wp-100 hp-100 bg-white zIndex-9">
        <div class="d-flex hp-100">
            <form class="compose-editor d-flex flex-column flex-fill hp-100 border-start border-end actionForm bg-white pb-4" action="{{ route('app.whatsappaismartreply.save') }}" method="POST">
                <div class="container-fluid px-4 px-lg-5 py-4 d-flex flex-column hp-100 overflow-auto">
                    <div class="d-flex flex-column flex-fill gap-24 min-h-100">
                        <div class="d-flex flex-column flex-xl-row align-items-xl-end justify-content-between gap-20">
                            <div class="d-flex flex-column gap-10">
                                <div class="d-inline-flex align-items-center gap-8 px-3 py-2 bg-success-100 text-success rounded-pill fs-12 fw-6">
                                    <i class="fa-brands fa-whatsapp"></i>
                                    <span>{{ __('WhatsApp Unofficial') }}</span>
                                </div>
                                <div>
                                    <h1 class="fs-28 lh-sm fw-6 text-gray-900 mb-2">{{ __('WhatsApp AI Smart Reply') }}</h1>
                                    <div class="fs-15 text-gray-600 max-w-700">{{ __('Build an AI-guided WhatsApp reply flow for your profiles with prompt-based responses, delay rules, fallback protection, and a cleaner operator workflow.') }}</div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-12">
                                <div class="border rounded-3 px-3 py-2 bg-light">
                                    <div class="fs-11 text-uppercase text-gray-500 fw-6">{{ __('Profiles') }}</div>
                                    <div class="fs-18 fw-6 text-gray-900">{{ $accounts->count() }}</div>
                                </div>
                                <div class="border rounded-3 px-3 py-2 bg-light">
                                    <div class="fs-11 text-uppercase text-gray-500 fw-6">{{ __('AI') }}</div>
                                    <div class="fs-18 fw-6 text-gray-900">{{ get_option('ai_status', 1) ? __('Ready') : __('Off') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 align-items-stretch flex-fill">
                            <div class="col-12 col-xl-4 d-flex">
                                <div class="card shadow-none border-gray-300 overflow-hidden w-100 hp-100">
                                    <div class="card-body p-4 d-flex flex-column gap-20 hp-100">
                                        <div>
                                            <div class="fs-20 fw-6 text-gray-900 mb-2">{{ __('Choose your reply target') }}</div>
                                            <div class="text-gray-600 fs-14">{{ __('Apply one AI smart reply rule to a connected WhatsApp profile and define how the assistant should answer each incoming message.') }}</div>
                                        </div>

                                        <div>
                                            <label for="wa_ai_smart_reply_account" class="form-label fw-6">{{ __('WhatsApp account') }}</label>
                                            <select id="wa_ai_smart_reply_account" class="form-select form-select-lg">
                                                <option value="">{{ __('Select WhatsApp account') }}</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id_secure }}">{{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="d-flex flex-column gap-12 mt-auto">
                                            <div class="d-flex gap-12 align-items-start p-3 rounded-3 bg-light">
                                                <div class="w-40 h-40 rounded-circle bg-success-100 text-success d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="fa-light fa-sparkles"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-6 text-gray-900">{{ __('AI-assisted writing') }}</div>
                                                    <div class="fs-13 text-gray-600">{{ __('Use the built-in AI workflow to turn each incoming message into one polished WhatsApp-ready reply.') }}</div>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-12 align-items-start p-3 rounded-3 bg-light">
                                                <div class="w-40 h-40 rounded-circle bg-primary-100 text-primary d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="fa-light fa-language"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-6 text-gray-900">{{ __('Context-aware responses') }}</div>
                                                    <div class="fs-13 text-gray-600">{{ __('The AI uses your prompt, the incoming message, and the current profile rules to keep replies on-brand and concise.') }}</div>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-12 align-items-start p-3 rounded-3 bg-light">
                                                <div class="w-40 h-40 rounded-circle bg-warning-100 text-warning d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="fa-light fa-shield-check"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-6 text-gray-900">{{ __('Safer automation') }}</div>
                                                    <div class="fs-13 text-gray-600">{{ __('Throttle replies with delays, fallback text, and exclusion lists so the AI avoids noisy or protected conversations.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-8 d-flex">
                                <div class="wa-ai-smart-reply-result w-100 hp-100">
                                    <div class="card shadow-none border-gray-300 overflow-hidden hp-100">
                                        <div class="card-body p-5 text-center d-flex flex-column align-items-center justify-content-center hp-100 min-h-400">
                                            <div class="d-inline-flex align-items-center justify-content-center w-80 h-80 rounded-circle bg-light mb-4">
                                                <i class="fa-light fa-sparkles text-gray-500 fs-28"></i>
                                            </div>
                                            <div class="fs-22 fw-6 text-gray-900 mb-2">{{ __('Ready to configure your AI smart reply') }}</div>
                                            <div class="fs-14 text-gray-600 max-w-520 mx-auto">{{ __('Pick one WhatsApp account on the left to open the AI instruction form, fine-tune fallback behavior, and control how replies are generated.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
(function () {
    var select = document.getElementById('wa_ai_smart_reply_account');
    var resultWrap = document.querySelector('.wa-ai-smart-reply-result');

    if (!select || !resultWrap) return;

    var emptyState = '<div class="card shadow-none border-gray-300 overflow-hidden hp-100"><div class="card-body p-5 text-center d-flex flex-column align-items-center justify-content-center hp-100 min-h-400"><div class="d-inline-flex align-items-center justify-content-center w-80 h-80 rounded-circle bg-light mb-4"><i class="fa-light fa-sparkles text-gray-500 fs-28"></i></div><div class="fs-22 fw-6 text-gray-900 mb-2">{{ __("Ready to configure your AI smart reply") }}</div><div class="fs-14 text-gray-600 max-w-520 mx-auto">{{ __("Pick one WhatsApp account on the left to open the AI instruction form, fine-tune fallback behavior, and control how replies are generated.") }}</div></div></div>';

    var bootDynamicContent = function (scope) {
        if (window.Main && typeof Main.Tags === 'function') {
            Main.Tags();
        }
    };

    var injectHtml = function (target, html) {
        target.innerHTML = html;
        var scripts = target.querySelectorAll('script');
        scripts.forEach(function (oldScript) {
            var newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(function (attr) {
                newScript.setAttribute(attr.name, attr.value);
            });
            newScript.text = oldScript.text || oldScript.textContent || oldScript.innerHTML || '';
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
        bootDynamicContent(target);
    };

    var loadInfo = function () {
        var account = select.value;

        if (!account) {
            injectHtml(resultWrap, emptyState);
            return;
        }

        $.ajax({
            url: '{{ route('app.whatsappaismartreply.info') }}',
            type: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                account: account
            },
            beforeSend: function () {
                injectHtml(resultWrap, '<div class="card shadow-none border-gray-300 overflow-hidden hp-100"><div class="card-body p-5 text-center text-gray-600 d-flex align-items-center justify-content-center hp-100 min-h-400"><div><div class="spinner-border spinner-border-sm me-2"></div>{{ __("Loading AI smart reply settings...") }}</div></div></div>');
            },
            success: function (result) {
                if (result.status == 1 && result.data) {
                    injectHtml(resultWrap, result.data);
                    return;
                }

                injectHtml(resultWrap, '<div class="alert alert-danger">' + (result.message || '{{ __("Could not load AI smart reply settings.") }}') + '</div>');
            },
            error: function () {
                injectHtml(resultWrap, '<div class="alert alert-danger">{{ __("Could not load AI smart reply settings.") }}</div>');
            }
        });
    };

    $(document).off('change.waAISmartReplyAccount').on('change.waAISmartReplyAccount', '#wa_ai_smart_reply_account', loadInfo);

    if (select.options.length === 2) {
        select.selectedIndex = 1;
        loadInfo();
    }
})();
</script>
@endsection