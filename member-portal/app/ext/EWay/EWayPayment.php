<?php

include("EwayPaymentLive.php");

class EWayPayment
{
	public function renew_current_user($membership)
	{
		return $this->do_payment_current_user($membership, "Renew $membership->description");
	}

	public function upgrade_current_user($membership)
	{
		return $this->do_payment_current_user($membership, "Upgrade to $membership->description");
	}

	public function do_payment_current_user($membership, $description)
	{
		global $wpdb;

		$wpu = wp_get_current_user();
		$user = $wpdb->get_row("select * from {$wpdb->prefix}user_details where user_id=$wpu->ID");
		$billing = $wpdb->get_row("select * from {$wpdb->prefix}billings where id=$user->billing_id");

		$address = "$user->address1 $user->address2 $user->suburb $user->state $user->postcode";

		$expdate = explode('-', date('j-Y', strtotime($billing->expiry_date)));

		$response = $this->pay($membership->fee * 100, $user->firstname, $user->lastname, $wpu->user_email, $address, $user->postcode, 
					$description, time(), $billing->card_name, $billing->card_number, $expdate[0], $expdate[1], $billing->ccv);

		return array(
			'id' => $user->id,
			'success' => $response['EWAYTRXNSTATUS'] === 'True' ? true : false,
			'message' => $response['EWAYTRXNERROR'],
			'trxid' => $response['EWAYTRXNNUMBER'],
			'description' => $description,
			'email' => $wpu->user_email,
			'amount' => $membership->fee,
		);
	}

	public function pay($amount_cents, $firstname, $lastname, $email, $address, $postcode, $invoice_desc, $invoice_ref, 
		$card_holder, $card_number, $card_exp_month, $card_exp_year, $cvn)
	{
	    $this->eway->setTransactionData("TotalAmount", $amount_cents);
	    $this->eway->setTransactionData("CustomerFirstName", $firstname);
	    $this->eway->setTransactionData("CustomerLastName", $lastname);
	    $this->eway->setTransactionData("CustomerEmail", $email);
	    $this->eway->setTransactionData("CustomerAddress", $address);
	    $this->eway->setTransactionData("CustomerPostcode", $postcode);
	    $this->eway->setTransactionData("CustomerInvoiceDescription", $invoice_desc);
	    $this->eway->setTransactionData("CustomerInvoiceRef", $invoice_ref);
	    $this->eway->setTransactionData("CardHoldersName", $card_holder);
	    $this->eway->setTransactionData("CardNumber", $card_number);
	    $this->eway->setTransactionData("CardExpiryMonth", $card_exp_month);
	    $this->eway->setTransactionData("CardExpiryYear", $card_exp_year);
	    $this->eway->setTransactionData("CVN", $cvn);
	    $this->eway->setTransactionData("TrxnNumber", "");
	    $this->eway->setTransactionData("Option1", "");
	    $this->eway->setTransactionData("Option2", "");
	    $this->eway->setTransactionData("Option3", "");

	    return $this->eway->doPayment();
	}

	public function __construct()
	{
		$settings = get_email_general_settings("payment_gateway_general_settings");
	    $sandbox = ($settings['eWay_sandbox'] == "true" ? false : true);
	    $this->eway = new EwayPaymentLive($settings['eWay_Customer_ID'], REAL_TIME_CVN, $sandbox);
	}
}