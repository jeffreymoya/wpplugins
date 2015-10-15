<?php

class Membership extends MvcModel {

    var $display_field = 'type';

    var $has_many = array('MembershipsAddon');

    var $includes = array('MembershipsAddon');

    public function after_save($object)
    {
    	$role = get_role($object->code);
    	if(empty($role))
    	{
    		add_role($object->code, $object->description, array('member_portal'));
    	}
    }

    public function before_delete($object)
    {
    	remove_role($object->code);
    }
    
}

?>