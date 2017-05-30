<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       goperuse.com
 * @since      1.0.0
 *
 * @package    Peruse
 * @subpackage Peruse/admin/partials
 */
?>

<div class="wrap">

  <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

  <h2 class="nav-tab-wrapper">
    <a href="?page=peruse" class="nav-tab nav-tab-active">Connect</a>
    <a href="?page=peruse-products" class="nav-tab">Products</a>
    <a href="?page=peruse-incentives" class="nav-tab">Incentives</a>
  </h2>

  <h2 class="title">Application Credentials</h2>

  <p>This information can be found in the application&#39;s settings section. Create a 'test application' for development purposes and use the 'production application' for a live environment.</p>

  <form method="post" name="peruse_connect_options" action="options.php">


  	<?php
        //Grab all options
        $options = get_option($this->plugin_name);

        // oAuth2 Credentials 
        $oauth2_id = $options['oauth2_id']; 
        $oauth2_secret = $options['oauth2_secret']; 

        // Scope
        $scope = (!empty($options['scope']) ? $options['scope'] : []);
    ?>

    <?php
        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);
    ?>

    <table class="form-table">
      <tbody>
        <tr>
           <th>
            <label for="<?php echo $this->plugin_name; ?>-oauth2_id">oAuth ID</label>
           </th> 
           <td>
             <input type="text" class="regular-text" id="<?php echo $this->plugin_name; ?>-oauth2_id" name="<?php echo $this->plugin_name; ?>[oauth2_id]" value="<?php if(!empty($oauth2_id)) echo $oauth2_id; ?>" required/>
           </td>
        </tr>
        <tr>
           <th>
            <label for="<?php echo $this->plugin_name; ?>-oauth2_secret">oAuth Secret</label>
           </th> 
           <td id="secret_container">
             <input type="password" class="regular-text" id="<?php echo $this->plugin_name; ?>-oauth2_secret" name="<?php echo $this->plugin_name; ?>[oauth2_secret]" value="<?php if(!empty($oauth2_secret)) echo $oauth2_secret; ?>" required/>

             <a id="secret-toggle" class="button-secondary genericon genericon-password" href="#"></a>
           </td>
        </tr>
      </tbody>
    </table>

    <h2 class="title">oAuth2 Scope</h2>

    <p>By default, when a user connects to your site they grant you the ability to access their 'basic' information (name &amp; balance) and charge for products they've elected to purchase. <strong>Select from below, the additional information you would like to collect from users.</strong></p>

    <table class="form-table">
      <tbody>
        <tr>
           <th>
            <label for="<?php echo $this->plugin_name; ?>-scope_email">E-mail address</label>
           </th> 
           <td>

            <input type="checkbox" id="<?php echo $this->plugin_name; ?>-scope_email" name="<?php echo $this->plugin_name; ?>[scope][]" value="email" <?php if(in_array('email',$scope)){echo "checked";} ?>/>
           </td>
        </tr>
      </tbody>
    </table>

  <?php submit_button('Save changes', 'primary','submit', TRUE); ?>

  </form>

</div>
