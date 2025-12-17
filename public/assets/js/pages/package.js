$(document).ready(function () {
    var package = $('#package').DataTable({
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
                d.search = $('input[type="search"]').val()
            },
        },
        columns: [

            {data: 'name'},
            {data: 'price'},

            {
                "data": "icon",
                "render": function (data) {
                    return '<img src="' + imgpath + data + '" width="60px;">';
                }
            },
            {data: 'category'},
            {data: 'sub_category'},
            // {
            //     data: 'number_ocategoryf_questions'
            // },
            // {
            //     data: 'no_of_exams'
            // },
            // {
            //     data: 'no_of_trial'
            // },
            {
                data: 'status_name',
                name: 'status',
                render: function (_, _, full) {
                    var contactId = full['id'];

                    if (contactId) {
                        let badgeClass;
                        if (full['status'] == 1) {
                            badgeClass = 'bg-primary';
                        } else if (full['status'] == 0) {
                            badgeClass = 'bg-danger';
                        }
                        var actions = '<h5><span class="badge ' + badgeClass + '">' + full['status_name'] + '</span></h5>';

                        return actions;
                    }

                    return '';
                }
            },
            {
                sortable: false,
                render: function (_, _, full) {
                    var contactId = full['id'];

                    if (contactId) {
                        var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';

                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light pe-2 edit-row" title="edit"><i class="bx bx-edit-alt bx-sm"></i></a>';
                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title="delete"><i class="bx bx-trash bx-sm"></i></a>';

                        actions += '</div>';

                        return actions;
                    }

                    return '';
                },
                width: '8%'
            },
        ],
        "drawCallback": function (settings) {
        }
    });

    $('#exam_id').select2({dropdownParent: $('#add-modal')});
    $('#category_id').select2({dropdownParent: $('#add-modal')});
    $('#sub_category_id').select2({dropdownParent: $('#add-modal')});

    let options = "";
    $(document).on('change', 'select#category_id', function () {
        // console.log(categories)
        let selected = categories.find((i) => i.id == $(this).val());

        options = `<option  value="">Select SubCategory</option>`;
        if (selected?.sub_categories) {
            selected.sub_categories.forEach((i) => {
                options += `<option value="${i.id}">${i.name}</option>`
            });
        }

        $("#sub_category_id").select2("destroy");
        $('#sub_category_id').html(options);
        $('#sub_category_id').select2({dropdownParent: $('#add-modal')});

    })
    $('.add-new').click(function (event) {
        $('#edit-id').val("");
        $('.modal-lable-class').html('Add');
        $('.view-image').hide();
        $('.invalid-feedback').html('');
        $('#exam_id').val("").trigger('change');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $('#add-form')[0].reset();
        $('#add-modal').modal('show');

    });


    $('body').on('click', '.edit-row', function (event) {
        var id = $(this).attr('data-id');
        $('.view-image').hide();
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $('#exam_id').val("").trigger('change');
        $('#add-form')[0].reset();
        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.status) {
                    $('#edit-id').val(id);
                    $('.modal-lable-class').html('Edit');
                    $('#add-modal').modal('show');
                    $('#add-form').find('#name').val(result.data.name);
                    $('#add-form').find('#price').val(result.data.price);
                    // $('#add-form').find('#number_of_questions').val(result.data.number_of_questions);
                    // $('#add-form').find('#no_of_exams').val(result.data.no_of_exams);
                    // $('#add-form').find('#no_of_trial').val(result.data.no_of_trial);
                    $('#add-form').find('#period').val(result.data.period);
                    $('#add-form').find('#status_type').val(result.data.status);
                    $('#add-form').find('#category_id').val(result.data.category_id);
                    // $('#add-form').find('#exam_id').val(result.data.exam_ids);
                    // $('#exam_id').trigger('change');
                    $('#category_id').trigger('change');
                    $('#add-form').find('#sub_category_id').val(result.data.sub_category_ids);
                    $('#sub_category_id').trigger('change');
                    $('.view-image a').attr("href", "#");
                    if (result.data.icon) {
                        $('#add-form .view-image').show();
                        $('.pack_img').attr("src", imgpath + result.data.icon);
                    }
                }
            }
        });
    });
    $('#add-form').submit(function (event) {
        event.preventDefault();
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
                $($this).find('button[type="submit"]').prop('disabled', false);
                if (result.status) {
                    $this[0].reset();
                    $('#add-form').find('#category_id').val('');
                    $('#category_id').trigger('change');

                    $('#edit-id').val(0);
                    showMessage("success", result.message);
                    $('#package').DataTable().ajax.reload();
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
                // package.reload();
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
                    url: deleteUrl + '?id=' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
                        if (result.status) {
                            showMessage("success", result.message);
                        } else {
                            showMessage("error", result.message);
                        }
                        $('#package').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});
