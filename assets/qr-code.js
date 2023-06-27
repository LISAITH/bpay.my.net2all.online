// To use Html5QrcodeScanner (more info below)
import {Html5QrcodeScanner} from "html5-qrcode";

// To use Html5Qrcode (more info below)
import {Html5Qrcode} from "html5-qrcode";

var html5QrcodeScannerFinal = null;

function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
   $("#" + $('#renderQrCode').attr('data-el')).val(decodedText).trigger('change');

    $('#transfer-container').fadeIn();
    html5QrcodeScannerFinal.clear();
    $('#cancelSCanQrCode').fadeOut();
}
function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning.
    // for example:
    console.warn(`Code scan error = ${error}`);
}
$('body').on('click','#scanQrCodeId',function (){
    let html5QrcodeScanner = new Html5QrcodeScanner(
        "renderQrCode",
        {
            fps: 10,
            qrbox: {width: 250, height: 250},
            rememberLastUsedCamera: true,
            aspectRatio: 1.7777778,
            showTorchButtonIfSupported: true
        });
    html5QrcodeScannerFinal = html5QrcodeScanner;
    html5QrcodeScanner.render(onScanSuccess,onScanFailure);
    $('#transfer-container').fadeOut();
    $('#cancelSCanQrCode').fadeIn();
});
$('body').on('click','#cancelSCanQrCode',function (){
    html5QrcodeScannerFinal.clear();
    $('#transfer-container').fadeIn();
    $(this).fadeOut();
});