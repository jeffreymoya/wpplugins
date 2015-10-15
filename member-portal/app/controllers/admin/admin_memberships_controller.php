<?php

class AdminMembershipsController extends MvcAdminController {
    
    var $default_columns = array(
    	'code', 
    	'description', 
    	'memberships_addons' => array('label'=>'addons', 'value_method'=>'admin_col_addons'), 
    	'fee'
    );

    public function add()
    {
        $this->set_addons();
        $this->create_or_save();
    }

    public function edit()
    {
        if(isset($this->params['data']))
        {
            global $wpdb;
            $id = $this->params['data']['Membership']['id'];
            $code = $this->params['data']['Membership']['code'];
            $description = $this->params['data']['Membership']['description'];
            $orig_code = $this->params['orig_code'];
            $orig_description = $this->params['orig_description'];

            if($code !== $orig_code || $description !== $orig_description)
            {
                remove_role($orig_code );
                add_role($code, $description, array('member_portal'));
            }
            
            $orig = explode("|", $this->params['orig_addons']);
            $addons = isset($this->params['addons']) ? $this->params['addons'] : [];
            $ins = array_diff($addons, $orig);
            $del = array_diff($orig, $addons);
            
            foreach ($del as $d)
            {
                $wpdb->delete( $wpdb->prefix.'memberships_addons', array( 'membership_id' => $id, 'addon_id' => $d ) );
            }

            foreach ($ins as $in) 
            {
                $wpdb->insert( 
                    $wpdb->prefix.'memberships_addons', 
                    array( 
                        'membership_id' => $id, 
                        'addon_id' => $in 
                    ), 
                    array( 
                        '%d', 
                        '%d' 
                    ) 
                );
            }
        }
        unset($this->params['data']['Membership']['membership_addon_id']);
        $this->set_addons();
        $this->verify_id_param();
        $this->set_object();
        $this->create_or_save();
    }

    public function admin_col_addons($object)
    {
    	$ret = '';
    	if(isset($object->memberships_addons))
    	{
    		$mas = $object->memberships_addons;
    		foreach ($mas as $ma) 
    		{
    			$ao = $ma->membership_addon;
    			$ret .= "<p>$ao->description</p>";
    		}
    	}
    	return $ret;
    }

    private function set_addons() {
        $this->load_model('MembershipAddon');
        $addons = $this->MembershipAddon->find(array('selects' => array('id', 'description')));
        $this->set('addons', $addons);
    }
    
}

?>