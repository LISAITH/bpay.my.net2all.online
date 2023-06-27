$('.widget-type-js').click(function (){
    let panelContainer = $('#panel-transaction-form');
    panelContainer.load(panelContainer.attr('data-url'),{type: $(this).attr('data-type')},function (xh,e,r){
        console.log(xh,e,r);
        $('#another-step-container').html('');

    });
});

$(document).ready(function(){
    let panelContainer = $('#panel-transaction-form');
    let type = $(panelContainer).attr('data-type');
    if(type.length < 0){
        return false;
    }
    panelContainer.load(panelContainer.attr('data-url'),{type: type},function (xh,e,r){
        $('#another-step-container').html('');
    });
})

$('body').on('click','#close-transfert-widget-js',function(){
    $('#panel-transaction-form').html('');
})
$('body').on('click','#transfer-account-to-account-btn-js',function (){
    event.preventDefault();
    $('.form-error').removeClass('form-error-active');
    if(!$('#AccountToAccountTransferForm').validate()){
        $('.form-error').addClass('form-error-active');
        return false;
    }
    let accountNumber = $('#cp_to_cp_accountNumber').val();
    let amount = $('#cp_to_cp_amount').val();
    if(accountNumber.length <= 0 || amount.length <= 0){
        $('.form-error').addClass('form-error-active');
        return false;
    }
    makeTransferAccountToAccount(accountNumber,amount);
});
function makeTransferAccountToAccount(accountNumber,amount){
    let retValue = false;
    $.ajax({
        url: $('#transfer-account-to-account-js').attr('data-transaction-url'),
        type: 'POST',
        async: false, // Mode synchrone
        data: ({
            accountNumber: accountNumber,
            step: 1,
            amount : amount
        }),
        dataType: "json",
        success: function(data){
            let step = data.step;
            let status = data.status;
            switch (status){
                case 'SUCCESS':
                    break;
                case 'ERROR':
                    break;
                default:
                    break;
            }
        },
        error:function (error){
            console.log(error)
        }
    });
    return retValue;
}
$('#transferBtn').click(function (){
    event.preventDefault();
    $('.form-error').removeClass('form-error-active');
    if(!$('#transferForm').validate()){
        $('.form-error').addClass('form-error-active');
        return false;
    }
    let accountNumber = $('#transfer_form_accountNumber').val();
    let amount = $('#transfer_form_amount').val();
    if(accountNumber.length <= 0 || amount.length <= 0){
        $('.form-error').addClass('form-error-active');
        return false;
    }
    $('#transfer-error').text('');
    checkIsMakeTransferToAccount(accountNumber,amount);
});
$('body').on('click','#transferConfirmBtn',function(){
    $('#process-control').html(`<div class="processing" style="  position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 999999999;
    margin: 0 auto;
    flex-direction: column;
    justify-content: center;
    align-items: center;">
                <div class="lds-spinner" style="
display: inline-block;
position: relative;
top:50%;
left: 50%;
transform: translate(-50%,-50%);
width: 80px;
height: 80px;
">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>`);

    let accountNumber = $('#transfer_form_accountNumber').val();
    let amount = $('#transfer_form_amount').val();
    let codeConfirmation = $('#codeConfirmation').val();
    let accountType = $('#accountType').val();
    let processControl = $('#process-control');
    $.ajax({
        url: $('#transferBtn').attr('data-transaction-url'),
        type: 'POST',
        async: false, // Mode synchrone
        data: ({
            accountNumber: accountNumber,
            step: 2,
            amount : amount,
            codeConfirmation : codeConfirmation,
            accountType : accountType
        }),
        dataType: "json",
        success: function(data){
            switch (data.status) {
                case 'SUCCESS':
                    processControl.html(data.widgetSuccess);
                    setTimeout(function(){
                        window.location.href = data.redirectUrl;
                    },2000)
                    break;
                default:
                    $('#process-control').html('');
                    break;
            }
        },
        error:function (error){
            $('#process-control').html('');
        }
    });
});
$('body').on('click','#backAction',function(){
    $('#another-step-container').html('');
    $('#step1').fadeIn();
})
function checkIsMakeTransferToAccount(accountNumber,amount){
    let retValue = false;
    $.ajax({
        url: $('#transferBtn').attr('data-transaction-url'),
        type: 'POST',
        async: false, // Mode synchrone
        data: ({
            accountNumber: accountNumber,
            step: 1,
            amount : amount
        }),
        dataType: "json",
        success: function(data){
            let step = data.step;
            let status = data.status;
            switch (status){
                case 'SUCCESS':
                    $('#another-step-container').html(data.widgetConfirm);
                    $('.steping').fadeOut();
                    break;
                case 'ERROR':
                    $('#transfer-error').text(data.errorMessage);
                    break;
                default:
                    break;
            }
        },
        error:function (error){
            console.log(error)
        }
    });
    return retValue;
}