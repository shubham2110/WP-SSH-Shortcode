<?php
/*
Plugin Name: Montoring Plugin for BASIS
Plugin URI: http://avihasta.com
Description: Shortcode for data center. Modules Required - ssh2 for php
Version: 1.0.0
Author: Shubham Saxena
Author URI: http://avihasta.com
*/


defined('ABSPATH') or die ('Access is not allowed for this Page. Please contact BASIS Team.');

class BasisAvihasta{
	
	public $plugin;
	

	function __construct(){
		//echo "PLIGIN WAS ACTIVATED";			
		/* Adding support to theme */
		/* RUN SHORTCODE IN EXCERPTS */
		$this->plugin = plugin_basename(__FILE__);
		add_filter( 'the_excerpt', 'shortcode_unautop');
		add_filter( 'the_excerpt', 'do_shortcode');
		add_filter( 'get_the_excerpt', 'do_shortcode', 5 );
		add_filter("show_admin_bar", "__return_false");
		add_theme_support('post-formats',array('status'));
		
		add_action('init', array($this,'wps_change_role_name'));
		
		// Shotcode Function
		add_shortcode('basis_runssh',array($this, 'basis_runssh_func') );
		
		add_shortcode('avihasta_basis',array($this, 'avihasta_basis_func') );
	
		$this->admin_page_init();
	
		add_action( 'wp_ajax_handle_ajax_request', array ($this,'handle_ajax_request') );
		add_action( 'wp_ajax_nopriv_handle_ajax_request', array($this,'handle_ajax_request') );	
		
		// Adding Ajax script in to the page
		add_action('wp_enqueue_scripts', array($this,'add_ajax_file'));
		
		// Calling Ajax script at the end for onload calls - call this at end
		add_action('wp_footer', array($this, 'add_this_script_footer') ); 

	}
	
	function wps_change_role_name() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();
		$wp_roles->roles['contributor']['name'] = 'TEAMXI';
		$wp_roles->role_names['contributor'] = 'TEAMXI';
	}

	
	function basis_ssh_exec($ip, $port, $user, $password, $cmd)
	{
		try {
			//return "$ip , $user , $password";
    			$connection = ssh2_connect($ip, $port);
    			if(!$connection)  throw new \Exception("Could not connect to $host on port $port");
    			$auth  = ssh2_auth_password($connection, $user, $password);
    			if(!$auth)  throw new \Exception("Could not authenticate with username $username and password ");  
			$stream = ssh2_exec($connection,$cmd);
	    		if (! $stream) throw new \Exception("Could not run this command or output is null");
			$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
   		 	stream_set_blocking($stream, true);
   		 	stream_set_blocking($errorstream, true);
			$contents = stream_get_contents($stream);
			$error="";	
			$error = stream_get_contents($errorStream);
    			@fclose($stream);
			@fclose($errorStream);
	    		$connection = NULL;
			//return $contents;
				
		} catch (Exception $e) {
	    		$error= $error . "Error due to :".$e->getMessage();
		
		}
	
		if(! $error)	return $contents ;
		else return $contents . $error ;
	}
	
	function avihasta_basis_func($atts, $content)
	{
		
		global $refresh_time;
		
		if(! $refresh_time)
			$refresh_time = 300000 ;
		
		$result="";
		
		$atts=shortcode_atts(
			array(
			    'uid' => 'ERP:Nothing',
			),$atts
		);
		
		extract($atts);
		
		//echo $uid;
		$id= get_the_ID();
		$this->add_this_script_footer(' <script > avihasta_load_ajax_v2( "'.$id.'" , "'.$uid.'" ); setInterval(function() { avihasta_load_ajax_v2( \''.$id.'\' , \''.$uid.'\' ); } , '.$refresh_time.') ;  </script> ');
		
		//$returntext = '<a href=# >aa </a>';
		$returntext= '<div class=row> ' ;
		$returntext = $returntext . '<a href="#" style="text-decoration: none;" id="ajax-response-'.$id.'"  onclick="avihasta_load_ajax_v2(\''."$id".'\' , \''."$uid".'\'  )"> ';
		$returntext = $returntext . 'Loading ... </a> </div>';
		return $returntext;
		

	}

	/*
	function avihasta_basis_func_old($atts, $content)
	{
		
		
		$result="";
		
		$atts=shortcode_atts(
			array(
			    'uid' => 'ERP:Nothing',
			),$atts
		);
		
		extract($atts);
		
		//echo $uid;
		$id= get_the_ID();
		$this->add_this_script_footer(" <script >  avihasta_load_ajax( $id ); </script> ");
		$returntext= "<div class='row'> " ;
		$returntext = $returntext . "<a href='#' style='text-decoration: none;' id='ajax-response-$id' onclick='avihasta_load_ajax(  $id  )'> ";
		$returntext = $returntext . "Loading ... $uid </a> </div>";
		return $returntext;
		

	}*/
	
	function basis_runssh_func($atts, $content)
	{
	//	if ( !isset($atts['ip']) ) $atts['ip']="default";
	//	if(empty($content)) $content="Testing";
		$result="";
		$atts=shortcode_atts(
			array(
			    'ip' => 'localhost',
			    'user' => 'root',
			    'password' => 'root123',
			    'cmd' => 'echo hostname',
			    'content' => !empty($content) ? $content : 'Testing content'
	
			),$atts
		);
		
		extract($atts);
		ob_flush();
		flush();
		//return $this->basis_ssh_exec($ip, 22, $user, $password, $cmd);

		// Below things are done for ajax calls	
		$id= get_the_ID();
		$this->add_this_script_footer(" <script > avihasta_load_ajax( $id ); </script> ");
		
		$returntext= "<div class='row'> " ;
		$returntext = $returntext . "<a href='#' style='text-decoration: none;' id='ajax-response-$id' onclick='avihasta_load_ajax(  $id  )'> ";
		$returntext = $returntext . "Loading ...  </a> </div>";
		return $returntext;
	
		
	}


	function admin_page_init(){
		
		require_once plugin_dir_path(__FILE__).'template/admin.php';
		$admin = new AvihastaBasisAdminPage();
		add_action('admin_menu', array($admin,'add_admin_pages'));	

			
		// setting link	
		add_filter("plugin_action_links_$this->plugin", array($this, 'setting_link'));	
	
	}
	
	function setting_link($links){
		// add custom setting link
		$settings_link = '<a href="options-general.php?page=avihasta_basis">Settings</a>';
		array_push($links, $settings_link);
		return $links;
		
	}

	/*
	function add_admin_pages(){
		
		add_menu_page('Portal Settings','BASIS PORTAL', 'manage_options' , 'avihasta_basis' , array($this, 'portal_setting_index') , '
dashicons-admin-tools' , null  );
		
	}

	function portal_setting_index(){
		//echo $this->plugin;
		require_once plugin_dir_path(__FILE__).'template/admin.php';
		require_once plugin_dir_path(__FILE__).'inc/avihasta-plugin-activate.php';
	}

	*/

	function add_this_script_footer($script){ 
		echo $script;
 	}
	
	function add_ajax_file()
	{
		wp_enqueue_script('avihasta-ajax',plugins_url('/js/ajax.js', __FILE__), array('jquery'), true) ;

		wp_localize_script( 'avihasta-ajax', 'avihasta_ajax_url', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		));
	}
	
	function handle_portal_request($uid)
	{
		global $wpdb;
		$myrows = $wpdb->get_results( "SELECT *  FROM wp_basis_portal_view" );
		//if(! $myrows) return;
		foreach($myrows as $row){
			$uid2= $row->UNIQUE_ID;
			if( trim(preg_replace('/\s+/',' ', $uid2)) == trim(preg_replace('/\s+/',' ', $uid)))
			{
				$ip=$row->IP;
				$user= $row->LOGINNAME;
				$pass = $row->LOGINPASS;
				$cmd = $row->COMMAND;
				$title = $row->TITLE;
				$thr = $row->THRESHOLD;
				$stype = $row->SHIFTLOGTYPE;
				$result =  $this->basis_ssh_exec($ip,22, $user, $pass, $cmd);
				if((is_numeric($stype) && (float)$stype != 0 ) || (is_numeric($thr)) && (float)$thr != 0 )
				{
					$str = preg_replace('/\D/', '', $result);
					//$float=(float)$val1;
					if( is_numeric($stype) && (float)$stype != 0 ) 
					{
					if((float)$str > (float)$stype) 
					{
						echo "<font color=red> $result </font>";
						return;

					}
					}
					if( is_numeric($thr) && (float)$thr != 0 )
					{
					if((float)$str > (float)$thr) 
					{
						echo "<font color=red> $result </font>";
						return;

					}
					}
				}
				
				echo "<font color=green>$result </font>";
			
			//	print_r  ($row);
			}
		}
		return ;
		

	}
	
	function handle_ajax_request()
	{
		$id = $_POST['id'];
		$uid= $_POST['uid'];
		//echo $id;
		//echo $uid;
		if($uid)
		   $this->handle_portal_request($uid);
		//sleep(1);	
	//	echo $id ;
	//	echo $_POST['var1'];
	//	echo "HELLO";
		wp_die();

	}
	
	function activate(){
		require_once plugin_dir_path(__FILE__).'inc/avihasta-plugin-activate.php';
		AvihastaPluginActivate::activate();
	}
	
 	function deactivate(){
		require_once plugin_dir_path(__FILE__).'inc/avihasta-plugin-deactivate.php';
		AvihastaPluginDeactivate::deactivate();
	}

}






if ( class_exists('BasisAvihasta')){

	$BA = new BasisAvihasta();
}
else
{
	die('Plugin Class is missing or tempered. Please Contact BASIS Team.');
}


// Activation
register_activation_hook(__FILE__, array($BA, 'activate'));


// Deactivation
register_deactivation_hook(__FILE__, array($BA, 'deactivate'));




?>
