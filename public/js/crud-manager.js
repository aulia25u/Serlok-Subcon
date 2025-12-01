class CrudManager {
    constructor(config) {
        this.entity = config.entity; // e.g., 'dept', 'section'
        this.config = config;
        this.routes = config.routes;
        this.columns = config.columns;
        this.modalId = config.modalId;
        this.formId = config.formId;
        this.tableId = config.tableId;
        this.filterBtnId = config.filterBtnId;
        this.resetBtnId = config.resetBtnId;
        this.addBtnId = config.addBtnId;
        this.dateFilters = config.dateFilters || false;
        this.onEdit = config.onEdit || null;
        this.onAdd = config.onAdd || null;
        this.onModalHidden = config.onModalHidden || null;

        this.table = null;
        this.init();
    }

    init() {
        this.initDataTable();
        this.initListeners();
    }

    initDataTable() {
        const self = this;
        const defaultOptions = {
            processing: true,
            serverSide: true,
            ajax: {
                url: this.routes.index,
                data: function (data) {
                    if (self.dateFilters) {
                        data.start_date = $(`#${self.entity}_start_date`).val();
                        data.end_date = $(`#${self.entity}_end_date`).val();
                    }
                    // Allow custom data extension
                    if (self.config && self.config.ajaxData) {
                         self.config.ajaxData(data);
                    }
                }
            },
            columns: this.columns,
            responsive: true,
            pageLength: 10
        };

        const options = $.extend(true, {}, defaultOptions, this.config.options || {});

        this.table = $(this.tableId).DataTable(options);
    }

    initListeners() {
        const self = this;

        // Filter & Reset
        if (this.filterBtnId) {
            $(this.filterBtnId).on('click', () => this.table.draw());
        }
        if (this.resetBtnId) {
            $(this.resetBtnId).on('click', () => {
                $(`#${self.entity}_start_date, #${self.entity}_end_date`).val('');
                this.table.draw();
            });
        }

        // Add Button
        if (this.addBtnId) {
            $(this.addBtnId).on('click', function () {
                $(`${self.modalId}Label`).text(`Add ${self.capitalize(self.entity)}`);
                $(`${self.formId}`)[0].reset();
                $(`#${self.entity}_id`).val('');
                $(`#${self.entity}_form_method`).val('POST');

                // Specific logic for Role which uses a different form action approach in original code
                self.showAddModal();
            });
        }

        // Modal Hidden
        $(this.modalId).on('hidden.bs.modal', function () {
            $(`${self.formId}`)[0].reset();
            $(`#${self.entity}_id`).val('');
            if ($(`#${self.entity}_form_method`).length) {
                $(`#${self.entity}_form_method`).val('POST');
            }
            if (self.onModalHidden) self.onModalHidden();
        });

        // Submit Form
        $(this.formId).on('submit', function (e) {
            e.preventDefault();
            const id = $(`#${self.entity}_id`).val();
            let url, type;

            if (self.entity === 'role') {
                // Role uses action attribute
                url = $(this).attr('action');
                type = $(this).find('input[name="_method"]').val() === 'PUT' ? 'PUT' : 'POST';
            } else {
                url = id ? `${self.routes.index}/${id}` : self.routes.store;
                type = id ? 'PUT' : 'POST';
            }

            $.ajax({
                url: url,
                type: type,
                data: $(this).serialize(),
                success: function (response) {
                    $(self.modalId).modal('hide');
                    $('.modal-backdrop').remove();
                    self.table.draw(false);
                    toastr.success(response.success);
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors || {};
                    Object.values(errors).forEach(msg => toastr.error(msg[0]));
                    if (!Object.keys(errors).length && xhr.responseJSON && xhr.responseJSON.error) {
                        toastr.error(xhr.responseJSON.error);
                    }
                }
            });
        });

        // Edit Button
        $(document).on('click', `.${self.entity}-edit-btn`, function () {
            const id = $(this).data('id');
            const editUrl = `${self.routes.index}/${id}/edit`;

            $.get(editUrl, function (response) {
                $(`${self.modalId}Label`).text(`Edit ${self.capitalize(self.entity)}`);
                $(`#${self.entity}_id`).val(response.id);

                if (self.entity === 'role') {
                    $(`${self.formId}`).attr('action', `${self.routes.index}/${id}`);
                    $(`${self.formId}`).find('input[name="_method"]').remove();
                    $(`${self.formId}`).append('<input type="hidden" name="_method" value="PUT">');
                } else {
                    $(`#${self.entity}_form_method`).val('PUT');
                }

                if (self.onEdit) self.onEdit(response);
                $(self.modalId).modal('show');
            }).fail(function () {
                toastr.error('Failed to load data.');
            });
        });

        // Delete Button
        $(document).on('click', `.${self.entity}-delete-btn`, function () {
            const id = $(this).data('id');
            if (confirm(`Are you sure you want to delete this ${self.entity}?`)) {
                $.ajax({
                    url: `${self.routes.index}/${id}`,
                    type: 'POST', // Using POST with _method DELETE for consistency
                    data: { _method: 'DELETE' },
                    success: function (response) {
                        self.table.draw(false);
                        toastr.success(response.success);
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                    }
                });
            }
        });
    }

    showAddModal() {
        const self = this;
        $(self.formId)[0].reset();
        $(self.modalId).find('.modal-title').text('Add ' + self.capitalize(self.entity));
        $(self.formId).find('input[name="id"]').val('');
        $(self.formId).find('input[name="_method"]').val('POST');
        
        // Specific logic for Role
        if (self.entity === 'role') {
             $(`${self.formId}`).attr('action', self.routes.store);
             $(`${self.formId}`).find('input[name="_method"]').remove();
        }

        $(self.modalId).modal('show');
        if (self.onAdd) self.onAdd();
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
}
