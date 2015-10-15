<?php

class AdminMembershipAddonsController extends MvcAdminController {
    
    var $default_columns = array('description');

    public function edit()
    {
    	$this->create_or_save();
    }
    
}

?>