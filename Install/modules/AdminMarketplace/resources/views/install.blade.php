<div class="modal fade" id="installModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header bg-light-subtle border-0">
                <h5 class="modal-title fw-semibold text-dark fs-16">
                    <i class="fa-light fa-file-zipper me-2 text-primary"></i> {{ __("Install Plugins / Addons") }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div>
                <form class="actionForm p-4" action="{{ module_url('do-install-zip') }}" method="POST" enctype="multipart/form-data" data-call-success="afterAddonInstall(result);">
                    @csrf
                    <div class="msg-errors mb-3"></div>
                    <div class="mb-3">
                        <label for="module_zip" class="form-label fw-semibold text-muted">{{ __("Select ZIP File") }}</label>
                        <input
                            type="file"
                            class="form-control form-control-lg rounded-3"
                            name="module_zip"
                            id="module_zip"
                            accept=".zip"
                            required
                        >
                    </div>
                    <div class="alert alert-light small d-flex align-items-start mt-3 mb-0 b-r-10">
                        <ul>
                            <li class="d-flex gap-2 mb-2">
                                <i class="fa-light fa-info-circle me-1 mt-1 text-primary"></i>
                                <span>{{ __("You can use this to install a new module or update an existing one.") }}</span>
                            </li>
                            <li class="d-flex gap-2 mb-2">
                                <i class="fa-light fa-info-circle me-1 mt-1 text-primary"></i>
                                <span>{{ __("The ZIP file must be a valid module package.") }}</span>
                            </li>
                            <li class="d-flex gap-2 mb-0">
                                <i class="fa-light fa-info-circle me-1 mt-1 text-primary"></i>
                                <span>{{ __("Ensure your server has write permission to /modules directory.") }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-footer border-0 px-0 pb-0 mt-3">
                        <button type="submit" class="btn btn-dark w-100 rounded-pill btn-lg">
                            <i class="fa fa-check-circle me-1"></i> {{ __("Install from ZIP") }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function afterAddonInstall(res) {
    if(res.status === 1 && res.id) {
        let $dummyBtn = $('<button/>');

        Main.ajaxPost(
            $dummyBtn, 
            "{{ module_url('active') }}/" + res.id, "",
            function(enableRes) {
                if(enableRes.status === 1) {
                    Main.closeModal('installModal');
                    setTimeout(() => location.reload(), 1200);
                }
            }
        );
    }
}
</script>