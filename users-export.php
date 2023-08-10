<?php
/*
Plugin Name: Users Export
Author: Nazrul Hassan
Description: Export Users
version:1.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function export_member(){

 add_menu_page('Export Member Title', 'Member Member ', 'manage_options', 'export-member', 'export_member_callback','dashicons-awards');
					  

}
add_action('admin_menu', 'export_member');


function export_member_callback(){

?>
		<form action="<?php echo admin_url(); ?>/admin-post.php" method="post">
			  <input type="hidden" name="action" value="export_member_csv1">
		<hr>
			 <input type="submit" value="Export Users" class="button button-primary button-large">
		</form>  
<?php

}

add_action( 'admin_post_export_member_csv1', 'export_member_csv_callback' );
function export_member_csv_callback() {


	global $table_prefix;
	$user_roles_option_name = $table_prefix . 'user_roles';
	$user_roles_data        = get_option($user_roles_option_name);
	$user_roles             = maybe_unserialize($user_roles_data);

	foreach($user_roles as $role){
		$rolesarray[] = $role['name'];
	}

	if ( ! current_user_can( 'manage_options' ) )
        return;
    $csv_filename = 'member_'.date('D-M-d-Y-H:i:s-A-e',time()).'.csv' ;
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename='.$csv_filename);
    header('Pragma: no-cache');
    header('Expires: 0');
    $file = fopen('php://output', 'w');
    // send the column headers


    $csvheaders = array(
    	'S.No',
    	'User ID',
    	'Email',
    	'First Name',
    	'Last Name',
    );

     fputcsv($file, $csvheaders ,',');

 		$args = array(
				'role__in'   => $rolesarray ,
				
			);

    $user_query = new WP_User_Query($args );
	$users 		= $user_query->get_results();
		$i=1;
	foreach ( $users as $user ) {
		

		$email   					= $user->data->user_email ;
		$fname   					= get_user_meta($user->id,'first_name',true); 
		$lname	 					= get_user_meta($user->id,'last_name',true);
	
    $csvcolumns = array(
    		$i,
			$user->data->ID, 
			$email,
			$fname,
			$lname
			
		) ;


    fputcsv($file,$csvcolumns);


    $i++;
	}
	exit();
}