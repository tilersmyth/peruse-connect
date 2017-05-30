<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       goperuse.com
 * @since      1.0.0
 *
 * @package    Peruse
 * @subpackage Peruse/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Peruse
 * @subpackage Peruse/public
 * @author     Tyler Smith <tyler@goperuse.com>
 */
class Peruse_Public {

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

	private static $baseUrl = 'https://app.goperuse.com/'; 

	private $provider;

	private $peruse_user;

	private $user_token;

	private $auth_link;

	private $product;

	private $meter = false;

	private $peruse_options;

	private $peruse_products;

	private $peruse_incentives;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->peruse_options = get_option($this->plugin_name);
		$this->peruse_products = get_option($this->plugin_name . '-products');
		$this->peruse_incentives = get_option($this->plugin_name . '-incentives');

	}

	/**
	 * Register the JavaScript for the Public area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		//No scripts for now

	}


	private function get($route) {

		if(isset($_SESSION['peruse_access_token'])){
			$request = $this->provider->getAuthenticatedRequest(
		      'GET',
		      $this::$baseUrl . 'api/graph/v1/' . $route,
		      $_SESSION['peruse_access_token']
		    );

			$response = $this->provider->getHttpClient()->send($request);
			return json_decode($response->getBody());
		}

		return null;

	}


	private function post($route, $body) {

		if(isset($_SESSION['peruse_access_token'])){

			$options['body'] = json_encode($body);
			$options['headers']['content-type'] = 'application/json';

			$request = $this->provider->getAuthenticatedRequest(
		      'POST',
		      $this::$baseUrl . 'api/graph/v1/' . $route,
		      $_SESSION['peruse_access_token'],
		      $options
		    );

			$response = $this->provider->getHttpClient()->send($request);
			return json_decode($response->getBody());
		}

		return null;

	}

	private function put($route, $body) {

		if(isset($_SESSION['peruse_access_token'])){

			$options['body'] = json_encode($body);
			$options['headers']['content-type'] = 'application/json';

			$request = $this->provider->getAuthenticatedRequest(
		      'PUT',
		      $this::$baseUrl . 'api/graph/v1/' . $route,
		      $_SESSION['peruse_access_token'],
		      $options
		    );

			$response = $this->provider->getHttpClient()->send($request);
			return json_decode($response->getBody());
		}

		return null;

	}


	public function connect() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

		// start the user session for maintaining individual user states during the multi-stage authentication flow:
		session_start();

		define('CLIENT_ID', $this->peruse_options['oauth2_id']);
		define('CLIENT_SECRET', $this->peruse_options['oauth2_secret']);
		define('REDIRECT_URI', rtrim(site_url(), '/') . '/');

		if (CLIENT_ID && CLIENT_SECRET){
		  
		  $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
		    'clientId'          =>  CLIENT_ID,
		    'clientSecret'      =>  CLIENT_SECRET,
		    'redirectUri'       => REDIRECT_URI,
		    'urlAuthorize'            => $this::$baseUrl . 'api/oauth2/authorize',
		    'urlAccessToken'          => $this::$baseUrl . 'api/oauth2/exchange',
		    'urlResourceOwnerDetails' => $this::$baseUrl . 'api/graph/v1'
		  ]);

		    //  Check for logout    
			if (isset($_REQUEST['logout'])) {
			   session_unset();
			}

			if (isset($_SESSION['peruse_access_token'])) {

				//refresh token if necessary
				if($_SESSION['peruse_token_exp'] < time()){

					unset($_SESSION['peruse_access_token']);

		    	$access_token = $this->provider->getAccessToken('refresh_token', [
		        'refresh_token' => $_SESSION['peruse_refresh_token']
		    	]);	

		    	$_SESSION['peruse_access_token'] = $access_token->getToken();
					$_SESSION['peruse_refresh_token'] = $access_token->getRefreshToken();
					$_SESSION['peruse_token_exp'] = $access_token->getExpires();
				}

				$this->user_token = $_SESSION['peruse_access_token'];

				$this->peruse_user = $this->get('user');
				return;

			}


			if (!isset($_GET['code'])) {

				$selected_scope = (!empty($this->peruse_options['scope']) ? $this->peruse_options['scope'] : []);
				$selected_scope_csv = implode(", ", $selected_scope);

		    $options = [
				   'scope' => $selected_scope_csv
				];

		    $this->auth_link = $this->provider->getAuthorizationUrl($options);

		    // Get the state generated for you and store it to the session.
		    $_SESSION['oauth2state'] = $this->provider->getState();


			// Check given state against previously stored one to mitigate CSRF attack
			} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

			    if (isset($_SESSION['oauth2state'])) {
			        unset($_SESSION['oauth2state']);
			    }
			    
			    exit('Invalid state');

			} else {

			  try {

			  	$access_token = $this->provider->getAccessToken('authorization_code', [
			       'code' => $_GET['code']
			    ]);

					$_SESSION['peruse_access_token'] = $access_token->getToken();
					$_SESSION['peruse_refresh_token'] = $access_token->getRefreshToken();
					$_SESSION['peruse_token_exp'] = $access_token->getExpires();

				  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']; 

				  header('Location:' . filter_var($redirect, FILTER_SANITIZE_URL));
				  die();

			  } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

		      // Failed to get the access token or user details.
		      exit($e->getMessage());

			  }
			}
		}
  }


  public function user_token() {

  	return $this->user_token;

  }

  public function auth_link() { 

  	return $this->auth_link;

  }


  public function user() {

  	return $this->peruse_user;

  }


  public function product($product_id, $product_type) {

  	if(!$this->product){

  		$this->product = $this->get('product/' . $product_type . '/' . $product_id);

  	}

  	return $this->product;

  }


  public function meter($product_type) { 

  	$app_meter = (object) array(
  		'allowed_views' => $this->peruse_incentives['meter_quantity'], 
  		'remaining_views' => $this->peruse_incentives['meter_quantity'], 
  		'product_type' => $product_type,
  		'meta' => array('schedule' => $this->peruse_incentives['meter_schedule']),
  		'schedule' => $this->peruse_incentives['meter_schedule'],
  		'new' => true
  		);

  	if(!$this->meter && $this->peruse_incentives['meter_quantity']){
  		$user_meter = $this->get('meter/' . $product_type);
  		$this->meter = $this->determine_meter($user_meter, $app_meter);
  	}

  	return $this->meter;

  }


  public function update_meter($action, $product_id, $product_type) {

  	//Save free product
  	$post_article = $this->post('product',
		array(
			'title' => get_the_title($product_id),
			'type' => $product_type, 
			'id' => $product_id,
			'product_url' => get_permalink($product_id),
			'description' => 'Metered product type: ' . $product_type, 
			'regular_price' => 0
		));

  	
  	$app_meter = $this->meter($product_type);
  	
	//Save/update meter
	if($action == 'save'){
		$app_meter->remaining_views = $app_meter->allowed_views - 1;
		$meter = $this->post('meter', $app_meter);
	}else{
		$app_meter->remaining_views = $app_meter->remaining_views - 1;
		$meter = $this->put('meter/' . $product_type, $app_meter);
	}

	$this->meter = $meter;

  }


  private function meter_expiration($schedule){

		switch ($schedule) {
			case 'day':
				$date = new DateTime('tomorrow');
		  	$date->setTime(0, 0);
				return $date;
		    break;
		  case 'week':
		  	$date = new DateTime('next monday');
		  	$date->setTime(0, 0);
				return $date;
		    break;
		  case 'month':
				$date = new DateTime('first day of next month');
				$date->setTime(0, 0);
				return $date;
		    break;
		  default:
		  	return new DateTime('+1 year');
		}

	}


	private function determine_meter($user_meter, $app_meter) {

		//No existing meter
		if(!$user_meter || !$user_meter->data){

			$expiration = $this->meter_expiration($app_meter->meta["schedule"]);
			$app_meter->expiration = $expiration->format('Y-m-d H:i:s');

			return $app_meter;
		}

		//Meter expired or is 'signup' with 0 remaining AND meter has changed from 'signup'
		$meter_exp = new DateTime($user_meter->data->expiration);
		$meter_exp->setTimezone(new DateTimeZone('America/New_York'));
		$current_date = new DateTime("now", new DateTimeZone('America/New_York') );

		$schedule_user = $user_meter->data->meta[0]->schedule;
		$schedule_app = $app_meter->meta["schedule"];

		if(($meter_exp->format('Y-m-d H:i:s') < $current_date->format('Y-m-d H:i:s')) || 
			(($schedule_user == 'signup' && $user_meter->data->remaining_views == 0) && 
			($schedule_app != 'signup'))){

			$expiration = $this->meter_expiration($user_meter->data->meta[0]->schedule);
			$app_meter->expiration = $expiration->format('Y-m-d H:i:s');
			return $app_meter;
		}

		//Existing meter
		return $user_meter->data;

	}

}

//Public functions
function peruse_user_token() { 
	global $peruse_config;

	return $peruse_config->user_token();
}

function peruse_login_link() {
	global $peruse_config;

	return $peruse_config->auth_link();
}

function peruse_logout_link() {

	$logout_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

	return $logout_url . '?logout';
}

function peruse_current_user() {
	global $peruse_config;

	return $peruse_config->user();
}


function peruse_meter($product_type) {
	global $peruse_config;

	$meter = $peruse_config->meter($product_type);

	return $meter;
}

function peruse_post_auth($postId, $product_type, $subscriber_role, $allow_free = true) {
	global $peruse_config;

	$user_logged_in = peruse_current_user();

	if(!$user_logged_in){
		return false;
	}

	$price_meta = get_post_meta($postId, 'peruse-article-price', true);

	if($allow_free && $price_meta == '0'){
		return true;
	}

	if($user_logged_in->role == $subscriber_role){
		return true;
	}

	$has_product = $peruse_config->product($postId, $product_type);

	if($has_product->data && $has_product->data->authorized){
		return true;
	}

	$meter = $peruse_config->meter($product_type);

	if(!$meter){
		return false;
	}

	if(isset($meter->new)){

		$peruse_config->update_meter('save', $postId, $product_type);

		return true;
	}

	if($meter->remaining_views>0){

		$peruse_config->update_meter('update', $postId, $product_type);

		return true;

	}

}
