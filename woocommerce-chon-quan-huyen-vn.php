<?php 
/*
 * @wordpress-plugin
 * Plugin Name:       Woocommerce - Quan/Huyen - VietNam
 * Plugin URI:        http://vuducnam.com/
 * Description:       
 * Version:           1.0
 * Author:            NamVD
 * Author URI:        http://vuducnam.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nam
 * Domain Path:       /languages
 */
defined( 'ABSPATH' ) OR exit;


final class Woocommerce_State_VietNam
{

	/**
	* @var The single instance of the class
	* @author Comfythemes
	* @since 1.0
	*/
	protected static $_instance = null;

	/**
	* Main Plugin Instance
	*
	* Ensures only one instance of Plugin is loaded or can be loaded.
	*
	* @author Comfythemes
	* @since 1.0
	* @static
	* @return Main instance
	*/
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	* Plugin Constructor.
	*
	* @author Comfythemes
	* @since 1.0
	*/
	public function __construct() {
		
		$this->define_constants();
		$this->init_hooks();

	}

	/**
	* Check plugin Woocommerce is active.
	*
	* @author Comfythemes
	* @since 1.0
	*/
	public static function check_woo_active(){

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if (is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return true;
		}else{
			return false;
		}
	}

	/**
	* Define Constants
	*
	* @author Comfythemes
	* @since 1.0
	*/
	private function define_constants() {
		
		$this->define('NAM_VER', '1.0');
		$this->define('NAM_NAME', esc_html__('Woocommerce - Dropdown state - VietNam', 'wzd'));
		$this->define('NAM_FOLDER', basename(dirname(__FILE__)));
		$this->define('NAM_DIR', plugin_dir_path(__FILE__));
		$this->define('NAM_URL', plugin_dir_url(NAM_FOLDER).NAM_FOLDER.'/');
		$this->define('NAM_ASSETS', NAM_URL.'assets/');
		$this->define('NAM_JS', NAM_URL.'assets/js/');
		$this->define('NAM_CSS', NAM_URL.'assets/css/');
		$this->define('NAM_IMG', NAM_URL.'assets/images/');
		
	}


	/**
	* Define constant if not already set
	*
	* @param  string $name
	* @param  string|bool $value
	* @author Comfythemes
	* @since 1.0
	*/
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	* Hook into actions and filters
	*
	* @author Comfythemes
	* @since 1.0
	*/
	public function init_hooks() {

		if ( ! $this->check_woo_active() ) {
			add_action( 'admin_notices', array( $this, 'installation_notice') );
		}else{

			add_action( 'wp_enqueue_scripts', array($this, 'nam_enqueue_scripts') );
			add_action( 'admin_enqueue_scripts', array($this, 'nam_admin_enqueue_scripts') );
			add_action( 'woocommerce_checkout_update_order_meta', array($this, 'nam_save_field_distric'));
		}

	}

	/**
	* Register the script for the public-facing side of the site.
	*
	* @since    1.0
	*/
	public function nam_enqueue_scripts(){

		if ( is_checkout() ) {

			wp_enqueue_script( 'nam-state-select', NAM_JS . 'nam-state.js', array( 'wc-country-select' ), NAM_VER, true );
			
			$response = wp_remote_get( NAM_JS . 'data-state-vn.json' );

			if( is_array($response) ) {
				$body = $response['body']; // use the content
			}else{
				$body = array();
			}

			wp_localize_script( 'nam-state-select', 'nam_state_params', array( 'state' => json_decode($body) ) );
		}
	}

	public function nam_admin_enqueue_scripts(){
		wp_enqueue_script( 'nam-admin', NAM_JS . 'nam-admin.js', array(), NAM_VER, true );

		if( is_user_logged_in() ){
        	$current_user = wp_get_current_user();
        	$distric = get_user_meta( $current_user->ID, 'billing_distric_vn', true );
        }else{
        	$distric = '';
        }

		wp_localize_script( 'nam-admin', 'nam_state_params', array( 'distric' => $distric ) );
	}

	/**
	* Save field with user id vs order id
	*/
	public function nam_save_field_distric( $order_id ){

		if ( ! empty( $_POST['billing_distric_vn'] ) ) {
	        update_post_meta( $order_id, 'billing_distric_vn', sanitize_text_field( $_POST['billing_distric_vn'] ) );

	        if( is_user_logged_in() ){
	        	$current_user = wp_get_current_user();
	        	update_user_meta( $current_user->ID, 'billing_distric_vn', sanitize_text_field( $_POST['billing_distric_vn'] ) );
	        }
	    }

	}


    /**
	* Display notice if woocommerce is not installed
	*
	* @author Comfythemes
	* @since 1.0
	*/
    public function installation_notice() {
        echo '<div class="error" style="padding:15px; position:relative;"><a href="http://wordpress.org/plugins/woocommerce/">Woocommerce</a>  must be installed and activated before using <strong>'.NAM_NAME.'</strong> plugin. </div>';
    }

}

/**
* Plugin load
*/
function Woocommerce_State_VietNam_Load_Plugin() {
	return Woocommerce_State_VietNam::instance();
}
$GLOBALS['wsvn'] = Woocommerce_State_VietNam_Load_Plugin();
?>