<?php

class Billing extends MvcModel
{
	var $validate = array(
        'card_type' => 'not_empty',
        'card_name' => 'not_empty',
        'card_number' => 'not_empty',
        'ccv' => 'numeric',
      );
}
