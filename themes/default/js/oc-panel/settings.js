//settins scripts 

// $('#allowed_formats option').each(function(){
//  $(this).attr('selected', 'selected');
// });

// jQuery.validator with bootstrap integration
jQuery.validator.setDefaults({
    highlight: function(element) {
        jQuery(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        jQuery(element).closest('.form-group').removeClass('has-error');
    },
    errorElement: 'span',
    errorClass: 'label label-danger',
    errorPlacement: function(error, element) {
        if(element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

$('.config').validate();

$('.plan-add').click(function() {
    $("#modalplan input[name='featured_days']").val('');
    $("#modalplan input[name='featured_price']").val('');
    $("#modalplan input[name='featured_days_key']").val('');
});
$('.plan-edit').click(function() {
    $('#modalplan').modal('show');
    $("#modalplan input[name='featured_days']").val($(this).data('days'));
    $("#modalplan input[name='featured_days_key']").val($(this).data('days'));
    $("#modalplan input[name='featured_price']").val($(this).data('price'));
});
$('.plan-delete').click(function(e) {
    e.preventDefault();
    $(this).closest('li').slideUp();
    $.ajax({url: $(this).attr('href')});
});

initPNotify();

function initPNotify() {
    $('form.ajax-load').submit(function(event) {
        $form = $(this);

        // process the form
        $.ajax({
            type        : $form.attr('method'),
            url         : $form.attr('action'),
            data        : $form.serialize(),
        })
        
            // using the done promise callback
            .done(function(data) {

                $(data).find('.alert').each(function() {
                    var notifyType = 'notice';
                    var notifyTitle = $(this).find('.alert-heading:first').text();
                    var notifyTitle = $(this).find('strong:first').text() + notifyTitle;
                    var notifyText = $(this).find('.close').remove();
                    var notifyText = $(this).find('.alert-heading').remove();
                    var notifyText = $(this).html();

                    if ($(this).hasClass('alert-info')) notifyType = 'info';
                    else if ($(this).hasClass('alert-success')) notifyType = 'success';
                    else if ($(this).hasClass('alert-danger')) notifyType = 'error';

                    new PNotify({
                        title: notifyTitle,
                        text: notifyText,
                        type: notifyType,
                        insert_brs: false,
                        delay: 4000,
                        styling: 'bootstrap3',
                    });
                });
            })

            .fail(function(data) {
                // show any errors
                console.log(data);
            });

        event.preventDefault();
    });
}

$(function(){
    service = $('#tab-settings li.active').find('.email-service').attr('id');

    if (service == 'smtp' || service == 'gmail' || service == 'outlook' || service == 'yahoo' || service == 'zoho')
    {
        showSmtpConfig();     
    }
    else if(service == 'elastic') {
        showElasticConfig();
    }
    else {
        hideEmailConfig();
    }
});

function hideEmailConfig() {
    $('#elastic-config').hide();
    $('#smtp-config').hide();
}

function showSmtpConfig() {
    $('#elastic-config').hide();
    $('#smtp-config').show();
}

function showElasticConfig() {
    $('#elastic-heading, #smtp-heading, #gmail-heading, #outlook-heading, #yahoo-heading, #zoho-heading').hide();
    $('input[name="service"]').val('elastic');
    $('#elastic-heading').show();
    $('#elastic-config').show();
    $('#smtp-config').hide();
}

function clearSmtpConfig() {
    $('select[name="smtp_secure"]').val('').trigger("change");
    $('input[name="smtp_host"]').val('');
    $('input[name="smtp_port"]').val('');
    $('select[name="smtp_auth"]').val('0').trigger("change");
    $('input[name="smtp_user"]').val('');
    $('input[name="smtp_pass"]').val('');
}

function clearElasticConfig() {
    $('input[name="elastic_username"]').val('');
    $('input[name="elastic_password"]').val('');
    $('input[name="elastic_listname"]').val('');
}

function smtpConfig() {
    $('#elastic-heading, #smtp-heading, #gmail-heading, #outlook-heading, #yahoo-heading, #zoho-heading').hide();
    $('#smtp-heading').show();
    $('input[name="service"]').val('smtp');
}

function gmailConfig() {
    $('#elastic-heading, #smtp-heading, #gmail-heading, #outlook-heading, #yahoo-heading, #zoho-heading').hide();
    $('#gmail-heading').show();
    $('input[name="service"]').val('gmail');
    $('select[name="smtp_secure"]').val('ssl').trigger("change");
    $('input[name="smtp_host"]').val('smtp.gmail.com');
    $('input[name="smtp_port"]').val('465');
    $('select[name="smtp_auth"]').val('1').trigger("change");
    $('input[name="smtp_user"]').val('');
    $('input[name="smtp_pass"]').val('');
}

function outlookConfig() {
    $('#elastic-heading, #smtp-heading, #gmail-heading, #outlook-heading, #yahoo-heading, #zoho-heading').hide();
    $('#outlook-heading').show();
    $('input[name="service"]').val('outlook');
    $('select[name="smtp_secure"]').val('tls').trigger("change");
    $('input[name="smtp_host"]').val('smtp.office365.com');
    $('input[name="smtp_port"]').val('587');
    $('select[name="smtp_auth"]').val('1').trigger("change");
    $('input[name="smtp_user"]').val('');
    $('input[name="smtp_pass"]').val('');
}

function yahooConfig() {
    $('#elastic-heading, #smtp-heading, #gmail-heading, #outlook-heading, #yahoo-heading, #zoho-heading').hide();
    $('#yahoo-heading').show();
    $('input[name="service"]').val('yahoo');
    $('select[name="smtp_secure"]').val('ssl').trigger("change");
    $('input[name="smtp_host"]').val('smtp.mail.yahoo.com');
    $('input[name="smtp_port"]').val('465');
    $('select[name="smtp_auth"]').val('1').trigger("change");
    $('input[name="smtp_user"]').val('');
    $('input[name="smtp_pass"]').val('');
}

function zohoConfig() {
    $('#elastic-heading, #smtp-heading, #gmail-heading, #outlook-heading, #yahoo-heading, #zoho-heading').hide();
    $('#zoho-heading').show();
    $('input[name="service"]').val('zoho');
    $('select[name="smtp_secure"]').val('tls').trigger("change");
    $('input[name="smtp_host"]').val('smtp.zoho.com');
    $('input[name="smtp_port"]').val('587');
    $('select[name="smtp_auth"]').val('1').trigger("change");
    $('input[name="smtp_user"]').val('');
    $('input[name="smtp_pass"]').val('');
}

$('#tab-settings a[class="email-service"][data-toggle="tab"]').on('shown.bs.tab', function (e) {
    service = $(this).attr('id');
    switch(service) {
        case 'smtp':
            clearElasticConfig();
            clearSmtpConfig();    
            smtpConfig();
            showSmtpConfig();    
            break;
        case 'gmail':
            clearElasticConfig();
            clearSmtpConfig();    
            gmailConfig();
            showSmtpConfig();    
            break;
        case 'outlook':
            clearElasticConfig();
            clearSmtpConfig();    
            outlookConfig();
            showSmtpConfig();
            break;
        case 'yahoo':
            clearElasticConfig();
            clearSmtpConfig();    
            yahooConfig();
            showSmtpConfig();
            break;
        case 'zoho':
            clearElasticConfig();
            clearSmtpConfig();    
            zohoConfig();
            showSmtpConfig();
            break;
        case 'elastic':
            clearSmtpConfig();
            showElasticConfig();
            break;
        default:
            $('#elastic-heading, #smtp-heading, #gmail-heading, #outlook-heading, #yahoo-heading, #zoho-heading').hide();
            clearSmtpConfig();
            clearElasticConfig();
            hideEmailConfig();
    }
})
