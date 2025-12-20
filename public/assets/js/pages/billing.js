var billing;

function getBillingTable() {
    billing = $('#invoiceTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
            url: apiUrl,
            type: 'GET',
            headers: {
                'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            data: function (d) {
                d.search = $('input[type="search"]').val();
            },
        },
        columns: [
            {
        data: 'id',
        render: function (data) {
            return `<input type="checkbox" class="invoice-checkbox" value="${data}">`;
        },
        orderable: false,
        searchable: false
    },
            {data: 'invoice_number'},
            {data: 'username'},
            {data: 'packagename'},
            {data: 'total_amount'},
            {data: 'status'},
            {data: 'created_at'},
            {
                sortable: false,
                render: function (_, _, full) {
                    let id = full.id;
                    return `
                        <div class="datatable-btn-container d-flex justify-content-between">
                            <a href="javascript:void(0)" data-id="${id}" class="edit-row">
                                <i class="bx bx-edit-alt bx-sm"></i>
                            </a>
                            <a href="javascript:void(0)" data-id="${id}" class="delete-row text-danger">
                                <i class="bx bx-trash bx-sm"></i>
                            </a>
                        </div>
                    `;
                },
            },
        ],
    });
}
function sendToBillingSystem(invoiceIds) {

    $.ajax({
        url: exportUrl,
        type: "POST",
        data: {
            invoices: invoiceIds,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            if (res.status) {
                Swal.fire('Done', 'Invoices sent successfully', 'success');
                billing.ajax.reload(null, false);
            }
        }
    });
}

$(document).ready(function () {

    getBillingTable();

    $('#checkAll').on('change', function () {
    $('.invoice-checkbox').prop('checked', this.checked);
});

$('#sendInvoices').on('click', function () {

        let invoiceIds = [];

        $('.invoice-checkbox:checked').each(function () {
            invoiceIds.push($(this).val());
        });

        if (invoiceIds.length === 0) {
            Swal.fire('Error', 'Select at least one invoice', 'error');
            return;
        }

        Swal.fire({
            title: 'Send invoices?',
            text: `You selected ${invoiceIds.length} invoice(s)`,
            icon: 'warning',
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                sendToBillingSystem(invoiceIds);
            }
        });

    });

    $('.add-new').click(function () {
        $('#add-form')[0].reset();
        $('#edit-id').val('');
        $('.modal-lable-class').text('Add');
        $('.invalid-feedback').html('');
        $('#add-modal.is-invalid').removeClass('is-invalid');
        $('#add-modal').modal('show');
    });

    $('body').on('click', '.edit-row', function () {
        let id = $(this).data('id');

        $.get(detailUrl, {id: id}, function (res) {
            if (res.status) {
                $('#edit-id').val(res.data.id);
                $('#total_amount').val(res.data.total_amount);
                $('#invoice_number').val(res.data.invoice_number);
                $('#username').val(res.data.user);
                $('#package_name').val(res.data.package);
                $('#status').val(res.data.status);
                $('.modal-lable-class').text('Edit');
                $('#add-modal').modal('show');
            }
        });
    });

    $('#add-form').submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: addUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status) {
                    $('#add-modal').modal('hide');
                    billing.ajax.reload(null, false);
                    Swal.fire('Saved', '', 'success');
                }
            }
        });
    });

    $('body').on('click', '.delete-row', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
        }).then((res) => {
            if (res.isConfirmed) {
                $.post(deleteUrl, {id: id}, function () {
                    billing.ajax.reload(null, false);
                });
            }
        });
    });

});
