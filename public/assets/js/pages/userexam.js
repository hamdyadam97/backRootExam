$(document).ready(function () {
    var userexam = $('#userexam').DataTable({
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
            { data: 'username'},
            { data: 'examtitle'},
            { data: 'score'},
            { data: 'start_date'},
            { data: 'end_date'},
            { data: 'status'},
            {
                sortable: false,
                render: function (_, _, full) {
                    console.log(full)
                    var contactId = full['id'];

                    if (contactId) {
                        var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';

                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light pe-2 edit-row" title="edit"><i class="bx bx-edit-alt bx-sm"></i></a>';
                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title="delete"><i class="bx bx-trash bx-sm"></i></a>';
                        if(full['status_number']==2){
                            actions += ' <a target="_blank" href="/userexam/download-exam-pdf?exam_id='+full['exam_id']+'&trial_id='+full['id']+'=&user_id='+full['user_id']+'" data-id="' + contactId + '" class="waves-effect waves-light text-success pe-2 " title="download"><i class="bx bxs-file-pdf bx-sm"></i></a>';
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


    $('.add-new').click(function (event) {
        $('#edit-id').val("");
        $('.modal-lable-class').html('Add');
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $('#add-form')[0].reset();
        $('#add-modal').modal('show');
        $('#user_exams_date').datepicker('destroy').datepicker();
         setTimeout( function(){
            if ($('#add-form').length > 0) {
                userexamSelect2();
            }
        },200);

    });

    function userexamSelect2() {
        if ($('#add-form select.select2').length > 0) {
            $('#add-form select.select2').each(function () {
                $(this).select2({
                    placeholder: $(this).attr('placeholder'),
                    allowClear: true,
                    dropdownParent: $('#add-modal')
                });
            });
        }
    }


    $('body').on('click', '.edit-row', function (event) {
        var id = $(this).attr('data-id');
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
                    setTimeout( function(){
                        if ($('#add-form').length > 0) {
                                userexamSelect2();
                        }
                        $('#add-form').find('#user_id').val(result.data.user_id).change();
                        $('#add-form').find('#exam_id').val(result.data.exam_id).change();
                    },200);

                    $('#add-form').find('#score').val(result.data.score);
                    $('#start_date').datepicker("update", result.data.start_date);
                    $('#end_date').datepicker("update", result.data.end_date);
                    $('#user_exam_status').val(result.data.status);

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
                    $('#userexam').DataTable().ajax.reload();
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
                // userexam.reload();
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
                        $('#userexam').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});
