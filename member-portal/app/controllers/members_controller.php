<?php

class MembersController extends MvcPublicController
{
    var $before = array('check_access');

    public function index()
    {
        $userdata = wp_get_current_user();
        $this->load_model('UserDetail');
        $details = $this->UserDetail->find_one(array(
            'conditions' => array('user_id' => $userdata->ID),
        ));

        if(empty($details))
        {
            global $wpdb;

            $this->load_model('Membership');

            $role = $userdata->roles[0];
            $membership = $this->Membership->find_one(array(
                'conditions' => array('type' => $role)
            ));

            $wpdb->insert( 
                $wpdb->prefix . 'user_details', 
                array( 
                    'user_id'=>$userdata->ID, 
                    'membership_id'=>$membership->id,
                    'firstname' => $userdata->user_firstname, 
                    'lastname' => $userdata->user_lastname
                ), 
                array( '%d', '%d', '%s', '%s' ) 
            );

            $details = $this->UserDetail->find_by_id($wpdb->insert_id);
            
            $this->load_model('Billing');
            $details->billing = $this->Billing->new_object(array(
                'id'=>null,
                'card_number' => '',
                'card_type' => '',
            ));
        }


        $this->load_helper('form');

        $this->set('object', $details);
        MvcObjectRegistry::add_object('UserDetail', $details);

    }

    public function login()
    {
        if(isset($this->params['email_reset']))
        {
            $this->doPasswordReset();
        }
        else if(is_user_logged_in())
        {
            wp_logout();
        }

        $args = array(
            'echo' => true,
            'redirect' => home_url('/members'),
            'form_id' => 'loginform',
            'label_username' => __('Username'),
            'label_password' => __('Password'),
            'label_remember' => __('Remember Me'),
            'label_log_in' => __('Log In'),
            'id_username' => 'user_login',
            'id_password' => 'user_pass',
            'id_remember' => 'rememberme',
            'id_submit' => 'wp-submit',
            'remember' => true,
            'value_username' => '',
            'value_remember' => false,
        );

        $this->set('form_opts', $args);
    }

    private function doPasswordReset()
    {
        $email = $this->params['email_reset'];

        $user = get_user_by('email', $email);

        if($user)
        {
            $login_url = home_url('/members/login');
            $random_password = wp_generate_password( $length=8, $include_standard_special_chars=false );
            wp_set_password($random_password, $user->ID);

            $msg = "<p>Your password have been reset successfully. Please login 
                    <a href='$login_url'>here</a> using the credentials below:</p>
                    <p><label>Username:</label>&nbsp;$email</p>
                    <p><label>Password:</label>&nbsp;$random_password</p>
                    <br/><p><strong>AMAC</strong></p>";

            wp_mail( $email, 'AMAC Password Reset', $msg);
            $this->set('success_reset', true);
        }
        else
        {
            $this->set('error', 'Email not found.');
        }
    }

    public function logout()
    {
        wp_logout();
        $url = MvcRouter::public_url(array('controller' => 'members', 'action' => 'login'));
        $this->redirect($url);
    }

    public function update_profile()
    {
        $userdata = wp_get_current_user();

        $this->load_model('UserDetail');

        $email = $this->params['data']['UserDetail']['user_email'];

        unset($this->params['data']['UserDetail']['user_email']);

        $this->params['data']['UserDetail']['user_id'] = $userdata->ID;

        if(!$this->UserDetail->save($this->params['data']))
        {
            die(json_encode(array('error'=>$this->UserDetail->validation_error_html)));
        }

        if(isset($email))
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            {
              die(json_encode(array('error'=>'Invalid email.')));
            }

            $fn = $this->params['data']['UserDetail']['firstname'];
            $ln = $this->params['data']['UserDetail']['lastname'];

            $data = array(
                'ID' => $userdata->ID,
                'user_email' => $email,
                'first_name' => $fn,
                'last_name' => $ln,
                'display_name' => "$ln $fn"
            );

            wp_update_user($data);

        }

        die(json_encode(array('success'=>true, 'msg'=>'Personal details have been successfully updated.')));
    }

    public function update_pwd()
    {
        $msg = '';
        $userdata = wp_get_current_user();
        $user = get_user_by('id', $userdata->ID);
        $ok = wp_check_password($this->params['current_pwd'], $user->user_pass, $user->ID);

        if($ok)
        {
            if(empty($this->params['new_pwd']))
            {
                $msg = 'New Password is required';
            }
            else if(empty($this->params['confirm_new_pwd']))
            {
                $msg = 'Confirm New Password is required';
            }
            else if($this->params['new_pwd'] !== $this->params['confirm_new_pwd'])
            {
                $msg = 'Passwords do not match';
            }
            else
            {
                wp_set_password( $this->params['new_pwd'], $userdata->ID );
            }
        }
        else
        {
            $msg = 'Invalid current password!';
        }

        $resp['success'] = empty($msg);
        $resp['msg'] = 'Password have been successfully updated.';
        $resp['error'] = $msg;

        die(json_encode($resp));
    }

    public function update_settings()
    {
        $this->load_model('UserDetail');
        $this->params['data']['UserDetail']['notify_renew_membership'] = empty($this->params['data']['UserDetail']['notify_renew_membership']) ? 0 : 1;
        $this->params['data']['UserDetail']['notify_new_courses'] = empty($this->params['data']['UserDetail']['notify_new_courses']) ? 0 : 1;
        $this->params['data']['UserDetail']['searchable'] = empty($this->params['data']['UserDetail']['searchable']) ? 0 : 1;
        if(!$this->UserDetail->save($this->params['data']))
        {
            die(json_encode(array('error'=>$this->UserDetail->validation_error_html)));
        }

        die(json_encode(array('success'=>true, 'msg'=>'Settings have been successfully updated.')));
    }

    public function update_cc()
    {
        $this->load_model('Billing');
        $this->load_helper('Cc');

        $cctype = $this->params['data']['Billing']['card_type'];

        if(empty($cctype))
        {
            die(json_encode(array('error'=>'Card type is required.')));
        }

        $ccno = $this->params['data']['Billing']['card_number'];

        if($cctype != $this->cc->validate_cc_number($ccno))
        {
            die(json_encode(array('error'=>'Invalid credit card number.')));
        }

        $ccname = $this->params['data']['Billing']['card_name'];
        if(empty($ccname))
        {
            die(json_encode(array('error'=>'Card name is required.')));
        }

        $ccmo = $this->params['expiry_month'];
        if(empty($ccmo))
        {
            die(json_encode(array('error'=>'Expiry month is required.')));
        }

        $ccyear = $this->params['expiry_year'];
        if(empty($ccyear))
        {
            die(json_encode(array('error'=>'Expiry year is required.')));
        }

        $ccv = $this->params['data']['Billing']['ccv'];
        if(empty($ccv) || !is_numeric($ccv))
        {
            die(json_encode(array('error'=>'Invalid CCV.')));
        }
        
        $expiry = "{$ccyear}-{$ccmo}-01";

        $this->params['data']['Billing']['expiry_date'] = $expiry;

        if(!$this->Billing->save($this->params['data']))
        {
            die(json_encode(array('error'=>$this->Billing->validation_error_html)));
        }

        $id = $this->Billing->insert_id;

        global $wpdb;

        $wpdb->update( 
            $wpdb->prefix.'user_details', 
            array( 
                'billing_id' => $id,
            ), 
            array( 'id' => $this->params['udid'] ),
            array( '%d' )
        ); 

        $ccno = substr($ccno, 0, 4) . str_repeat("X", strlen($ccno) - 8) . substr($ccno, -4);

        $data = ['card_type'=>strtoupper($cctype), 'card_number'=>$ccno, 'exp_date'=>date('F Y', strtotime(date($expiry)))];

        die(json_encode(array('success'=>true, 'data'=>$data, 'msg'=>'Billing details have been successfully updated.')));

    }

    public function update_auto_renew()
    {
        global $wpdb;

        $userdata = wp_get_current_user();
        $ar = $this->params['auto_renew'];

        $wpdb->update(
            $wpdb->prefix.'user_details', 
            array('auto_renew_membership' => $ar), 
            array('user_id' => $userdata->ID),
            array('%d')
        );

        die(json_encode(array('success'=>true, 'msg'=>'Auto renew has been '.($ar == '1' ? 'enabled' : 'disabled'))));
    }


    public function check_access()
    {
        if (!strpos($this->current_url(), 'login') && !is_user_logged_in())
        {
            $url = MvcRouter::public_url(array('controller' => 'members', 'action' => 'login'));
            $this->redirect($url);
        }
    }
}

