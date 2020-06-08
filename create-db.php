<?php

/**
 * Class is for creating a database and inserting the data into it.
 */
class CreateDB
{
    /**
     * Create the required database, unless it already exists.
     */
    public function installDatabase()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'bookings';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name varchar(200) NOT NULL,
		email varchar(500) NOT NULL,
		phone varchar (500) NOT NULL,
		status varchar (500) NOT NULL,
		start_date datetime NOT NULL,
		end_date datetime NOT NULL,
		price mediumint(9) NOT NULL,
		created_at timestamp NULL,
		PRIMARY KEY  (id)
	    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        maybe_create_table($table_name, $sql);
    }
}