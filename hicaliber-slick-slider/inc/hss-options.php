<?php 

/**
* 
*/
class HSS_OPTIONS
{
	   /*
     * Add the admin menus
     */
    public static function Add_Admin_Menus(){

        add_submenu_page( 'edit.php?post_type=slider-items' , __( 'HSS Slick Slider Options', 'hss_slick_slider' ) 
        	, 'Manage Slider', 'edit_posts', 'hss-options' , array('HSS_OPTIONS', 'Options_Templage_Page') );

    }

    public static function options() {
    	register_setting('hss_settings_group', 'hss_settings');
    }

        /*
     * Create the options page
     */
    public static function Options_Templage_Page(){ ?>
		<?php $hss_options = get_option('hss_settings'); ?>
        <div class="wrap">

            <h2 style="clear:both;"><?php _e( 'Hicaliber Slick Slider Options' ); ?></h2>

            <form method="post" action="options.php">
            <?php settings_fields( 'hss_settings_group' ); ?>
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div id="post-body">
						<div id="post-body-content">
							<div class="postbox">
								<h3><span>Settings</span></h3>
								<div class="inside">
									<table class="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row">Speed</th>
												<td>
													<input type="number" name="hss_settings[hss_speed]" value="<?php echo $hss_options['hss_speed']; ?>">
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">Auto Play</th>
												<td>
													<select name="hss_settings[hss_autoplay]">
														<option value="true"<?php if (isset($hss_options['hss_autoplay']) && $hss_options['hss_autoplay']=='true'){echo 'selected';} ?>><?php _e('True') ?></option>
														<option value="false" <?php if (isset($hss_options['hss_autoplay']) && $hss_options['hss_autoplay']=='false'){echo 'selected';} ?> ><?php _e('False') ?></option>
													</select>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row">Infinite Loop</th>
												<td>
													<select name="hss_settings[hss_infiniteloop]">
														<option value="true"<?php if (isset($hss_options['hss_infiniteloop']) && $hss_options['hss_infiniteloop']=='true'){echo 'selected';} ?>><?php _e('True') ?></option>
														<option value="false" <?php if (isset($hss_options['hss_infiniteloop']) && $hss_options['hss_infiniteloop']=='false'){echo 'selected';} ?> ><?php _e('False') ?></option>
													</select>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<p class="submit">
                   			<?php submit_button(); ?>
                		</p>
					</div>
					<div class="inner-sidebar">
						<div class="postbox">
							<h3><span>Slider set up</span></h3>
							<div class="inside">
								<p>Copy and paste below shortcode edit area: </p>
								<code>['slider category="slider_category"']</code>
								<br>
								<p>Copy and paste below code in your template file: </p>
								<code>hi_slick_slider('slider_category');</code>
								<br>
								<p>That's it! Enjoy HSS Slider!</p>
							</div>
						</div>
					</div>
            	</div>
            </form>

        </div>

    <?php }

}

 ?>