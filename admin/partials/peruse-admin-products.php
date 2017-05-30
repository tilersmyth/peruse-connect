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
    <a href="?page=peruse" class="nav-tab">Connect</a>
    <a href="?page=peruse-products" class="nav-tab nav-tab-active">Products</a>
    <a href="?page=peruse-incentives" class="nav-tab">Incentives</a>
  </h2>

  <h2 class="title">Articles</h2>

 <form method="post" name="peruse_products_options" action="options.php">

	<?php
    //Grab all options
    $options = get_option($this->plugin_name . '-products');

    $enable = $options['enable']; 
    $price = $options['price']; 
    $integration = $options['integration'];
  ?>

  <?php
  	settings_fields($this->plugin_name . '-products');
  	do_settings_sections($this->plugin_name . '-products');
  ?>
 
 	<table class="form-table">
    <tbody>
      <tr>
         <th>
          <label for="<?php echo $this->plugin_name; ?>-enable">Enable</label>
         </th> 
         <td>
          <input type="checkbox" id="<?php echo $this->plugin_name; ?>-enable" name="<?php echo $this->plugin_name; ?>-products[enable]" value="true" <?php checked($enable, true); ?> />
         </td>
      </tr>
      <tr>
         <th>
          <label for="<?php echo $this->plugin_name; ?>-price">Default price</label>
         </th> 
         <td>
          <input type="text" class="article_child_element" class="regular-text" id="<?php echo $this->plugin_name; ?>-price" name="<?php echo $this->plugin_name; ?>-products[price]" value="<?php echo $price; ?>" <?php echo (!($enable) ? 'disabled': '') ?>/>
          <br>
          <span class="description">Articles will default to this price if not specified upon publishing</span>
         </td>
      </tr>
      <tr>
         <th>
         	Integration
         </th> 
         <td>
         	<fieldset>
	         	<label for="<?php echo $this->plugin_name; ?>-integration">
	          	<input type="checkbox" class="article_child_element" id="<?php echo $this->plugin_name; ?>-integration" name="<?php echo $this->plugin_name; ?>-products[integration]" value="true" <?php checked($integration, true); ?> <?php echo (!($enable) ? 'disabled': '') ?> /> Use pre-built integration
	          </label>
          </fieldset>
         </td>
      </tr>
    </tbody>
  </table>

  <?php submit_button('Save changes', 'primary','submit', TRUE); ?>

  </form>  
  
</div>
