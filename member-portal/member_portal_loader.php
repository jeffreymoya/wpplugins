<?php

class MemberPortalLoader extends MvcPluginLoader
{
    public $db_version = '1.0';
    public $tables = array();

    public function activate()
    {

        // This call needs to be made to activate this app within WP MVC

        $this->activate_app(__FILE__);

        // Perform any databases modifications related to plugin activation here, if necessary

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        add_option('member_portal_db_version', $this->db_version);

        //add roles
        add_role('associate', 'Associate Member', array('edit_user_profile'));
        add_role('full', 'Full Member', array('edit_user_profile'));
        add_role('student', 'Student Member', array('edit_user_profile'));
        add_role('overseas', 'Overseas Member', array('edit_user_profile'));
        add_role('honorary', 'Honorary Fellow', array('edit_user_profile'));
        add_role('fellow', 'Fellow', array('edit_user_profile'));

        global $wpdb;

        $sql = '
            CREATE TABLE ' . $wpdb->prefix . 'user_details (
              id int(11) NOT NULL auto_increment,
              user_id int(11) NOT NULL,
              membership_id int(11) NOT NULL,
              billing_id int(11) NOT NULL,
              firstname varchar(255) NOT NULL,
              lastname varchar(255) NOT NULL,
              address1 varchar(255) default NULL,
              address2 varchar(255) default NULL,
              suburb varchar(100) default NULL,
              state varchar(5) default NULL,
              postcode varchar(20) default NULL,
              phone varchar(15) default NULL,
              registered_date datetime NOT NULL,
              renewal_date datetime NULL,
              searchable tinyint(1) default 1,
              notify_renew_membership tinyint(1) default 1,
              notify_new_courses tinyint(1) default 1,
              auto_renew_membership tinyint(1) default 1,
              PRIMARY KEY  (id)
            )';
        dbDelta($sql);

        $sql = '
            CREATE TABLE ' . $wpdb->prefix . 'memberships (
              id int(11) NOT NULL auto_increment,
              code varchar(255) NOT NULL,
              description varchar(255) NOT NULL,
              fee varchar(50) NOT NULL,
              PRIMARY KEY  (id)
            )';
        dbDelta($sql);

        $sql = '
            CREATE TABLE ' . $wpdb->prefix . 'memberships_addons (
              id int(11) NOT NULL auto_increment,
              membership_id int(11) NOT NULL,
              addon_id int(11) NOT NULL,
              PRIMARY KEY  (id)
            )';
        dbDelta($sql);

        $sql = '
            CREATE TABLE ' . $wpdb->prefix . 'membership_addons (
              id int(11) NOT NULL auto_increment,
              description varchar(255) NOT NULL,
              PRIMARY KEY  (id)
            )';
        dbDelta($sql);

        $sql = '
            CREATE TABLE ' . $wpdb->prefix . 'billings (
              id int(11) NOT NULL auto_increment,
              card_type varchar(50) NOT NULL,
              card_name varchar(255) NOT NULL,
              card_number varchar(50) NOT NULL,
              expiry_date date NOT NULL,
              ccv int(3) NOT NULL,
              PRIMARY KEY  (id)
            )';
        dbDelta($sql);

        $sql = '
            CREATE TABLE ' . $wpdb->prefix . 'payment_histories (
              id int(11) NOT NULL auto_increment,
              user_id int(11) NOT NULL,
              trx_id int(11) NOT NULL,
              date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              item varchar(255) NOT NULL,
              card varchar(255) NOT NULL,
              charge varchar(255) NOT NULL,
              payment varchar(255) NOT NULL,
              balance varchar(255) NOT NULL,
              invoice varchar(255) NOT NULL,
              receipt varchar(255) NOT NULL,
              PRIMARY KEY  (id)
            )';
        dbDelta($sql);

        $sql = '
            CREATE TABLE ' . $wpdb->prefix . 'registrations (
              id int(11) NOT NULL,
              email varchar(255) NOT NULL,
              hash varchar(255) NOT NULL,
              status tinyint(1) NOT NULL,
              reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )';
        dbDelta($sql);
    }

    public function deactivate()
    {

        // This call needs to be made to deactivate this app within WP MVC

        $this->deactivate_app(__FILE__);

        // Perform any databases modifications related to plugin deactivation here, if necessary

        /* Set up an array of roles to delete. */
        $roles_to_delete = array(
            'associate',
            'full',
            'student',
            'overseas',
            'honorary',
            'fellow',
        );

        /* Loop through each role, deleting the role if necessary. */

        foreach ($roles_to_delete as $role)
        {
            /* Get the users of the role. */
            $users = get_users(array('role' => $role));
            /* Check if there are no users for the role. */

            if (count($users) <= 0)
            {
                /* Remove the role from the site. */
                remove_role($role);
            }
        }
    }
}
