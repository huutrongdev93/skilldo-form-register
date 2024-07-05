<div class="modal fade" id="js_export_form_register_result_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">{{ trans('export.gfr.title') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="js_export_form_register_result_form">
                {!! Admin::loading() !!}
                <div class="modal-body" style="overflow-x:auto; max-height:500px;">
                    <div class="form-group">
                        <label class="form-check radio d-block mb-2">
                            <input type="radio" name="exportType" value="pageCurrent" class="form-check-input" checked> {{trans('export.options.page')}}
                        </label>
                        <label class="form-check radio d-block mb-2">
                            <input type="radio" name="exportType" value="check" class="form-check-input"> {{trans('export.options.check')}}
                        </label>
                        <label class="form-check radio d-block mb-2">
                            <input type="radio" name="exportType" value="searchCurrent" class="form-check-input"> {{trans('export.options.search')}}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-white" type="button" data-bs-dismiss="modal" aria-label="Close">{{trans('button.cancel')}}</button>
                    <button class="btn btn-blue" type="button" id="js_export_form_register_result_btn_submit"><i class="fa-light fa-download"></i> {{trans('export.data')}}</button>
                </div>
            </div>
            <div id="js_export_form_register_result_result" style="display:none;">
                <div class="modal-body">
                    <a href="" class="btn btn-blue btn-blue-bg" download><i class="fa-duotone fa-file-excel"></i> {{trans('export.button.download')}}</a>
                    <button class="btn btn-white" type="button" data-bs-dismiss="modal" aria-label="Close">{{trans('button.close')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        class ExportFormResultHandel {
            constructor() {
                this.modalHandle = new bootstrap.Modal('#js_export_form_register_result_modal')
                this.modal = $('#js_export_form_register_result_modal')
            }
            openModal(element) {
                this.modal.find('#js_export_form_register_result_form').show();
                this.modal.find('#js_export_form_register_result_result').hide();
                this.modalHandle.show()
                return false;
            }
            export(element) {
                let self = this;

                let data = {};

                let filter  = $(':input', $('#table-form-filter')).serializeJSON();

                let search = $(':input', $('#table-form-search')).serializeJSON();

                data.search = {...search, ...filter}

                let exportType = this.modal.find('input[name="exportType"]:checked').val();

                if(exportType === 'pageCurrent') {

                    data.listData = [];

                    let divElements = document.querySelectorAll('tr[class*="tr_"]');

                    divElements.forEach(function(element) {
                        let classList = element.classList;
                        for (let i = 0; i < classList.length; i++) {
                            if (classList[i].startsWith("tr_")) {
                                let number = classList[i].substr(3); // Cắt bỏ phần "tr_"
                                data.listData.push(number)
                            }
                        }
                    });

                    if(data.listData.length === 0) {
                        SkilldoMessage.error(lang.get('export.error.noData'));
                        return false;
                    }
                }

                if(exportType === 'check') {

                    data.listData = []; let i = 0;

                    $('.select:checked').each(function () { data.listData[i++] = $(this).val(); });

                    if(data.listData.length === 0) {
                        SkilldoMessage.error(lang.get('export.error.noChoose'));
                        return false;
                    }
                }

                if(typeof data == "undefined") {
                    SkilldoMessage.error(lang.get('export.error.type.illegal'));
                    return false;
                }

                this.modal.find('#js_export_form_register_result_form .loading').show();

                data.action = 'Form_Register_Ajax::export';

                data.exportType = exportType

                request.post(ajax, data).then(function (response) {
                    if (response.status === 'success') {
                        self.modal.find('#js_export_form_register_result_form .loading').hide();
                        self.modal.find('#js_export_form_register_result_form').hide();
                        self.modal.find('#js_export_form_register_result_result a').attr('href', response.data);
                        self.modal.find('#js_export_form_register_result_result').show();
                    }
                });

                return false;
            }
        }

        const exportForm = new ExportFormResultHandel();

        $(document)
            .on('click', '#js_export_form_register_result_btn_modal', function () {
                return exportForm.openModal($(this))
            })
            .on('click', '#js_export_form_register_result_btn_submit', function () {
                return exportForm.export($(this))
            })
    })
</script>
<style>
    .table.table tr.new td {
        background-color: #e1f1ea;
    }
</style>
