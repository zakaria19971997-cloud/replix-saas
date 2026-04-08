@extends('layouts.app')

@section('content')
    <div class="compose position-absolute l-0 t-0 wp-100 hp-100 bg-white zIndex-9">
        <div class="d-flex hp-100">
            @can("appfiles")
            <div class="compose-media d-flex flex-column flex-fill max-w-400 min-w-300 bg-white d-none">
                @include('appfiles::block_files')
            </div>
            @endcan

            <form class="compose-editor d-flex flex-column flex-fill hp-100 border-start border-end actionForm bg-white pb-4" action="{{ module_url('save') }}" method="POST">
                <div class="container-fluid px-4 px-lg-5 py-4 d-flex flex-column hp-100 overflow-auto">
                    <div class="d-flex flex-column flex-fill gap-24 min-h-100">
                        <div class="d-flex flex-column flex-xl-row align-items-xl-end justify-content-between gap-20">
                            <div class="d-flex flex-column gap-10">
                                <div class="d-inline-flex align-items-center gap-8 px-3 py-2 bg-success-100 text-success rounded-pill fs-12 fw-6">
                                    <i class="fa-brands fa-whatsapp"></i>
                                    <span>{{ __('WhatsApp Unofficial') }}</span>
                                </div>
                                <div>
                                    <h1 class="fs-28 lh-sm fw-6 text-gray-900 mb-2">{{ __('WhatsApp Auto Reply') }}</h1>
                                    <div class="fs-15 text-gray-600 max-w-700">
                                        {{ __('Build an automatic reply flow for your WhatsApp profiles with media support, AI-assisted caption writing, and a cleaner operator workflow.') }}
                                    </div>
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
                                            <div class="text-gray-600 fs-14">
                                                {{ __('Apply one autoresponder to every WhatsApp profile or tune a separate rule for a specific number.') }}
                                            </div>
                                        </div>

                                        <div>
                                            <label for="wa_auto_reply_account" class="form-label fw-6">{{ __('WhatsApp account') }}</label>
                                            <select id="wa_auto_reply_account" class="form-select form-select-lg">
                                                <option value="">{{ __('Select WhatsApp account') }}</option>
                                                <option value="all">{{ __('Apply for all accounts') }}</option>
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
                                                    <div class="fs-13 text-gray-600">{{ __('Use the built-in AI button to turn a simple idea into a polished auto reply.') }}</div>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-12 align-items-start p-3 rounded-3 bg-light">
                                                <div class="w-40 h-40 rounded-circle bg-primary-100 text-primary d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="fa-light fa-photo-film"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-6 text-gray-900">{{ __('Media-ready responses') }}</div>
                                                    <div class="fs-13 text-gray-600">{{ __('Attach images or videos from the existing file manager without leaving this screen.') }}</div>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-12 align-items-start p-3 rounded-3 bg-light">
                                                <div class="w-40 h-40 rounded-circle bg-warning-100 text-warning d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="fa-light fa-shield-check"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-6 text-gray-900">{{ __('Safer automation') }}</div>
                                                    <div class="fs-13 text-gray-600">{{ __('Throttle replies with delays and exclusion lists to avoid noisy conversations.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-8 d-flex">
                                <div class="wa-auto-reply-result w-100 hp-100">
                                    <div class="card shadow-none border-gray-300 overflow-hidden hp-100">
                                        <div class="card-body p-5 text-center d-flex flex-column align-items-center justify-content-center hp-100 min-h-400">
                                            <div class="d-inline-flex align-items-center justify-content-center w-80 h-80 rounded-circle bg-light mb-4">
                                                <i class="fa-light fa-comments text-gray-500 fs-28"></i>
                                            </div>
                                            <div class="fs-22 fw-6 text-gray-900 mb-2">{{ __('Ready to configure your first auto reply') }}</div>
                                            <div class="fs-14 text-gray-600 max-w-520 mx-auto">
                                                {{ __('Pick one WhatsApp account on the left to open the composer, connect media, and generate better replies with the AI tools already available in your project.') }}
                                            </div>
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
        var select = document.getElementById('wa_auto_reply_account');
        var resultWrap = document.querySelector('.wa-auto-reply-result');

        if (!select || !resultWrap) return;

        var emptyState = '<div class="card shadow-none border-gray-300 overflow-hidden hp-100"><div class="card-body p-5 text-center d-flex flex-column align-items-center justify-content-center hp-100 min-h-400"><div class="d-inline-flex align-items-center justify-content-center w-80 h-80 rounded-circle bg-light mb-4"><i class="fa-light fa-comments text-gray-500 fs-28"></i></div><div class="fs-22 fw-6 text-gray-900 mb-2">{{ __("Ready to configure your first auto reply") }}</div><div class="fs-14 text-gray-600 max-w-520 mx-auto">{{ __("Pick one WhatsApp account on the left to open the composer, connect media, and generate better replies with the AI tools already available in your project.") }}</div></div></div>';

        var isDesktopViewport = function () {
            return $(window).width() >= 992;
        };

        var toggleComposePanels = function (showMedia) {
            var mediaPanel = $('.compose-media');
            var editorPanel = $('.compose-editor');

            if (isDesktopViewport()) {
                mediaPanel.removeClass('d-none');
                editorPanel.removeClass('d-none');
                return;
            }

            if (showMedia) {
                mediaPanel.removeClass('d-none');
                editorPanel.addClass('d-none');
            } else {
                mediaPanel.addClass('d-none');
                editorPanel.removeClass('d-none');
            }
        };

        var syncComposeMediaResponsive = function () {
            if (isDesktopViewport()) {
                toggleComposePanels(true);
            }
        };

        var bindComposeActions = function () {
            $(document).off('click.waCloseCompose').on('click.waCloseCompose', '.closeCompose', function (e) {
                e.preventDefault();
                $('.compose-preview').removeClass('active');

                if (!isDesktopViewport()) {
                    toggleComposePanels(false);
                }

                return false;
            });

            $(document).off('click.waShowCompose').on('click.waShowCompose', '.showCompose', function (e) {
                e.preventDefault();
                $('.compose-preview').removeClass('active');

                if (!isDesktopViewport()) {
                    toggleComposePanels(false);
                }

                return false;
            });

            $(document).off('click.waShowMedia').on('click.waShowMedia', '.showMedia', function (e) {
                e.preventDefault();

                if (!isDesktopViewport()) {
                    toggleComposePanels(true);
                }

                return false;
            });

            $(document).off('click.waShowPreview').on('click.waShowPreview', '.showPreview', function (e) {
                e.preventDefault();
                $('.compose-preview').addClass('active');
                return false;
            });

            $(window).off('resize.waComposeMedia').on('resize.waComposeMedia', function () {
                syncComposeMediaResponsive();
            });
        };
        var bootDynamicContent = function (scope) {
            if (window.Main && typeof Main.Emoji === 'function') {
                Main.Emoji();
            }

            if (window.Files && typeof Files.init === 'function') {
                Files.init(false);
            }

            if (window.bootstrap) {
                var tooltipTriggerList = [].slice.call((scope || document).querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function (el) {
                    bootstrap.Tooltip.getOrCreateInstance(el);
                });

                var popoverTriggerList = [].slice.call((scope || document).querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.forEach(function (el) {
                    bootstrap.Popover.getOrCreateInstance(el);
                });
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

        var resetMediaSelection = function () {
            $('.compose-media input[name="id[]"]').prop('checked', false);
            $('.compose-media .file-item').removeClass('selected');
            $('.compose-media .file-item .remove').remove();

            if (window.Main && typeof Main.Uncheckbox === 'function') {
                Main.Uncheckbox('.compose-media');
            }
        };

        var syncSelectedMediaFromComposer = function () {
            var selectedFiles = [];

            $('#medias input[name="medias[]"]').each(function () {
                var value = ($(this).val() || '').trim();
                if (value) {
                    selectedFiles.push(value);
                }
            });

            if (!selectedFiles.length) {
                return;
            }

            $('.compose-media input[name="id[]"]').each(function () {
                var input = $(this);
                var value = (input.val() || '').trim();
                var fileItem = input.closest('.file-item');

                if (selectedFiles.indexOf(value) !== -1) {
                    input.prop('checked', true);
                    fileItem.addClass('selected');
                }
            });
        };

        var loadInfo = function () {
            var account = select.value;
            resetMediaSelection();

            if (!account) {
                injectHtml(resultWrap, emptyState);
                return;
            }

            $.ajax({
                url: '{{ module_url("info") }}',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                    account: account
                },
                beforeSend: function () {
                    injectHtml(resultWrap, '<div class="card shadow-none border-gray-300 overflow-hidden hp-100"><div class="card-body p-5 text-center text-gray-600 d-flex align-items-center justify-content-center hp-100 min-h-400"><div><div class="spinner-border spinner-border-sm me-2"></div>{{ __("Loading auto reply settings...") }}</div></div></div>');
                },
                success: function (result) {
                    if (result.status == 1 && result.data) {
                        injectHtml(resultWrap, result.data);
                        syncSelectedMediaFromComposer();
                        return;
                    }

                    injectHtml(resultWrap, '<div class="alert alert-danger">' + (result.message || '{{ __("Could not load auto reply settings.") }}') + '</div>');
                },
                error: function () {
                    injectHtml(resultWrap, '<div class="alert alert-danger">{{ __("Could not load auto reply settings.") }}</div>');
                }
            });
        };

        $(document).on('change', '#wa_auto_reply_account', loadInfo);

        $(document).on('submit', '#WhatsAppAutoReplyForm', function (e) {
            e.preventDefault();
            var form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: form.serialize(),
                success: function (result) {
                    if (result.status == 1) {
                        if (window.Main && typeof Main.notification === 'function') {
                            Main.notification(result.message || '{{ __("Succeeded") }}', 'success');
                        } else {
                            alert(result.message || '{{ __("Succeeded") }}');
                        }
                        return;
                    }

                    if (window.Main && typeof Main.notification === 'function') {
                        Main.notification(result.message || '{{ __("Save failed.") }}', 'error');
                    } else {
                        alert(result.message || '{{ __("Save failed.") }}');
                    }
                },
                error: function () {
                    if (window.Main && typeof Main.notification === 'function') {
                        Main.notification('{{ __("Save failed.") }}', 'error');
                    } else {
                        alert('{{ __("Save failed.") }}');
                    }
                }
            });
        });

        bindComposeActions();
        syncComposeMediaResponsive();
        bootDynamicContent(document);
    })();
</script>
@endsection
