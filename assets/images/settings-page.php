<?php

/**
 * displays the plugin settings page.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div class="wrap">

    <h2><?php _e( 'Guaranteed Reviews Company', 'woo-guaranteed-reviews-company' ); ?></h2>

    <?php if ( isset($_POST['wp-sag-settings-submit']) ) : ?>
        <div class="updated notice"><p><?php _e( 'Settings saved!', 'woo-guaranteed-reviews-company' ); ?></p></div>
    <?php endif; ?>

    <?php if ( isset($_POST['wp-sag-reset-submit']) ) : ?>
        <div class="updated notice"><p><?php _e( 'All reviews successfully deleted', 'woo-guaranteed-reviews-company' ); ?></p></div>
    <?php endif; ?>

    <?php if ( isset($_GET['account_created']) ) : ?>
        <div class="updated notice"><p><?php _e( 'Account successfully created!', 'woo-guaranteed-reviews-company' ); ?></p></div>
    <?php endif; ?>

    <form method="post">

      <?php wp_nonce_field( 'wp-sag-settings-form' ); ?>

        <h3 class="title"><?php _e( 'General settings', 'woo-guaranteed-reviews-company' ); ?></h3>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="api_key"><?php _e( 'API Key', 'woo-guaranteed-reviews-company' ); ?></label>
                </th>
                <td>
                    <?php if ( $languages = apply_filters( 'wpml_active_languages', null ) ) : ?>
                        <?php foreach ( $languages as $language ) : ?>
                            <img src="<?php echo $language['country_flag_url']; ?>" height="12" alt="<?php echo $language['translated_name']; ?>" width="18" /> &nbsp;
                            <input class="regular-text ltr"
                                   type="text"
                                   name="api_key[<?php echo $language['language_code']; ?>]"
                                   value="<?php
                                                echo ( is_array( $raw_api_key = $this->settings->get( 'api_key_raw' ) )
                                                        && array_key_exists( $language['language_code'], $raw_api_key ) ) ?
                                                        $raw_api_key[ $language['language_code'] ]
                                                        :
                                                        $raw_api_key;
                                        ?>" /><br/>
                       <?php endforeach; ?>
                        <?php else : ?>
                    <input class="regular-text ltr"
                           type="text"
                           name="api_key"
                           value="<?php echo $this->settings->get( 'api_key' ); ?>" />
                    <?php endif; ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="wc_statuses"><?php _e( 'Order statuses to include', 'woo-guaranteed-reviews-company' ); ?></label>
                </th>
                <td>
                    <?php if ( $wc_statuses = wc_get_order_statuses() ) : ?>
                        <select name="wc_statuses[]" multiple>
                            <?php foreach ( $wc_statuses as $status => $label ) : ?>
                                <option value="<?php echo $status; ?>"<?php echo in_array($status, $this->settings->get( 'wc_statuses' ) ) ? 'selected="selected"' : '' ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e( 'Select order statuses you want to send review requests (Use "Ctrl" keyboard key to select many ones)', 'woo-guaranteed-reviews-company' ); ?></p>

                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <h3 class="title"><?php _e( 'Widget options', 'woo-guaranteed-reviews-company' ); ?></h3>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="enable_widget_js"><?php _e( 'Javascript', 'woo-guaranteed-reviews-company' ); ?></label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php _e( 'Javascript widget', 'woo-guaranteed-reviews-company' ); ?></span></legend>
                        <label for="enable_widget_js">
                            <input name="enable_widget_js" type="checkbox" value="1" id="enable_widget_js" <?php echo ( $this->settings->get( 'enable_widget_js' ) == 1 ) ? 'checked="checked"' : '' ?>>
                            <?php _e( 'Enable Javascript widget', 'woo-guaranteed-reviews-company' ); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="enable_widget_product_summary"><?php _e( 'Product', 'woo-guaranteed-reviews-company' ); ?></label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php _e( 'Product widget', 'woo-guaranteed-reviews-company' ); ?></span></legend>
                        <label for="enable_widget_product">
                            <input name="enable_widget_product" type="checkbox" value="1" id="enable_widget_product" <?php echo ( $this->settings->get( 'enable_widget_product' ) == 1 ) ? 'checked="checked"' : '' ?>>
                            <?php _e( 'Enable product widget', 'woo-guaranteed-reviews-company' ); ?>
                        </label>
                        <p class="description"><?php _e('Alternatively you can use <code>[wcsag_summary]</code> and <code>[wcsag_reviews]</code> shortcodes on your product page', 'woo-guaranteed-reviews-company' ); ?></p>
                    </fieldset>
                </td>
            </tr>

            <tr valign="top">
              <th scope="row">
                  <label for="widget_product_summary_style"><?php _e( 'Product widget', 'woo-guaranteed-reviews-company' ); ?></label>
              </th>
              <td>
                  <fieldset>
                      <legend class="screen-reader-text"><span><?php _e( 'Product widget', 'woo-guaranteed-reviews-company' ); ?></span></legend>
                      <label for="widget_style" style="display:block;">
                          <?php _e( 'Choose Widget product rating style (On product pages)', 'woo-guaranteed-reviews-company' ); ?>
                      </label>
                      <div class="widget_style_preview" style="display:flex; flex-direction:row; align-items: center;">
                        <div class="buttons">
                          <input name="widget_style" type="radio" value="0" id="widget_summary_off" <?php echo ( $this->settings->get( 'widget_style' ) == 0 ) ? 'checked="checked"' : '' ?>>
                          <span><?php _e('Disable', 'woo-guaranteed-reviews-company' ); ?></span><br><br>
                          <input name="widget_style" type="radio" value="1" id="widget_style_1" <?php echo ( $this->settings->get( 'widget_style' ) == 1 ) ? 'checked="checked"' : '' ?>>
                          <span><?php _e('Classic style', 'woo-guaranteed-reviews-company' ); ?></span><br><br>
                          <input name="widget_style" type="radio" value="2" id="widget_style_2" <?php echo ( $this->settings->get( 'widget_style' ) == 2 ) ? 'checked="checked"' : '' ?>>
                          <span><?php _e('Logo & stars', 'woo-guaranteed-reviews-company' ); ?></span><br><br>
                          <input name="widget_style" type="radio" value="3" id="widget_style_3" <?php echo ( $this->settings->get( 'widget_style' ) == 3 ) ? 'checked="checked"' : '' ?>>
                          <span><?php _e('Stars', 'woo-guaranteed-reviews-company' ); ?></span>
                        </div>
                        <div style="margin-left: 50px;">
                          <img id="style_1" <?php echo ( $this->settings->get( 'widget_style' ) == 1 ) ? 'style="display: block"' : 'style="display: none"' ?> src="<?php echo WC_SAG_PLUGIN_URL; ?>assets/images/style_1_preview_<?php echo $this->settings->get( 'sag_lang' ); ?>.png"  alt="Widget Style 1">
                          <img id="style_2" <?php echo ( $this->settings->get( 'widget_style' ) == 2 ) ? 'style="display: block"' : 'style="display: none"' ?> src="<?php echo WC_SAG_PLUGIN_URL; ?>assets/images/style_2_preview_<?php echo $this->settings->get( 'sag_lang' ); ?>.png"  alt="Widget Style 2">
                          <img id="style_3" <?php echo ( $this->settings->get( 'widget_style' ) == 3 ) ? 'style="display: block"' : 'style="display: none"' ?> src="<?php echo WC_SAG_PLUGIN_URL; ?>assets/images/style_3_preview_<?php echo $this->settings->get( 'sag_lang' ); ?>.png"  alt="Widget Style 3">
                        </div>
                      </div>
                      <script type="text/javascript">
                          for (var i = 1; i < 4; i++) {
                            document.querySelector('#widget_style_'+i).addEventListener('change', function(){
                                if (this.checked == true) {
                                  document.querySelectorAll('.widget_style_preview img').forEach((item, j) => {
                                    item.style.display = "none";
                                  });
                                  let imageIdToDisplay = "style_" + this.id.slice(-1);
                                  document.getElementById(imageIdToDisplay).style.display = "block";
                                }
                            });
                          }
                          document.querySelector('#widget_summary_off').addEventListener('change', function(){
                              if (this.checked == true) {
                                document.querySelectorAll('.widget_style_preview img').forEach((item, j) => {
                                  item.style.display = "none";
                                });
                              }
                          });
                      </script>
                  </fieldset>
              </td>
            </tr>

            <tr valign="top">
              <th scope="row">
                  <label for="review_origin_lang_flags"><?php _e( 'Translated reviews origin lang flags', 'woo-guaranteed-reviews-company' ); ?></label>
              </th>
              <td>
                  <fieldset>
                      <legend class="screen-reader-text"><span><?php _e( 'Translated reviews origin lang flags', 'woo-guaranteed-reviews-company' ); ?></span></legend>
                      <label for="flags" style="display:block;">
                          <?php _e( 'Choose Widget product rating style (On product pages)', 'woo-guaranteed-reviews-company' ); ?>
                      </label>
                      <div class="flags_state" style="display:flex; flex-direction:row; align-items: center;">
                        <div class="buttons">
                          <input name="flags" type="radio" value="0" <?php echo ( $this->settings->get( 'review_origin_lang_flags' ) == 0 ) ? 'checked="checked"' : '' ?>>
                          <span><?php _e('Disable', 'woo-guaranteed-reviews-company' ); ?></span><br><br>
                          <input name="flags" type="radio" value="1" <?php echo ( $this->settings->get( 'review_origin_lang_flags' ) == 1 ) ? 'checked="checked"' : '' ?>>
                          <span><?php _e('Enable only for translated reviews', 'woo-guaranteed-reviews-company' ); ?></span><br><br>
                          <input name="flags" type="radio" value="2" <?php echo ( $this->settings->get( 'review_origin_lang_flags' ) == 2 ) ? 'checked="checked"' : '' ?>>
                          <span><?php _e('Enable for every review', 'woo-guaranteed-reviews-company' ); ?></span>
                        </div>
                      </div>
                  </fieldset>
              </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="enable_widget_js"><?php _e( 'Iframe', 'woo-guaranteed-reviews-company' ); ?></label>
                </th>
                <td>

                    <p class="description"><?php echo sprintf( wp_kses( __( 'Use <code>[wcsag_iframe]</code> shortcode or our widget in <a href="%s">Appearance > Widgets</a>.', 'woo-guaranteed-reviews-company' ), array(  'a' => array( 'href' => array() ), 'code' => array() ) ), esc_url( admin_url( 'widgets.php' ) ) ); ?></p>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="enable_widget_footer"><?php _e( 'Footer', 'woo-guaranteed-reviews-company' ); ?></label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php _e( 'Footer widget', 'woo-guaranteed-reviews-company' ); ?></span></legend>
                        <label for="enable_widget_footer">
                            <input name="enable_widget_footer" type="checkbox" value="1" id="enable_widget_footer" <?php echo ( $this->settings->get( 'enable_widget_footer' ) == 1 ) ? 'checked="checked"' : '' ?>>
                            <?php _e( 'Enable footer widget', 'woo-guaranteed-reviews-company' ); ?>
                        </label>
                        <p class="description"><?php echo sprintf( wp_kses( __( 'Works with storefront based themes. Alternatively you can use <code>[wcsag_footer]</code> shortcode or our widget in <a href="%s">Appearance > Widgets</a>.', 'woo-guaranteed-reviews-company' ), array(  'a' => array( 'href' => array() ), 'code' => array() ) ), esc_url( admin_url( 'widgets.php' ) ) ); ?></p>
                    </fieldset>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="enable_loop_rating"><?php _e( 'Loop rating', 'woo-guaranteed-reviews-company' ); ?></label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php _e( 'Loop rating', 'woo-guaranteed-reviews-company' ); ?></span></legend>
                        <label for="enable_loop_rating">
                            <input name="enable_loop_rating" type="checkbox" value="1" id="enable_loop_rating" <?php echo ( $this->settings->get( 'enable_loop_rating' ) == 1 ) ? 'checked="checked"' : '' ?>>
                            <?php _e( 'Display star rating on product list', 'woo-guaranteed-reviews-company' ); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="posts_per_page"><?php _e( 'Maximum product reviews', 'woo-guaranteed-reviews-company' ); ?></label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php _e( 'Maximum product reviews', 'woo-guaranteed-reviews-company' ); ?></span></legend>
                        <label for="posts_per_page">
                            <input class="regular-text ltr" required name="posts_per_page" type="number" min="1" value="<?php echo $this->settings->get( 'posts_per_page' )?>" id="posts_per_page">
                            <p class="description"><?php _e( 'Choose how many product reviews you would like to show by default before showing the "Show more reviews" button', 'woo-guaranteed-reviews-company' ); ?></p>
                        </label>
                    </fieldset>
                </td>
            </tr>

        </table>

        <p class="submit">
            <input class="button button-primary"
                   type="submit"
                   name="wp-sag-settings-submit"
                   value="<?php _e( 'Update settings', 'woo-guaranteed-reviews-company' ); ?>" />
        </p>

    </form>

    <form method="post">

        <?php wp_nonce_field( 'wp-sag-reset-form' ); ?>

        <h3 class="title"><?php _e( 'Reset plugin', 'woo-guaranteed-reviews-company' ); ?></h3>

        <input class="button"
               type="submit"
               name="wp-sag-reset-submit"
               style="background-color: #dc3232; color: white;"
               onclick="return confirm('<?php _e( 'Are you sure?', 'woo-guaranteed-reviews-company' ); ?>');"
               value="<?php _e( 'Delete all reviews', 'woo-guaranteed-reviews-company' ); ?>" />
    </form>

</div>
