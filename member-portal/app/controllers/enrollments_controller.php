<?php

class EnrollmentsController extends MvcPublicController {
    
    public function enroll()
    {
    	$this->redirect_authenticated();

    	$this->save_details();
    	
    	$this->load_helper('form');
    	$this->load_model('UserDetail');
    	$defs = array(
    		'id' => null,
    		'membership_id' => null,
    		'billing_id' => null,
			'firstname' => null,
			'address1' => null,
			'suburb' => null,
			'lastname' => null,
			'address2' => null,
			'postcode' => null,
			'phone' => null
    	);

    	if(isset($this->params['data']))
    	{
    		$defs = array_merge($this->params['data'], $defs);
    	}

    	$new = $this->UserDetail->new_object($defs);

    	$this->set('object', $new);
        MvcObjectRegistry::add_object('UserDetail', $new);
    }

    public function payment()
    {
        global $wp;

        $request = explode("/",$wp->request);

        $action = home_url('/'.$request[0]);

        switch ($request[0]) {
            case 'apply':
                $this->register_payment($request[1]);
                $action .= '/'.$request[1];
                break;
            case 'renew':
                $this->renew_membership();
                break;
            case 'upgrade':
                $this->upgrade_membership();
                break;
            default:
                break;
        }

        $this->set('action', $action);
        $this->set('action_type', $request[0]);
    }

    private function renew_membership() 
    {
        $membership = $this->set_membership('associate');
        $result = $this->process_membership($membership, 'renew');

        if($result && $result['success'])
        {
            $this->post_renew($membership, $result);
        }
    }

    private function upgrade_membership() 
    {
        $membership = $this->set_membership('full');
        $result = $this->process_membership($membership, 'upgrade');
        
        if($result && $result['success'])
        {
            $this->post_upgrade($membership, $result);
        }
    }

    private function test_email($membership)
    {
        global $wpdb;

        $wpu = wp_get_current_user();
        $user = $wpdb->get_row("select * from {$wpdb->prefix}user_details where user_id=$wpu->id");
        $this->send_membership_email($user, $membership, current_time('mysql'));
        die('sent');
    }

    private function post_renew($membership, $result)
    {
        global $wpdb;

        $renewal_date = current_time('mysql');

        $wpdb->update(
            $wpdb->prefix . 'user_details',
            array('renewal_date' => $renewal_date),
            array('id' => $result['id'])
        );

        $this->post_process_membership('Renew Associate Membership', 'Membership Success', $membership, $result);

    }

    public function post_upgrade($membership, $result)
    {
        global $wpdb;

        $wpdb->update(
            $wpdb->prefix . 'user_details',
            array('membership_id' => $membership->id),
            array('id' => $result['id'])
        );

        $this->post_process_membership('Upgrade to Full Membership', 'Membership Success', $membership, $result);
    }

    private function post_process_membership($title, $email_template, $membership, $registration)
    {
        global $wpdb;

        $wpu = wp_get_current_user();

        $user = $wpdb->get_row("select * from {$wpdb->prefix}user_details where id={$registration['id']}");

        $fullname = ucwords("$user->firstname $user->lastname");

        $receipt = $this->generate_docs_and_payment_log($user, $membership, $title, $registration['trxid']);

        $amaclogin = home_url('/members/login?action=reset');

        $content = array(
            'membershipno' => '',
            'membersince' => date('Y-m-d', strtotime($user->registered_date)),
            'memberstatus' => 'Active',
            'nextrenewaldate' => date('Y-m-d', strtotime("+1 year")),
            'amacreset' => "<a href='$amaclogin'>here</a>",
        );

        $this->send_membership_email($email_template, $wpu->user_email, $membership, $title, $fullname, $receipt, $content);
    }

    private function send_membership_email($template_name, $email, $membership, $subject, $fullname, $receipt=null, $content=array())
    {
        global $wpdb;

        $addons = $wpdb->get_results(
            "select * from {$wpdb->prefix}membership_addons where id in (select addon_id from {$wpdb->prefix}memberships_addons where membership_id=$membership->id)"
        );
        $inclusions = '';
        foreach ($addons as $addon) 
        {
            $inclusions .= "<li>$addon->description</li>";
        }

        $template_content = array(
            'membername' => $fullname,
            'expirydate' => date('Y-m-d', strtotime("+1 year")),
            'name' => $fullname,
            'membertype' => $membership->description,
            'memberinclusions' => $inclusions,
        );
        

        $this->send_email($template_name, $subject, $email, $fullname, array_merge($template_content, $content), $receipt);

        
    }

    private function send_email($template_name, $subject, $to, $recipient_name, $content=array(), $pdf=null)
    {
        //FOR TESTING
        $amachome = 'http://hicalibertest.com.au/amac/'; //home_url();
        $amaclogo = $amachome.'/wp-content/themes/amac/assets/images/logo.png'; //home_url('/wp-content/themes/amac/assets/images/logo.png');

        $message = array(
            'subject' => $subject,
            'to' => array(
                array(
                    'email' => $to,
                    'name' => $recipient_name,
                    'type' => 'to'
                )
            ),
            'html' => array(
                array( 'name'=>'amachome', 'content'=> "<a href='$amachome'>www.amac.org.au</a>" ),
                array( 'name'=>'amaclogo', 'content'=> "<img style='width:100%;' src='$amaclogo'></img>" ),
            )
        );

        foreach ($content as $key => $value) 
        {
            $message['html'][] = array('name'=>$key, 'content'=>$value);
        }

        if(!empty($pdf))
        {
            $message['attachments'] =  DocHandler::pdf_upload_dir($pdf);
        }
        
        wpMandrill::sendEmail($message, null ,$template_name);
    }

    private function log_payment($id, $txid, $cardtype, $item, $amount, $invoice, $receipt)
    {
        global $wpdb;

        $wpdb->insert($wpdb->prefix . 'payment_histories',
            array(
                'user_id' => $id,
                'trx_id' => $txid,
                'card' => $cardtype,
                'item' => $item,
                'payment' => $amount,
                'invoice' => $invoice,
                'receipt' => $receipt
            )
        );
    }

    private function generate_docs_and_payment_log($user, $membership, $item_detail, $txid, $quantity=1, $tax='')
    {
        $params = [
            'username' => ucwords("$user->firstname $user->lastname"),
            'useraddr' => $user->address1.' '.$user->address2.' '.$user->suburb.' '.$user->state. ' '. $user->postcode,
            'itemname' => 'Item',
            'itemdetails' => is_array($item_detail) ? $item_detail : array($item_detail),
            'invoicedate' => date('Y-m-d'),
            'subtotal' => $membership->fee,
            'tax' => $tax,
            'total' => $membership->fee,
            'quantity' => $quantity,
            'rate' => $membership->fee,
            'amount' => $membership->fee,
        ];

        $receipt = DocHandler::generate_pdf('receipt', array_merge($params, [
            'formofpayment' => 'Credit Card',
            'paymrefno' => $txid,
            'paymentamount' => $membership->fee,
            'balance' => $membership->fee,

        ]));
        
        $invoice = DocHandler::generate_pdf('invoice', array_merge($params, [
            'invoiceno' => $txid,
            'amtdue' => $membership->fee,
            'duedate' => date('Y-m-d')

        ]));

        $this->log_payment(
                $user->id,
                $txid,
                $user->billing->card_type,
                is_array($item_detail) ? implode('<br/>', $item_detail) : $item_detail,
                $membership->fee,
                $invoice,
                $receipt
            );

        return $receipt;
    }

    private function process_membership($membership, $type) 
    {
        $this->redirect_non_member();

        if(isset($this->params[$type]))
        {
            include( plugin_dir_path ( __FILE__ ) . implode(DIRECTORY_SEPARATOR, array('..', 'ext', 'EWay', 'EWayPayment.php')) );
            $eway = new EWayPayment();

            $paym_desc = ucwords($type . ' ' . $membership->description);

            $resp = $eway->do_payment_current_user($membership, $paym_desc);

            $this->set('transaction', $resp);

            return $resp;
        }


        $this->set_billing_info();

        return false;
    }

    private function register_payment($hash)
    {
        ini_set('max_execution_time', 300);
        
        $this->redirect_authenticated();

        global $wpdb;
        
        $user = $wpdb->get_row(
            $wpdb->prepare("select id, email from {$wpdb->prefix}registrations where hash=%s and status=1", $hash)
        );

        if(empty($user))
        {
            die('Invalid parameter.');
        }

        $membership = $this->set_membership('associate');

        $this->process_new_member($user, $membership);

        $data = array(
            'card_type' => '',
            'card_number' => '',
            'card_name' => '',
            'expiry_month' => '',
            'expiry_year' => '',
            'ccv' => '',
        );

        if(isset($this->params['data'])) 
        {
            $data = array_merge($data, $this->params['data']);
        }

        $data['hash'] = $hash;

        $this->set('object', $data);
    }

    private function set_membership($code)
    {
        $this->load_model('Membership');
        $membership = $this->Membership->find_one(array('conditions' => array('code'=>$code)));
        $this->set('membership', $membership);

        return $membership;
    }

    private function set_billing_info()
    {
        $user = wp_get_current_user(); 
        $this->load_model('UserDetail');
        $user_detail = $this->UserDetail->find_one(array('conditions' => array('user_id'=>$user->ID)));
        $this->set('billing_info', $user_detail->billing);

        return $user_detail->billing;
    }

    private function process_new_member($registration, $membership)
    {
        if(!isset($this->params['data'])) return;

        $details = $this->params['data'];

    	$error = $this->validate_payment($details);

        if(!empty($error))
        {
            $this->flash('error', $error);
            return;
        }

        $billing_id = $this->insert_billing_info($details);

        if(!$billing_id)
        {
            $this->flash('error', 'Unable to process request. Please contact site administrator.');
            return;
        }

        $paym_resp = $this->do_payment_new_member($registration, $membership, $billing_id);

        if($paym_resp['EWAYTRXNSTATUS'] == 'False')
        {
            $this->flash('error', $paym_resp['message']);
            return;
        }

        error_log(print_r($paym_resp, true));

        $wpuser = $this->create_wp_user($details, $registration, $membership);

        if(isset($wpuser['error']))
        {
            $this->flash('error', $wpuser['error']);
            return;
        }

        $registered_date = current_time('mysql');

        $this->UserDetail->update($registration->id, array(
            'user_id'=>$wpuser['id'], 
            'membership_id'=>$membership->id,
            'billing_id'=>$billing_id,
            'registered_date'=>$registered_date
        ));

        $amaclogin = home_url('/members/login');

        $fullname = ucwords("{$wpuser['firstname']} {$wpuser['lastname']}");

        $membership_details = array(
            'membershipno' => '',
            'membersince' => date('Y-m-d', strtotime($registered_date)),
            'memberstatus' => 'Active',
            'nextrenewaldate' => date('Y-m-d', strtotime("+1 year")),
            'amacreset' => "<a href='$amaclogin'>here</a>",
        );

        $logindetails = array(
            'username' => $registration->email,
            'password' => $wpuser['password'],
            'amaclogin' => "<a href='$amaclogin'>here</a>",
        );
        
        $this->send_membership_email('Member Login Details', $registration->email, $membership, 'Welcome to AMAC!', $fullname, null, $logindetails);
        $this->send_membership_email('Registration Membership Success', $registration->email, $membership, 'AMAC Membership Details', $fullname, null, $membership_details);
        //$this->email_credentials($user->email, $wpuser['password']);

        $this->invalidate_registration_hash($registration->id);

        $url = MvcRouter::public_url(array('controller' => 'enrollments', 'action' => 'payment_success'));
        $this->redirect($url);
    }

    private function do_payment_new_member($registration, $membership, $billing_id)
    {
        global $wpdb;

        $user = $wpdb->get_row("select * from {$wpdb->prefix}user_details where id=$registration->id");
        $billing = $wpdb->get_row("select * from {$wpdb->prefix}billings where id=$billing_id");

        include( plugin_dir_path ( __FILE__ ) . implode(DIRECTORY_SEPARATOR, array('..', 'ext', 'EWay', 'EWayPayment.php')) );
        $eway = new EWayPayment();

        $paym_desc = ucwords('Registration ' . $membership->description);
        $address = "$user->address1 $user->address2 $user->suburb $user->state $user->postcode";

        $paym_resp = $eway->pay($membership->fee * 100, $user->firstname, $user->lastname, $registration->email, $address, $user->postcode, $paym_desc, $txid, 
            $billing->card_holder, $billing->card_number, $billing->card_exp_month, $billing->card_exp_year, $billing->cvn);

        if($paym_resp['EWAYTRXNSTATUS'] == 'True')
        {
            $this->set('transaction', $resp);

            $this->generate_docs_and_payment_log($user, $membership, 'Registration Associate Membership', $paym_resp['EWAYTRXNNUMBER']);
        }

        return $paym_resp;
    }

    private function invalidate_registration_hash($id)
    {
        global $wpdb;

        $res = $wpdb->update(
            $wpdb->prefix . 'registrations',
            array('status'=>0),
            array('id'=>$id),
            array('%d')
        );

        if($res === false)
        {
            error_log('Failed to update registrations table. hash='.$hash);
        }
    }

    private function create_wp_user($details, $user, $membership)
    {
        global $wpdb;

        $this->load_model('UserDetail');
                
        $random_password = wp_generate_password( $length=8, $include_standard_special_chars=false );
        $user_id = wp_create_user($user->email, $random_password, $user->email);

        if(is_wp_error($user_id))
        {
            return array('error'=>$user_id->get_error_message());
        }

        $ud = $wpdb->get_row("select firstname, lastname from {$wpdb->prefix}user_details where id=$user->id");
        $user_id_role = new WP_User($user_id);
        $user_id_role->set_role($membership->code);

        wp_update_user(array('ID'=>$user_id, 'first_name'=>$ud->firstname, 'last_name'=>$ud->lastname));

        return array('id'=>$user_id, 'password'=>$random_password, 'firstname'=> $ud->firstname, 'lastname'=> $ud->lastname);
    }

    private function insert_billing_info($details)
    {
        global $wpdb;

        $expiry = $details['expiry_year'].'-'.$details['expiry_month'].'-01';
        unset($details['expiry_month']);
        unset($details['expiry_year']);
        $details['expiry_date'] = $expiry;

        $wpdb->insert($wpdb->prefix.'billings', $details);

        return $wpdb->insert_id;
    }

    private function validate_payment($details)
    {
        $this->load_helper('Cc');
        $error = '';

        if(empty($details['card_type']))
        {
            $error .= '<span>Card type is required</span>';
        }
        if($details['card_type'] != $this->cc->validate_cc_number($details['card_number']))
        {
            $error .= '<span>Invalid card number</span>';
        }
        if(empty($details['card_name']))
        {
            $error .= '<span>Card name is required</span>';
        }
        if(empty($details['expiry_month']))
        {
            $error .= '<span>Expiry year is required</span>';
        }
        if(empty($details['expiry_year']))
        {
            $error .= '<span>Expiry month is required</span>';
        }
        if(empty($details['ccv']))
        {
            $error .= '<span>CCV is required</span>';
        }

        return $error;
    }

    public function payment_success(){}

    private function save_details()
    {
        ini_set('max_execution_time', 300);

        
        if(!isset($this->params['data'])) return;

        $this->load_model('UserDetail');
        $details = $this->params['data']['UserDetail'];
        $error = '';

        if(empty($details['firstname']))
        {
            $error .= '<span>First name is required</span>';
        }
        if(empty($details['lastname']))
        {
            $error .= '<span>Last name is required</span>';
        }

        $email = $details['user_email'];

        if(empty($email))
        {
            $error .= '<span>User email is required</span>';
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $error .= '<span>Invalid email</span>';
        }
        if(get_user_by( 'email', $email ))
        {
            $error .= '<span>Email already in use.</span>';
        }
        
        if(empty($error))
        {

            if(!$this->UserDetail->save($this->params['data']))
            {
                $this->flash('error', $this->UserDetail->validation_error_html);
            }
            else
            {
                global $wpdb;

	    		$id = $this->UserDetail->insert_id;
	    		$hash = wp_hash($email.$id);

	    		$ins = $wpdb->insert(
	    			$wpdb->prefix.'registrations',
	    			array('id'=>$id, 'email'=>$email, 'hash'=>$hash, 'status'=>1),
	    			array('%d', '%s', '%s')
	    		);

	    		if($ins)
	    		{
                    $fullname = ucwords("{$details['firstname']} {$details['lastname']}");
		    		$this->send_email(
                        'Registration Membership Approved', //template name
                        'Application for Associate Membership', 
                        $email, 
                        $fullname, 
                        array('membername'=>$fullname, 'application'=>'Associate Membership', 'registrationlink'=>'<a href="'.home_url('/apply/'.$hash).'">here</a>')
                    );

	    			$url = MvcRouter::public_url(array('controller' => 'enrollments', 'action' => 'register_success'));
	        		$this->redirect($url);
	    		}
	    		else 
                {
                    error_log(print_r($ins));
                    die('Unable to process application. Please contact the site administrator');
                }

	    	}
    	}
    	else
    	{
    		$this->flash('error', $error);
    	}
    }

    public function register_success(){}

    private function redirect_authenticated()
    {
    	if (is_user_logged_in())
        {
            $url = MvcRouter::public_url(array('controller' => 'members', 'action' => 'index'));
            $this->redirect($url);
        }
    }

    private function redirect_non_member()
    {
        if (!is_user_logged_in())
        {
            $url = MvcRouter::public_url(array('controller' => 'members', 'action' => 'login'));
            $this->redirect($url);
        }
    }
}

?>