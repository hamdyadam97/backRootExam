$(document).ready(function () {
    var subcategory = $('#subcategory').DataTable({
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
                d.category_id = $('#category_id').val()
            },
        },
        columns: [
            { data: 'categoryname'},
            {
                data: 'name'
            },
            { "data": "icon" ,
              "render": function (data) {
              return '<img src="'+imgpath+data+'" width="60px;">';}
            },
            {
                data: 'order'
            },
            {'data': 'foreground_color', 'name': 'foreground_color'},
            {'data': 'background_color', 'name': 'background_color'},
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
        "drawCallback": function (settings) {}
    });

    $(document).on('change','#category_id', function (e) {
        subcategory.ajax.reload();
    })
    $('.filter_category').select2({
        placeholder: $(this).attr('placeholder'),
        allowClear: true,
    });


    $('.add-new').click(function (event) {
        $('#foreground_color').spectrum({
            togglePaletteOnly:true,
            showPaletteOnly: true,
        });
        $('#background_color').spectrum({
            togglePaletteOnly:true,
            showPaletteOnly: true,
        });
        $('#edit-id').val("");
        $('.modal-lable-class').html('Add');
        $('.view-image').hide();
        $('#add-form').find('#foreground_color').spectrum('set','#000000');
        $('#add-form').find('#background_color').spectrum('set','#000000');
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $('#add-form')[0].reset();
        $('#add-modal').modal('show');
         setTimeout( function(){
            if ($('#add-form').length > 0) {
                subcatSelect2();
            }
        },200);

    });

    function subcatSelect2() {
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
        $('#foreground_color').spectrum({
            togglePaletteOnly:true,
            showPaletteOnly: true,
        });
        $('#background_color').spectrum({
            togglePaletteOnly:true,
            showPaletteOnly: true,
        });
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
                    $('#add-form').find('#name').val(result.data.name);
                    $('.view-image a').attr("href", "#");
                    if (result.data.icon) {
                        $('#add-form .view-image').show();
                        $('.subcat_img').attr("src", imgpath+result.data.icon);
                    }
                    $('#add-form').find('#foreground_color').spectrum('set',result.data.foreground_color);
                    $('#add-form').find('#background_color').spectrum('set',result.data.background_color);
                    $('#add-form').find('#status_type').val(result.data.status);
                    $('#add-form').find('#order').val(result.data.order);
                    setTimeout( function(){
                        if ($('#add-form').length > 0) {
                                subcatSelect2();
                        }
                        $('#add-form').find('#cat_id').val(result.data.cat_id).change();
                    },200);

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
                    $('#subcategory').DataTable().ajax.reload();
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
                // subcategory.reload();
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
                        $('#subcategory').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});
