<?php

namespace App\Entity\BPayEntity\Stripe;

class StripePaymentIntent
{
    private $id;

    private $object;
    private $amount_capturable;
    private $amount_details;
    private $amount_received;
    private $application;
    private $application_fee_amount;
    private $automatic_payment_methods;
    private $canceled_at;
    private $cancellation_reason;
    private $capture_method;
    private $client_secret;
    private $confirmation_method;
    private $created;
    private $currency;
    private $customer;
    private $description;
    private $invoice;
    private $last_payment_error;
    private $latest_charge;
    private $livemode;

    private $metadata;
    private $next_action;
    private $on_behalf_of;
    private $payment_method;
    private $payment_method_options;

    private $processing;
    private $receipt_email;
    private $redaction;
    private $review;
    private $setup_future_usage;
    private $statement_descriptor;
    private $shipping;
    private $statement_descriptor_suffix;
    private $status;
    private $transfer_data;
    private $transfer_group;

    public function __construct()
    {
        $this->payment_method = new StripePaymentMethodCard();
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getAmountCapturable()
    {
        return $this->amount_capturable;
    }

    /**
     * @return mixed
     */
    public function getAmountDetails()
    {
        return $this->amount_details;
    }

    /**
     * @return mixed
     */
    public function getAmountReceived()
    {
        return $this->amount_received;
    }

    /**
     * @return mixed
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getApplicationFeeAmount()
    {
        return $this->application_fee_amount;
    }

    /**
     * @return mixed
     */
    public function getAutomaticPaymentMethods()
    {
        return $this->automatic_payment_methods;
    }

    /**
     * @return mixed
     */
    public function getCanceledAt()
    {
        return $this->canceled_at;
    }

    /**
     * @return mixed
     */
    public function getCancellationReason()
    {
        return $this->cancellation_reason;
    }

    /**
     * @return mixed
     */
    public function getCaptureMethod()
    {
        return $this->capture_method;
    }

    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * @return mixed
     */
    public function getConfirmationMethod()
    {
        return $this->confirmation_method;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @return mixed
     */
    public function getLastPaymentError()
    {
        return $this->last_payment_error;
    }

    /**
     * @return mixed
     */
    public function getLatestCharge()
    {
        return $this->latest_charge;
    }

    /**
     * @return mixed
     */
    public function getLivemode()
    {
        return $this->livemode;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return mixed
     */
    public function getNextAction()
    {
        return $this->next_action;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return mixed
     */
    public function getOnBehalfOf()
    {
        return $this->on_behalf_of;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethodOptions()
    {
        return $this->payment_method_options;
    }

    /**
     * @return mixed
     */
    public function getProcessing()
    {
        return $this->processing;
    }

    /**
     * @return mixed
     */
    public function getReceiptEmail()
    {
        return $this->receipt_email;
    }

    /**
     * @return mixed
     */
    public function getRedaction()
    {
        return $this->redaction;
    }

    /**
     * @return mixed
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @return mixed
     */
    public function getSetupFutureUsage()
    {
        return $this->setup_future_usage;
    }

    /**
     * @return mixed
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @return mixed
     */
    public function getStatementDescriptor()
    {
        return $this->statement_descriptor;
    }

    /**
     * @return mixed
     */
    public function getStatementDescriptorSuffix()
    {
        return $this->statement_descriptor_suffix;
    }

    /**
     * @return mixed
     */
    public function getTransferData()
    {
        return $this->transfer_data;
    }

    /**
     * @return mixed
     */
    public function getTransferGroup()
    {
        return $this->transfer_group;
    }

    /**
     * @param mixed $amount_capturable
     */
    public function setAmountCapturable($amount_capturable): void
    {
        $this->amount_capturable = $amount_capturable;
    }

    /**
     * @param mixed $amount_details
     */
    public function setAmountDetails($amount_details): void
    {
        $this->amount_details = $amount_details;
    }

    /**
     * @param mixed $amount_received
     */
    public function setAmountReceived($amount_received): void
    {
        $this->amount_received = $amount_received;
    }

    /**
     * @param mixed $application
     */
    public function setApplication($application): void
    {
        $this->application = $application;
    }

    /**
     * @param mixed $application_fee_amount
     */
    public function setApplicationFeeAmount($application_fee_amount): void
    {
        $this->application_fee_amount = $application_fee_amount;
    }

    /**
     * @param mixed $automatic_payment_methods
     */
    public function setAutomaticPaymentMethods($automatic_payment_methods): void
    {
        $this->automatic_payment_methods = $automatic_payment_methods;
    }

    /**
     * @param mixed $canceled_at
     */
    public function setCanceledAt($canceled_at): void
    {
        $this->canceled_at = $canceled_at;
    }

    /**
     * @param mixed $cancellation_reason
     */
    public function setCancellationReason($cancellation_reason): void
    {
        $this->cancellation_reason = $cancellation_reason;
    }

    /**
     * @param mixed $capture_method
     */
    public function setCaptureMethod($capture_method): void
    {
        $this->capture_method = $capture_method;
    }

    /**
     * @param mixed $client_secret
     */
    public function setClientSecret($client_secret): void
    {
        $this->client_secret = $client_secret;
    }

    /**
     * @param mixed $confirmation_method
     */
    public function setConfirmationMethod($confirmation_method): void
    {
        $this->confirmation_method = $confirmation_method;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created): void
    {
        $this->created = $created;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @param mixed $invoice
     */
    public function setInvoice($invoice): void
    {
        $this->invoice = $invoice;
    }

    /**
     * @param mixed $last_payment_error
     */
    public function setLastPaymentError($last_payment_error): void
    {
        $this->last_payment_error = $last_payment_error;
    }

    /**
     * @param mixed $latest_charge
     */
    public function setLatestCharge($latest_charge): void
    {
        $this->latest_charge = $latest_charge;
    }

    /**
     * @param mixed $livemode
     */
    public function setLivemode($livemode): void
    {
        $this->livemode = $livemode;
    }

    /**
     * @param mixed $metadata
     */
    public function setMetadata($metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @param mixed $next_action
     */
    public function setNextAction($next_action): void
    {
        $this->next_action = $next_action;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object): void
    {
        $this->object = $object;
    }

    /**
     * @param mixed $on_behalf_of
     */
    public function setOnBehalfOf($on_behalf_of): void
    {
        $this->on_behalf_of = $on_behalf_of;
    }

    /**
     * @param mixed $payment_method
     */
    public function setPaymentMethod($payment_method): void
    {
        $this->payment_method = $payment_method;
    }

    /**
     * @param mixed $payment_method_options
     */
    public function setPaymentMethodOptions($payment_method_options): void
    {
        $this->payment_method_options = $payment_method_options;
    }

    /**
     * @param mixed $processing
     */
    public function setProcessing($processing): void
    {
        $this->processing = $processing;
    }

    /**
     * @param mixed $receipt_email
     */
    public function setReceiptEmail($receipt_email): void
    {
        $this->receipt_email = $receipt_email;
    }

    /**
     * @param mixed $redaction
     */
    public function setRedaction($redaction): void
    {
        $this->redaction = $redaction;
    }

    /**
     * @param mixed $review
     */
    public function setReview($review): void
    {
        $this->review = $review;
    }

    /**
     * @param mixed $setup_future_usage
     */
    public function setSetupFutureUsage($setup_future_usage): void
    {
        $this->setup_future_usage = $setup_future_usage;
    }

    /**
     * @param mixed $shipping
     */
    public function setShipping($shipping): void
    {
        $this->shipping = $shipping;
    }

    /**
     * @param mixed $statement_descriptor
     */
    public function setStatementDescriptor($statement_descriptor): void
    {
        $this->statement_descriptor = $statement_descriptor;
    }

    /**
     * @param mixed $statement_descriptor_suffix
     */
    public function setStatementDescriptorSuffix($statement_descriptor_suffix): void
    {
        $this->statement_descriptor_suffix = $statement_descriptor_suffix;
    }

    /**
     * @param mixed $transfer_data
     */
    public function setTransferData($transfer_data): void
    {
        $this->transfer_data = $transfer_data;
    }

    /**
     * @param mixed $transfer_group
     */
    public function setTransferGroup($transfer_group): void
    {
        $this->transfer_group = $transfer_group;
    }
}
