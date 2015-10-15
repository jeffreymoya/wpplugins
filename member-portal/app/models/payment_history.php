<?php

class PaymentHistory extends MvcModel
{
    public $belongs_to = array(
        'UserDetail' => array(
            'foreign_key' => 'user_id',
        ),
    );

}
