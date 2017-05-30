<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       goperuse.com
 * @since      1.0.0
 *
 * @package    Peruse
 * @subpackage Peruse/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Peruse
 * @subpackage Peruse/admin
 * @author     Tyler Smith <tyler@goperuse.com>
 */
class Peruse_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Peruse_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Peruse_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/peruse-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-genericons', plugin_dir_url( __FILE__ ) . 'css/genericons.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Peruse_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Peruse_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jquery.maskMoney.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-mask', plugin_dir_url( __FILE__ ) . 'js/peruse-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	 
	public function add_plugin_admin_menu() {

	    /*
	     * Add a settings page for this plugin to the Settings menu.
	     *
	     * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
	     *
	     *        Administration Menus: http://codex.wordpress.org/Administration_Menus
	     *
	     */
	    add_menu_page('Peruse', 'Peruse', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'), 'dashicons-peruse');
	    add_submenu_page( $this->plugin_name, 'Peruse', 'Connect', 'manage_options', $this->plugin_name);
	    add_submenu_page( $this->plugin_name, 'Peruse', 'Products', 'manage_options', $this->plugin_name . '-products', array($this, 'display_plugin_products_page'));
			add_submenu_page( $this->plugin_name, 'Peruse', 'Incentives', 'manage_options', $this->plugin_name . '-incentives', array($this, 'display_plugin_incentive_page'));	

	}

	 /**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	 
	public function add_action_links( $links ) {
	    /*
	    *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	    */
	   $settings_link = array(
	    '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
	   );
	   return array_merge(  $settings_link, $links );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	 
	public function display_plugin_setup_page() {
	    include_once( 'partials/peruse-admin-display.php' );
	}

	public function display_plugin_products_page() {
	    include_once( 'partials/peruse-admin-products.php' );
	}

	public function display_plugin_incentive_page() {
	    include_once( 'partials/peruse-admin-incentive.php' );
	}

	public function options_update() {
    	register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
    	register_setting($this->plugin_name . '-incentives', $this->plugin_name . '-incentives', array($this, 'validate_incentives'));
    	register_setting($this->plugin_name . '-products', $this->plugin_name . '-products', array($this, 'validate_products'));


    	$products = get_option($this->plugin_name . '-products');

    	if($products['enable']){
    		add_action('add_meta_boxes', array($this, 'article_price_meta_box'));
    	}
 	}

 	public function validate_incentives($input) {
	           
	    $valid = array();

	    // Meter config
	    $valid['meter_quantity'] = $input['meter_quantity']; 
	    $valid['meter_schedule'] = isset($input['meter_schedule']) ? $input['meter_schedule'] : 'signup';
	    
	    return $valid;
	 }

	 public function validate_products($input) {
	           
	    $valid = array();

	    $valid['enable'] = (isset($input['enable']) && !empty($input['enable'])) ? true : false;
	    $valid['price'] = (!empty($input['price'])) ? $input['price'] : '0.00';
	    $valid['integration'] = (isset($input['integration']) && !empty($input['integration'])) ? true : false;
	    
	    return $valid;
	 }

	public function validate($input) {
	           
	    $valid = array();

	    //oAuth2 Credentials
	    $valid['oauth2_id'] = $input['oauth2_id'];
	    $valid['oauth2_secret'] = $input['oauth2_secret'];

	    //Scope
	    $valid['scope'] = (isset($input['scope']) && !empty($input['scope'])) ? $input['scope'] : 0;
	    
	    return $valid;
	 }


	public function article_price_meta_box() { 
		add_meta_box("peruse-meta-box", "Peruse Article Price", array($this, 'article_meta_box_markup'), "post", "side", "high", null);  
	}

	public function article_meta_box_markup($object){
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");

    $products = get_option($this->plugin_name . '-products');

    $default_price = $products['price'] ? $products['price'] : 0;

    ?>
    <div>
       <input name="peruse-article-price" id="peruse-price" type="text" value="<?php echo (get_post_meta($object->ID, "peruse-article-price", true) !== '') ? get_post_meta($object->ID, "peruse-article-price", true) : $default_price; ?>">
    </div>
    <?php  
	}

	public function save_peruse_price_meta($post_id, $post, $update){
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "post";
    if($slug != $post->post_type)
        return $post_id;

    $article_price = "";

    if(isset($_POST["peruse-article-price"])){
        $article_price = $_POST["peruse-article-price"];
    } 


    update_post_meta($post_id, "peruse-article-price", $article_price);
	}

}
