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
    <a href="?page=peruse-products" class="nav-tab">Products</a>
    <a href="?page=peruse-incentives" class="nav-tab nav-tab-active">Incentives</a>
  </h2>

  <h2 class="title">Article Meter</h2>

  <form method="post" name="peruse_incentives_options" action="options.php">

  	<?php
      //Grab all options
      $options = get_option($this->plugin_name . '-incentives');

      // Meter config
      $meter_quantity = $options['meter_quantity']; 
      $meter_schedule = $options['meter_schedule']; 
    ?>

    <?php
    	settings_fields($this->plugin_name . '-incentives');
    	do_settings_sections($this->plugin_name . '-incentives');
    ?>

   <p>Allow free article viewing by selecting from the quantity and schedule below. Selecting "0" for the quantity will disable the meter. <strong>Note: Users granted a previously set meter will have access to that meter until it runs out.</strong></p>

    <table class="form-table">
      <tbody>
        <tr>
           <th>
            <label for="<?php echo $this->plugin_name; ?>-meter_quantity">Quantity Allowed</label>
           </th> 
           <td>

            <select name="<?php echo $this->plugin_name; ?>-incentives[meter_quantity]" id="<?php echo $this->plugin_name; ?>-meter_quantity">
              <?php 
                echo '<option '.(empty($meter_quantity) ? 'selected="selected"': '').' value="0">0 (Disabled)</option>';

                for ($x = 1; $x <= 20; $x++) {  
                  echo '<option '.($x == $meter_quantity ? 'selected="selected"': '').' value="'.$x.'">'.$x.'</option>';
                } 
              ?>           
            </select>
           </td>
        </tr>
        <tr>
          <th>
            <label for="<?php echo $this->plugin_name; ?>-meter_schedule">Schedule</label>
          </th> 
          <td>
            <select name="<?php echo $this->plugin_name; ?>-incentives[meter_schedule]" id="<?php echo $this->plugin_name; ?>-meter_schedule" <?php echo (empty($meter_quantity) ? 'disabled': '') ?>>
              <option <?php echo (empty($meter_schedule) || $meter_schedule == 'signup') ? 'selected="selected"' : ''; ?> value="signup">Sign up</option>
              <option <?php selected( $meter_schedule, 'day' ); ?> value="day">Day</option>
              <option <?php selected( $meter_schedule, 'week' ); ?> value="week">Week</option>
              <option <?php selected( $meter_schedule, 'month' ); ?> value="month">Month</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>

    <?php submit_button('Save changes', 'primary','submit', TRUE); ?>

  </form>
</div>
