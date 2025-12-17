function showMessage(e = "success", a = "") {
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: false,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "3000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        closeEasing: "linear",
        showMethod: "show",
        hideMethod: "hide",
        closeMethod: "hide"
    }, "success" != e && "Success" != e || toastr.success(a), "warning" != e && "Warning" != e || toastr.warning(a), "info" != e && "Info" != e || toastr.info(a), "error" != e && "Error" != e || toastr.error(a)
}

function pagereload() {
    var interval = setInterval(function () {
        location.reload(true);
        clearInterval(interval);
    }, 1500);
}
$(document).ready(function () {
    var intervalAlert = setInterval(function () {
        $('.alert').hide();
        clearInterval(intervalAlert);
    }, 6000);
    $("input").attr("autocomplete", "off");
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

if($.fn.datepicker){
    $.fn.datepicker.dates.de = { days: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"], daysShort: ["Son", "Mon", "Die", "Mit", "Don", "Fre", "Sam"], daysMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"], months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"], monthsShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"], today: "Heute", monthsTitle: "Monate", clear: "Löschen", weekStart: 1 }
}

if ($('input[data-provide="datepicker"], div[data-provide="datepicker"] input').length > 0) {
    $('input[data-provide="datepicker"], div[data-provide="datepicker"] input').datepicker({
        language: localLang
    });
}

if (jQuery.fn.dataTableExt) {
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "extract-date-pre": function (value) {
            var date = $(value, 'span')[0].innerHTML;
            date = date.split('.');
            return Date.parse(date[2] + '/' + date[1] + '/' + date[0])
        },
        "extract-date-asc": function (a, b) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },
        "extract-date-desc": function (a, b) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });
}

$('body').on('keyup', 'form input, form textarea', function (event) {
    onchangeform($(this));
});

if ($('input[data-provide="datepicker"]').length > 0) {
    $('input[data-provide="datepicker"]').datepicker().on('change', function (ev) {
        onchangeform($(this));
    });
}

function onchangeform(element) {
    if ($(element).val().length > 0) {
        $(element).closest('.mb-3').find('.invalid-feedback').html('');
        if ($(element).hasClass('is-invalid'))
            $(element).removeClass('is-invalid');

        if ($(element).parents('.input-group').hasClass('is-invalid'))
            $(element).parents('.input-group').removeClass('is-invalid');
    }
}

$('body').on("change", "form input[type='file'], form input[type='checkbox'], form select, form input[type='radio']", function (event) {
    onchangeform($(this));
});

function changeDatatableLang() {
    if (typeof localLang !== 'undefined') {
        if (localLang == 'de') {
            return {
                "sEmptyTable": "Keine Daten in der Tabelle vorhanden",
                "sInfo": "_START_ bis _END_ von _TOTAL_ Einträgen",
                "sInfoEmpty": "0 bis 0 von 0 Einträgen",
                "sInfoFiltered": "(gefiltert von _MAX_ Einträgen)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ Einträge anzeigen",
                "sLoadingRecords": "Wird geladen...",
                "sProcessing": "Bitte warten...",
                "sSearch": "Suchen",
                "sZeroRecords": "Keine Einträge vorhanden.",
                "oPaginate": {
                    "sFirst": "Erste",
                    "sPrevious": "Zurück",
                    "sNext": "Nächste",
                    "sLast": "Letzte"
                },
                "oAria": {
                    "sSortAscending": ": aktivieren, um Spalte aufsteigend zu sortieren",
                    "sSortDescending": ": aktivieren, um Spalte absteigend zu sortieren"
                }
            };
        } else {
            return {
                "sEmptyTable": "No data available in table",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                "sInfoEmpty": "Showing 0 to 0 of 0 entries",
                "sInfoFiltered": "(filtered from _MAX_ total entries)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Show _MENU_ entries",
                "sLoadingRecords": "Loading...",
                // "sProcessing": "Processing...",
                "sSearch": "Search:",
                "sZeroRecords": "No matching records found",
                "oPaginate": {
                    "sFirst": "First",
                    "sLast": "Last",
                    "sNext": "Next",
                    "sPrevious": "Previous"
                },
                "oAria": {
                    "sSortAscending": ": activate to sort column ascending",
                    "sSortDescending": ": activate to sort column descending"
                }
            };
        }
    } else {
        return {
            "sEmptyTable": "No data available in table",
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
            "sInfoEmpty": "Showing 0 to 0 of 0 entries",
            "sInfoFiltered": "(filtered from _MAX_ total entries)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "Show _MENU_ entries",
            "sLoadingRecords": "Loading...",
            // "sProcessing": "Processing...",
            "sSearch": "Search:",
            "sZeroRecords": "No matching records found",
            "oPaginate": {
                "sFirst": "First",
                "sLast": "Last",
                "sNext": "Next",
                "sPrevious": "Previous"
            },
            "oAria": {
                "sSortAscending": ": activate to sort column ascending",
                "sSortDescending": ": activate to sort column descending"
            }
        };
    }
}


function getErrors(jqXhr, path) {
    // hideLoader();
    switch (jqXhr.status) {
        case 401 :
            // $(location).prop('pathname', path);
            // break;
            customSweetAlert(
                'error',
                jqXhr.responseJSON.message,
                ''
            );
        case 400 :
            customSweetAlert(
                'error',
                jqXhr.responseJSON.message,
                ''
            );
            break;
        case 422 :
            (function ($) {
                var $errors = jqXhr.responseJSON.errors;
                var errorsHtml = '<ul style="list-style-type: none">';
                $.each($errors, function (key, value) {
                    errorsHtml += '<li style="font-family: \'Droid.Arabic.Kufi\' !important">' + value[0] + '</li>';
                });
                errorsHtml += '</ul>';
                customSweetAlert(
                    'error',
                    'Something went wrong!',
                    errorsHtml
                );
            })(jQuery);

            break;
        default:
            errorCustomSweet();
            break;
    }
    return false;
}