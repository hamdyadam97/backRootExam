$(document).ready(function () {
    var table = $('#discountscode').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        order:[[0, 'desc']],
        ajax: {
            url: apiUrl,
            type: 'GET',
            headers: {
                'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            /*  data: function (d) {
                 d.search = $('input[type="search"]').val()
             }, */
        },
        columnDefs:[
            {
                target: 0,
                visible: false,
                searchable: false
            },
        ],
        columns: [
            {
                data: 'id',
                name: 'id'
            },
            {
                data: 'code',
                name: 'code'
            },
            {
                data: 'marketer',
                name: 'marketer'
            },
            {
                data: 'type',
                name: 'type'
            },
            {
                data: 'discount',
                name: 'discount',
            },

            {
                data: 'quantity',
                name: 'quantity'
            },
            {
                data: 'hyper_pays_count',
                name: 'hyper_pays_count'
            },
            {
                data: 'from_date',
                name: 'from_date'
            },
            {
                data: 'to_date',
                name: 'to_date'
            },

            {
                data: 'status',
                name: 'status'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },

        ],
        "drawCallback": function (settings) {}
    });

    $('.add-new').click(function (event) {
        $('#edit-id').val("");
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $('#add-form')[0].reset();
        $("#percentage-section").removeClass('d-none');
        $("#amount-section").addClass('d-none');
        $('#add-modal').modal('show');
        $('#from_date').datepicker('destroy').datepicker();
        $('#to_date').datepicker('destroy').datepicker();
    });

    $("#type").change(function(e) {
        var value = $(this).val();
        $("#percentage").val("");
        $("#amount").val("");

        if(value == 2) {
            $("#amount-section").removeClass('d-none');
            $("#percentage-section").addClass('d-none');
        } else {
            $("#percentage-section").removeClass('d-none');
            $("#amount-section").addClass('d-none');
        }

    });

    $("#add-form").submit(function(e){
        e.preventDefault();
        var $this = $(this);
        var dataString = new FormData($('#add-form')[0]);
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $.ajax({
            url: addUrl,
            type: 'POST',
            data: dataString,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $($this).find('button[type="submit"]').prop('disabled', true);
            },
            success: function (result) {

                console.log(result);
                $($this).find('button[type="submit"]').prop('disabled', false);
                if (result.status) {
                    $this[0].reset();
                    $('#edit-id').val(0);
                    showMessage("success", result.message);
                    $('#discountscode').DataTable().ajax.reload();
                    $('#add-modal').modal('hide');
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    first_input = "";
                    $('.error').html("");
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $('#add-form #' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                        $('#add-form #' + key).addClass('is-invalid');
                    });
                    $('#add-form').find("." + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                alert('Something went wrong!', 'error');
            }
        });
    });

    $('body').on('click', '.edit-row', function (event) {
        var id = $(this).attr('data-id');
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $('#add-form')[0].reset();
        $.ajax({
            url: detailUrl,
            type: 'GET',
            data: {id: id},
            dataType: 'json',
            success: function (result) {
                if (result.status) {
                    $('#edit-id').val(id);
                    $('.modal-lable-class').html('Edit');
                    $('#add-modal').modal('show');

                    $("#code").val(result.data.code);
                    $("#marketer").val(result.data.marketer);

                    $("#type").val(result.data.type);

                    if(result.data.type == 1) {
                        $("#percentage").val(result.data.percentage);
                        $("#percentage-section").removeClass('d-none');
                        $("#amount-section").addClass('d-none');
                    } else {
                        $("#amount").val(result.data.amount);
                        $("#percentage-section").addClass('d-none');
                        $("#amount-section").removeClass('d-none');
                    }

                    $("#quantity").val(result.data.quantity)
                    $('#from_date').datepicker("update", result.data.from_date);
                    $('#to_date').datepicker("update", result.data.to_date);
                    $('#status_type').val(result.data.status);
                }
            }
        });
    });

    $('body').on('click', '.delete-row', function (event) {
        var id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure',
            text: "You won't be able to revert this",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes delete it',
            cancelButtonText: 'No cancel',
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {id: id},
                    dataType: 'json',
                    success: function (result) {
                        if (result.status) {
                            showMessage("success", result.message);
                        } else {
                            showMessage("error", result.message);
                        }
                        $('#discountscode').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});
