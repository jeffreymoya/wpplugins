<?php

class MembershipsAddon extends MvcModel {

    var $belongs_to = array(
    	'Membership', 
    	'MembershipAddon' => array('foreign_key' => 'addon_id')
    );
    var $includes = array('MembershipAddon');
    
}

?>