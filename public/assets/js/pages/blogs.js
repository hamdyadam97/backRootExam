$(document).ready(function () {
    var question = $('#question').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        "sScrollX": "100%",

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
            {data: 'id'},
            {data: 'title'},
            {data: 'category'},
            {data: 'image'},
            {
                sortable: false,
                render: function (_, _, full) {
                    var contactId = full['id'];

                    if (contactId) {
                        var actions = '<div class="datatable-btn-container d-flex align-items-center ">';

                        actions += ' <a href="' + baseUrl + 'blogs/edit/' + contactId + '" data-id="' + contactId + '" class="waves-effect waves-light pe-2" title="edit"><i class="bx bx-edit-alt bx-sm"></i></a>';
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

    $(document).on('change', '#exam_filter,#category_id,#sub_category_id,#sub_subcategory_id,#topic_id,#exam_section_id', function (e) {
        question.ajax.reload();
    })

    $('#add-form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);

        let method = $this.attr('method');
        // var dataString = new FormData($('#add-form')[0]);
        var formData = new FormData(this);
        var editorData = CKEDITOR.instances.description.getData();

        formData.append('description', editorData);

        $('.invalid-feedback').html('');

        $.ajax({
            url: addUrl,
            type: method,
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function () {
                $($this).find('button[type="submit"]').prop('disabled', true);
            },
            success: function (result) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                if (result.status) {
                    // $this[0].reset();
                    // $('#edit-id').val(0);
                    // showMessage("success", result.message);
                    // $('#question').DataTable().ajax.reload();
                    // $('#add-modal').modal('hide');

                    // window.location.href = listUrl;
                    location.reload();

                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    first_input = "";
                    $('.error').html("");
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        var org_key = key;
                        if (key.indexOf('.') !== -1) {
                            key = key.replace('.', '');
                        }

                        $('#' + key + 'Error').html('<strong>' + result.error[org_key] + '</strong>');
                        if (key == "answer_option_id") {
                            // $('#add-form .' + key).addClass('is-invalid');
                        } else {
                            $('#add-form #' + key).addClass('is-invalid');
                        }
                    });
                    var i = 0;
                    $('#add-form .answer_option').each(function () {
                        if (result.error['answer_option.' + i] && result.error['answer_option.' + i][0]) {
                            $(this).addClass('is-invalid');
                            $(this).closest('div').find('span').html('<strong>' + result.error['answer_option.' + i][0] + '</strong>');
                        }
                        i++;
                    });
                    $('#add-form').find("." + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                // alert('Something went wrong!', 'error');
                getErrors(error);

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
                        $('#question').DataTable().ajax.reload();
                    }
                });
            }
        });
    });
    $('body').on('click', '.add_answer_option', function (e) {
        e.preventDefault();
        var content = $('.add_html_answer_option').html();
        $('.answer_option_all').append(content);
    });
    $('body').on('click', '.remove_answer_option', function (e) {
        e.preventDefault();
        $(this).closest('.answer_option_div').remove();
    });
});


// function setdata(json) {
//     // if(json.questions_answers[0]){
//     //     $('#add-form').find('#exam_id').val(json.questions_answers[0].exam_id);
//     //     $('#exam_id').trigger('change');
//     //     $.each(json.questions_answers,function(key,value){
//     //         if(!key == 0){
//     //             var content = $('.add_html_answer_option').html();
//     //             $('.answer_option_all').append(content);
//     //         }
//     //     });
//     //     var i=0;
//     //     $('#add-form .answer_option').each(function(){
//     //         if(json.questions_answers[i] && json.questions_answers[i]['answer_option']){
//     //             $(this).val(json.questions_answers[i]['answer_option']);
//     //         }
//     //         i++;
//     //     });
//     // }
//     CKEDITOR.instances['text_question'].setData(json.text_question);
//
//
//     $('#add-form').find('#status_type').val(json.status);
//     $('#add-form').find('#question_type').val(json.question_type);
//     $('#add-form').find('#answer_type').val(json.answer_type);
//     $('#add-form').find('#answer_option_id').val(json.correct_answer_id);
//     $('#add-form').find('#hint').val(json.hint);
//     $('#add-form').find('#show_hint').val(json.show_hint);
//     $('#add-form').find('#show_answer').val(json.show_answer);
//     $('#add-form').find('#show_video').val(json.show_video);
//     $('#add-form').find('#video_link').val(json.video_link);
//     $('#add-form').find('#time_minutes').val(json.time_minutes);
//     // $('#add-form').find('#section_id').val(json.section_id);
//     $('#add-form').find('#answer_has_image').val(json.answer_has_image);
//     $('#add-form').find('#question_has_image').val(json.question_has_image);
//
//     $('.view-question-image a').attr("href", "#");
//     if (json.question_image) {
//         $('#add-form .view-question-image').show();
//         $('.view-question-image img').attr("src", json.question_image);
//     }
//     if (json.answer_image) {
//         $('#add-form .view-answer-image').show();
//         $('.view-answer-image img').attr("src", json.answer_image);
//     }
//
//
//     if (json.correct_answer_id) {
//         correct_answer_array = json.correct_answer_id.split(',');
//         var correctAnswerArray = correct_answer_array.map(function (x) {
//             return parseInt(x, 10);
//         });
//         var i = 1;
//         $('#add-form .answer_option_id_bkp').each(function () {
//             if (jQuery.inArray(json.questions_answers[i - 1].id, correctAnswerArray) !== -1) {
//                 $(this).val(1);
//             }
//             i++;
//         });
//         var i = 1;
//         $('#add-form .answer_option_id').each(function () {
//             if (jQuery.inArray(json.questions_answers[i - 1].id, correctAnswerArray) !== -1) {
//                 $(this).attr('checked', true);
//             }
//             i++;
//         });
//     }
//
//     for (var i = 0; i <= 4; i++) {
//         if (typeof json.questions_answers != "undefined" && typeof json.questions_answers[i] != "undefined")
//             CKEDITOR.instances['correct_answer_editor' + i].setData(json.questions_answers[i].answer_option);
//     }
// }


$(document).on('change', 'input#question_image', function (e) {
    e.preventDefault();
    let file = this.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = function (event) {
            $(".question_img").attr("src", event.target.result);
            $(".question_img").closest('.view-question-image').css('display', 'block');
        };
        reader.readAsDataURL(file);
    }

});
