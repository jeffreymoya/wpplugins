<?php

class UserDetail extends MvcModel
{
    var $includes = array('PaymentHistory', 'Billing', 'Membership');

    public $belongs_to = array(
        'Membership' => array(
            'foreign_key' => 'membership_id',
        ),
        'Billing' => array(
            'foreign_key' => 'billing_id',
        ),
    );

    public $has_many = array(
        'PaymentHistory' => array(
            'foreign_key' => 'user_id',
        ),
    );

    var $validate = array(
        'firstname' => 'not_empty',
        'lastname' => 'not_empty',
        'postcode' => 'numeric',
        'phone' => 'numeric',
      );

    public function after_find($object)
    {
        global $wpdb;

        $userdata = wp_get_current_user();
        $object->user_email = $userdata->user_email;

        if($object->firstname != $userdata->user_firstname || $object->lastname != $userdata->user_lastname)
        {
            $wpdb->update( 
                $wpdb->prefix .'user_details', 
                array( 
                    'firstname' => $userdata->user_firstname,
                    'lastname' => $userdata->user_lastname
                ), 
                array( 'user_id' => $userdata->ID ),
                array( '%s', '%s' )
            ); 
        }
        $object->firstname = $userdata->user_firstname;
        $object->lastname = $userdata->user_lastname;
    }
}
