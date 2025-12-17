$(document).ready(function () {
    var userexam = $('#notificationtable').DataTable({
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
        },
        columns: [
            { data: 'title'},
            { data: 'description'},

            // {
            //     sortable: false,
            //     render: function (_, _, full) {
            //         var contactId = full['id'];

            //         if (contactId) {
            //             var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';

            //             actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title="delete"><i class="bx bx-trash bx-sm"></i></a>';

            //             actions += '</div>';

            //             return actions;
            //         }

            //         return '';
            //     },
            //     width: '8%'
            // },
        ],
        "drawCallback": function (settings) {}
    });


    $('.add-new').click(function (event) {
        $('#add-form')[0].reset();
        $('#add-form #time').val('0:00');
        $('#edit-id').val("");
        $('.view-image').hide();
        $('.modal-lable-class').html('Add');
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $('#add-form')[0].reset();
        $('#add-modal').modal('show');
        $('#start_date').datepicker('destroy').datepicker();
        $('#end_date').datepicker('destroy').datepicker();

         setTimeout( function(){
            if ($('#add-form').length > 0) {
                examSelect2();
            }
        },200);
        
    });

    function examSelect2() {
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

    if ($('#time').length) {
        $('#time').timepicker({
            showMeridian: false,
            minuteStep: 15,
            defaultTime: null,
            icons: {
                up: 'mdi mdi-chevron-up',
                down: 'mdi mdi-chevron-down'
            },
            appendWidgetTo: "#timepicker-input-time"
        })
        // .on('change', function (e) {
        //     onchangeform($(this));
        // });
        // $('#shop_end_time').timepicker({
        //     showMeridian: false,
        //     minuteStep: 30,
        //     defaultTime: null,
        //     icons: {
        //         up: 'mdi mdi-chevron-up',
        //         down: 'mdi mdi-chevron-down'
        //     },
        //     appendWidgetTo: "#timepicker-input-shop_end_time"
        // }).on('change', function (e) {
        //     onchangeform($(this));
        // });
    }


      $('#cat_id').change(function() {
       
        var categoryId = $(this).val();
        getCategory(categoryId);
        
      });
      function getCategory(categoryId,sub_cat_id = ""){
        if (categoryId) {
            $.ajax({
                url: subcatUrl +'?id='+ categoryId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#sub_cat_id').empty();
                    $('#sub_cat_id').append('<option value="">Select subcategory</option>');
                    if (response.status == true) {
                      $.each(response.data, function(key, value) {
                        if(sub_cat_id==key){
                            $('#sub_cat_id').append('<option selected value="' + key + '">' + value + '</option>');
                        }else{
                            $('#sub_cat_id').append('<option value="' + key + '">' + value + '</option>');                            
                        }
                    });
                  }
              }
          });
        } else {
            $('#sub_cat_id').empty();
            $('#sub_cat_id').append('<option value="">Select subcategory</option>');
        }
      }    
   
    $('body').on('click', '.edit-row', function (event) {
        var id = $(this).attr('data-id');
        $('.view-image').hide()
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
                    
                    $('#add-form').find('#cat_id').val(result.data.cat_id).change();
                    getCategory(result.data.cat_id,result.data.sub_cat_id);
                    setTimeout( function(){
                        //$('#add-form').find('#sub_cat_id').val(result.data.sub_cat_id);
                        if ($('#add-form').length > 0) {
                            examSelect2();
                        }
                    },200);
                    
                    // $('#usertable').load();
                    $('#add-form').find('#title').val(result.data.title);
                    $('#add-form').find('#description').val(result.data.description);
                    // $('#add-form').find('#time').val(result.data.time);
                    // $('#add-form').find('#time').timepicker('setTime', result.data.time);
                    
                    // $('.view-image a').attr("href", "#");
                    // if (result.data.icon) {
                    //     $('#add-form .view-image').show();
                    //     $('.exam_img').attr("src", imgpath+result.data.icon);
                    // }
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
                    userexam.ajax.reload();
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
                // exam.reload();
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
                        $('#exam').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
});
