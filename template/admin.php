<?php 


class AvihastaBasisAdminPage
{

	static function add_admin_pages(){
                
                add_menu_page('Portal Settings','BASIS PORTAL', 'manage_options' , 'avihasta_basis' , array($this, 'portal_setting_index') , 'dashicons-admin-tools' , null  );
               

	 
        }
	
	function getter($url){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER,0);
		$data=curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	static   function portal_setting_index(){
                echo "Hello";
		echo $this->getter("10.52.150.150/phpMyAdmin/sql.php?db=wordpress&table=wp_basis_portal_view&pos=0&pma_username=viewuser&pma_password=root123");
                //require_once plugin_dir_path(__FILE__).'template/admin.php';
        }



}
