$(document).ready(function () {
    var usertable = $('#usertable').DataTable({
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
                d.status = $('#status_filter').val()
                d.first_name = $('#f_name_filter').val()
                d.last_name = $('#l_name_filter').val()
                d.mobile = $('#mobile_filter').val()
            },
        },
        columns: [
            {
                data: 'first_name'
            },
            {
                data: 'last_name'
            },
            { "data": "thumb" ,
              "render": function (data) {
              return '<img src="'+imgpath+data+'" width="60px;">';}
            },
            {
                data: 'role_type'
            },
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
                        if (!full.mobile_verified_at){
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light text-success pe-2 verify-row" title="verify"><i class="bx bx-user-check bx-sm"></i></a>';

                        }
                        actions += '</div>';

                        return actions;
                    }

                    return '';
                },
                width: '8%'
            },
        ],
        "drawCallback": function (settings) {}
    });

    $(document).on('change','#status_filter, #f_name_filter , #l_name_filter , #mobile_filter', function (e) {
        usertable.ajax.reload();
    })

    $('.add-new').click(function (event) {
        $('#edit-id').val("");
        $('.modal-lable-class').html('Add');
        $('.view-image').hide();
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $('#add-form')[0].reset();
        $('#add-modal').modal('show');
        $('#password_group').show();
    });

    $('body').on('click', '.edit-row', function (event) {
        var id = $(this).attr('data-id');
        $('.view-image').hide();
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
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
                    // $('#add-form').find('#password_group').hide();
                    // $('#add-form').find('#password').val(result.data.password);
                    $('#add-form').find('#first_name').val(result.data.first_name);
                    $('#add-form').find('#last_name').val(result.data.last_name);
                    // $('#add-form').find('#email').val(result.data.email);
                    $('.view-image a').attr("href", "#");
                    if (result.data.thumb) {
                        $('#add-form .view-image').show();
                        $('.user_img').attr("src", imgpath+result.data.thumb);
                    }
                    $('#add-form').find('#role_type').val(result.data.role_type);

                    iti.setCountry(result.data.mobile_country_code)

                    $('#add-form').find('input[name=mobile_number]').val(result.data.mobile_number);
                    // $('#add-form').find('#mobile').val(result.data.mobile);

                    $('#add-form').find('#token').val(result.data.token);
                    $('#add-form').find('#device_id').val(result.data.device_id);
                    $('#add-form').find('#score').val(result.data.score);
                    $('#add-form').find('#status_type').val(result.data.status);


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
                    $('#edit-id').val(0);
                    showMessage("success", result.message);
                    $('#usertable').DataTable().ajax.reload();
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
                // location.reload();
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
                        $('#usertable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
    $('body').on('click', '.verify-row', function (event) {
        var id = $(this).attr('data-id');
        Swal.fire({
            title: 'Are you sure',
            text: "You won't be able to revert this",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes verify it',
            cancelButtonText: 'No cancel',
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: verifyUrl + '?id=' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
                        if (result.status) {
                            showMessage("success", result.message);
                        } else {
                            showMessage("error", result.message);
                        }
                        $('#usertable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});
