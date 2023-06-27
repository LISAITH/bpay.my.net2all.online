$('#recharge_montant').change(function(){
    triggerPaymentForm()
});
$('.tab-pane-js').click(function(){
    let dataId = $(this).attr('data-id');
    $('#' +  dataId).addClass('active').siblings('div.tab-pane').removeClass('active');
})
function triggerPaymentForm(amount){
    setTimeout(function (){
        let amount = $('#recharge_montant').val();
        if(isNaN(parseInt(amount))){
            amount = 0;
        }
        if(amount > 0 ){
            reloadPaymentForm();
        }else{clearPaymentForm()}
    },1000);
}
function clearPaymentForm(){
    $('#virement-container-js').html('');
}
function reloadPaymentForm(){

    let currentProvider = $('#recharge_paymentMethodType').val();
    let amount = $('#recharge_montant').val();
    let payData = {
        amount: amount,
        providerCode:currentProvider
    }
    $('#virement-container-js').load($('#reload-form-url-js').attr('data-url'),{payData:JSON.stringify(payData)},function (responseText, textStatus, XMLHttpRequest){
        console.log(responseText,textStatus,XMLHttpRequest);
    });
}
$('body').on('click','#paymentBtnButtonMomo',function (){
    event.preventDefault();
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
    if(!$('#paymentBtnButtonMomoForm').validate()){
        return false;
    }
    $('.processing').css('display','block');
    $(this).closest('form').ajaxSubmit({
        error: function(data) {  console.log(data); },
        success: function(data) {
            console.log(data.payload);
            let payload = data.payload.requestId;
            let status = data.payload.status;
            switch (status){
                case 'SUCCESSFUL':
                    $('#recharge_transactionRef').val(payload);
                    sell();
                    break;
                default:
                    $('#process-control').html(data.widgetFail);
                   /* setTimeout(function(){
                        $('#process-control').html('');
                    },2000)*/
                    break;
            }
        },
    });
});

$('.bpa-payment-item').click(function(){
    $(this).addClass('bpay-payment-item-active').siblings('div.bpa-payment-item').removeClass('bpay-payment-item-active');
    $('#recharge_paymentMethodType').val($(this).attr('data-provider-code'));
    let amount = $('#recharge_montant').val();
    triggerPaymentForm(amount);

})
function sell(){
    $('form[name=\'recharge\']').ajaxSubmit({
        error: function(data) {  console.log(data); },
        success: function(data) {
            console.log(data);
            if(data.status === 'success'){
                $('#container-v2').html(data.widget);
                setTimeout(function(){
                    window.location.href = data.redirectUrl;
                },2000)
            }
        }
    });
}
$('body').on('change','#isMyAccountJs',function (){
    if($(this).prop('checked')){
        $('#momo_pay_form_holderName_container').css('display','none');
        $('#momo_pay_form_isYourAccount').val('true');
    }else{ $('#momo_pay_form_holderName_container').css('display','block');
        $('#momo_pay_form_isYourAccount').val('false');
        $('#momo_pay_form_holderName').attr('required',true);}
})