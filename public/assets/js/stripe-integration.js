
let stripeIntegration = Stripe('{{ publicKey }}');
let elements = stripeIntegration.elements({
    clientSecret: '{{ clientSecret }}',
});
let card = null;

$(document).ready(function(){
    initialize();
    document.querySelector("#virement-form").addEventListener("submit", handleSubmit);
});

async function initialize() {
    let style = {
        base: {
            color: "#32325d",
        }
    };
    card = elements.create("card",{ style: style });
    card.mount("#link-authentication-element");
    card.on('change', ({error}) => {
        let displayError = document.getElementById('card-errors');
        if (error) {
            displayError.textContent = error.message;
        } else {
            displayError.textContent = '';
        }
    });

}

async function handleSubmit(e) {
    e.preventDefault();
    $('#process-control').html(` <div class="processing" style=" position: absolute;
    width: 100%;
    height: 100%;
    display:none !important;
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
    $('.processing').css('display','block');
    let displayError = document.getElementById('card-errors');
    displayError.textContent = '';
    displayError.classList.remove('alert-danger','alert');
    // If the client secret was rendered server-side as a data-secret attribute
    // on the <form> element, you can retrieve it here by calling `form.dataset.secret`
    stripeIntegration.confirmCardPayment('{{ clientSecret }}', {
        payment_method: {
            card: card,
            billing_details: {
                name: 'BPAY CUSTOMER'
            }
        }
    }).then(function(result) {
        if (result.error) {
            let displayError = document.getElementById('card-errors');
            displayError.textContent = result.error.message;
            displayError.classList.add('alert-danger', 'alert');
            $('.processing').css('display','none');
        } else {

            // The virement has been processed!
            if (result.paymentIntent.status === 'succeeded') {
                $('#recharge_form_transactionRef').val(result.paymentIntent.id);
                sellRecharge();
            }else{
                let displayError = document.getElementById('card-errors');
                displayError.textContent = 'Paiment echoué';
            }
        }
    });

    //setLoading(false);
}