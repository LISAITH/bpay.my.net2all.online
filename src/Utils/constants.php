<?php

namespace App\Utils;

class constants
{
    public const USER_TYPE_SYS_NAME_PARTICULIER = 'PARTICULAR';
    public const USER_TYPE_SYS_NAME_DISTRIBUTEUR = 'DISTRIBUTER';
    public const USER_TYPE_SYS_SYS_NAME_PARTENAIRE = 'PARTNER';
    public const USER_TYPE_SYS_NAME_ADMIN = 'ADMIN';
    public const USER_TYPE_SYS_NAME_VENDOR = 'VENDOR';
    public const USER_TYPE_SYS_NAME_ENTREPRISE = 'ENTERPRISE';
    public const API_MOMO_PARAM_GROUP = 'API-MOMO-INTEGRATION';
    public const MOOV_API_INTEGRATION = 'MOOV-API-INTEGRATION';
    public const API_MOMO_PARAMETER_LIVE = 'API-PARAMETER-LIVE';
    public const API_MOMO_PARAMETER_SANDBOX = 'API-PARAMETER-SANDBOX';

    public const PRIMARY_KEY = 'PRIMARY-KEY';
    public const SECONDARY_KEY = 'SECONDARY-KEY';
    public const X_REFERENCE_ID = 'X-REFERENCE-ID';
    public const OCI_APIM_SUBSCRIPTION_KEY = 'OCI-APIM-SUBSCRIPTION-KEY';
    public const API_ID = 'API-ID';
    public const API_USER = 'API-USER';
    public const HOST = 'HOST';
    public const PROVIDER_CALLBACK = 'PROVIDER-CALLBACK';
    public const ACCESS_TOKEN = 'ACCESS-TOKEN';
    public const TOKEN_TYPE = 'TOKEN-TYPE';
    public const TOKEN_EXPIRE_IN = 'TOKEN-EXPIRE-IN';
    public const X_TARGET_ENVIRONEMENT = 'X-TARGET-ENVIRONMENT';
    public const AUTHORIZATION_BASIC = 'Basic';
    public const AUTHORIZATION_BEARER = 'Bearer';
    public const CALLBACK_PROVIDER = 'PROVIDER-CALLBACK';
    public const API_MOMO_HOST = 'API-HOST';
    public const API_KEY = 'API-KEY';
    public const X_CALLBACK_URL = 'X-CALLBACK-URL';
    public const REQUEST_TO_PAY_URI = 'requesttopay';
    public const REQUEST_TO_PAY_TRANSACTION_STATUS = 'requesttopay/';
    public const REQUEST_TO_PAY_TRANSACTION = 'requesttransstatus';
    public const MOMO_PROVIDER_CODE = 'MOMO';
    public const MOOV_PROVIDER_CODE = 'MOOV';

    public const BPAY_PROVIDER_CODE = 'BPAY';

    public const PAYEE_NOT_FOUND = 'PAYEE_NOT_FOUND';

    public const PAYER_NOT_FOUND = 'PAYER_NOT_FOUND';

    public const NOT_ALLOWED = 'NOT_ALLOWED';

    public const NOT_ALLOWED_TARGET_ENVIRONMENT = 'NOT_ALLOWED_TARGET_ENVIRONMENT';

    public const INVALID_CALLBACK_URL_HOST = 'INVALID_CALLBACK_URL_HOST';

    public const INVALID_CURRENCY = 'INVALID_CURRENCY';
    public const STATUS_200 = '200';
    public const STATUS_202 = '202';
    public const STATUS_500 = '500';
    public const STATUS_400 = '400';
    public const STATUS_409 = '409';
    public const ACCEPTED = 'ACCEPTED';
    public const SUCCESSFUL = 'SUCCESSFUL';
    public const FAILED = 'FAILED';
    public const FLOOZ_PROVIDER_CODE = 'FLOOZ';
    public const PAYPAL_PROVIDER_CODE = 'PAYPAL';
    public const STRIPE_PROVIDER_CODE = 'STRIPE';
    public const RECHARGE_BPAY_ACCOUNT = 'RECHARGEMENT COMPTE BPAY';
    public const EUR_CURRENCY = 'EUR';
    public const XOF_CURRENCY = 'XOF';
    public const CREDIT = 'CREDIT';
    public const DEBIT = 'DEBIT';

    public const VALIDATED = 'VALIDATED';

    public const REGLEMENT_BPAY = 'REGLEMENT_BPAY';
    public const VIREMENT_BPAY_BANK = 'VIREMENT_BPAY_BANK';
    public const VIREMENT_INTER_BANK = 'VIREMENT_INTER_BANK';
    public const RECHARGEMENT_BPAY = 'RECHARGEMENT_BPAY';
    public const TRANSFERT_BPAY = 'TRANSFERT_BPAY';
    public const PENDING = 'PENDING';
    public const STRIPE_INTEGRATION = 'STRIPE-INTEGRATION';
    public const STRIPE_SANDBOX_PARAMETER = 'STRIPE-SANDBOX-PARAMETER';
    public const STRIPE_PUBLIC_KEY = 'STRIPE-PUBLIC-KEY';
    public const STRIPE_SECRET_KEY = 'STRIPE-SECRET-KEY';
    public const STRIPE_LIVE_PARAMETER = 'STRIPE-LIVE-PARAMETER';
    public const CELTTIS_PROVIDER_CODE = 'CELTIIS';
    public const ENTERPRISE = 'ENTERPRICE';
    public const PARTICULAR = 'PARTICULAR';
    public const CARDETYPE = 'CARD';
    public const EUR = 'EUR';
    public const XOF = 'XOF';
    public const FCFA = 'FCFA';
    public const USD_CURRENCY = 'USD';
    public const PRINCIPAL_ACC = 'PRINCIPAL_ACCOUNT';
    public const SOUS_TYPE_ACC = 'SOUS_TYPE_ACCOUNT';
    public const ERROR = 'ERROR';
    public const SUCCESS = 'SUCCESS';
    public const TRANSFER_BPAY_IMG = 'bpay.png';
    public const DEFAULT_IMG_URL = 'default-user.png';
    public const CP_TO_CP = 'CP_TO_CP';
    public const CP_TO_SC = 'CP_TO_SC';
    public const SC_TO_CP = 'SC_TO_CP';
    public const SC_TO_SC = 'SC_TO_SC';
    public const PCP_TO_SC = 'PAIEMENT_BPAY';
    public const FRAIS_REGLEMENT = 'FRAIS_REGLEMENT';
    public const FRAIS_BPAY_VERS_SOUS_COMPTE = 'FRAIS_BPAY_VERS_SOUS_COMPTE';
    public const FRAIS_INTER_BPAY = 'FRAIS_INTER_BPAY';
    public const FRAIS_BPAY_BANK = 'FRAIS_BPAY_BANK';
    public const FRAIS_INTER_BANK = 'FRAIS_INTER_BANK';
    public const REJECTED = 'REJECTED';
    public const TIMEOUT = 'TIMEOUT';
    public const ONGOING = 'ONGOING';
    public const REGLEMENT =' REGLEMENT B-PAY';
    public const BPAY_PARAMETER = 'BPAY-PARAMETER';
    public const WHO_SUPPORT_TAXE = 'WHO-SUPPORT-TAXE';
    public const SENDER = 'SENDER';
    public const  RECEIVER = 'RECEIVER';

    public const PAIEMENT_MOBILE = 'PAIEMENT_MOBILE';
    public const PAIEMENT_STRIPE = 'PAIEMENT_STRIPE';

}
