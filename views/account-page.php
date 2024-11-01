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

    <h2><img style="margin:20px 0;max-width: 250px;" src="<?php echo WC_SAG_PLUGIN_URL; ?>assets/images/steavisgarantis_logo_<?php echo substr( get_locale(), 0, 2) == 'fr' ? 'fr' : 'en' ?>.png" alt="<?php _e( 'Guaranteed Reviews Company', 'woo-guaranteed-reviews-company' ); ?>"></h2>

    <?php if ( isset( $messages ) && is_array( $messages ) ) : ?>
        <?php foreach( $messages as $message ) : ?>
            <div class="<?php echo $message['class']; ?>"><p><?php echo $message['message']; ?></p></div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <div class="postbox">
            
        <div class="inside">
            <h3><?php _e( 'Create an account', 'woo-guaranteed-reviews-company' ); ?></h3>
                    
            <p><?php _e( 'Please create an account on to get an API key.', 'woo-guaranteed-reviews-company' ); ?></p>

            <ul style="font-style: italic; color: green; margin: 0 0 2em">
                <li>
                    <span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php _e( 'In less than 3 minutes!', 'woo-guaranteed-reviews-company' ); ?>
                </li>
                <li>
                    <span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php _e( '30 days trial', 'woo-guaranteed-reviews-company' ); ?>
                </li>
            </ul>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="language"><?php _e( 'Account language', 'woo-guaranteed-reviews-company' ); ?></label>
                    </th>
                    <td>
                        <fieldset id="WCSAGAccountLanguage">
                            <label><input type="radio" name="language" value="https://www.societe-des-avis-garantis.fr/wp-login.php?action=register"<?php echo substr( get_locale(), 0, 2) == 'fr' ? 'checked="checked"' : '' ?>> 
								<?php _e( 'French (Société des Avis Garantis)', 'woo-guaranteed-reviews-company' ); ?>
							</label>
							<br/>
                            <label><input type="radio" name="language" value="https://www.guaranteed-reviews.com/wp-login.php?action=register"<?php echo substr( get_locale(), 0, 2) == 'en' ? 'checked="checked"' : '' ?>> 
								<?php _e( 'English (Guaranteed Reviews Company)', 'woo-guaranteed-reviews-company' ); ?>
							</label>
							<br/>
                            <label><input type="radio" name="language" value="https://www.sociedad-de-opiniones-contrastadas.es/wp-login.php?action=register"<?php echo substr( get_locale(), 0, 2) == 'es' ? 'checked="checked"' : '' ?>> 
								<?php _e( 'Spanish (Sociedad de Opiniones Contrastadas)', 'woo-guaranteed-reviews-company' ); ?>
							</label>
							<br/>
                            <label><input type="radio" name="language" value="https://www.societa-recensioni-garantite.it/wp-login.php?action=register"<?php echo substr( get_locale(), 0, 2) == 'it' ? 'checked="checked"' : '' ?>> 
								<?php _e( 'Italian (Società Recensioni Garantite)', 'woo-guaranteed-reviews-company' ); ?>
							</label>
							<br/>
                            <label><input type="radio" name="language" value="https://www.g-g-b.de/wp-login.php?action=register"<?php echo substr( get_locale(), 0, 2) == 'de' ? 'checked="checked"' : '' ?>> 
								<?php _e( 'German (Gesellschaft für Garantierte Bewertungen)', 'woo-guaranteed-reviews-company' ); ?>
							</label>
							<br/>
                            <label><input type="radio" name="language" value="https://www.g-b-n.nl/wp-login.php?action=register"<?php echo substr( get_locale(), 0, 2) == 'nl' ? 'checked="checked"' : '' ?>>
								<?php _e( 'Dutch (Gegarandeerde Beoordelingen Nederland)', 'woo-guaranteed-reviews-company' ); ?>
							</label>
                        </fieldset>
                    </td>
                </tr>
            </table>
        
            <a id="WCSAGRegisterLink" class="button button-primary" href="<?php echo $this->settings->get('sag_domain') . '/wp-login.php?action=register'; ?>" target="_blank" rel="noreferrer noopener" style="padding: 0.75em 1em;height: auto;font-weight: bold;margin-top: 1em;max-width: 280px;text-align: center;width: 100%;"><?php _e( 'Create your account', 'woo-guaranteed-reviews-company' ); ?> <span class="dashicons dashicons-external" aria-hidden="true"></span></a>
            <script>
                (function($){
                    $(function(){
                        $('#WCSAGAccountLanguage input').on('change', function(e) {
                            $('#WCSAGRegisterLink').attr('href', $(this).val());
                        });
                    })
                })(jQuery);
            </script>
        </div>
    </div>

    <form method="post" style="margin-top: 4em">
        
        <?php wp_nonce_field( 'wp-sag-registered-form' ); ?>

        <h3 class="title"><?php _e( 'Already registered?', 'woo-guaranteed-reviews-company' ); ?></h3>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="api_key"><?php _e( 'API Key', 'woo-guaranteed-reviews-company' ); ?></label>
                </th>
                <td>
                    <input class="regular-text ltr"
                           type="text"
                           name="api_key" />
                    <p class="description"><?php _e('Available on your Guaranteed Reviews Company account. ', 'woo-guaranteed-reviews-company' ); ?></p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input class="button button-primary"
                   type="submit"
                   name="wp-sag-registered-submit"
                   value="<?php _e( 'Submit', 'woo-guaranteed-reviews-company' ); ?>" />
        </p>

    </form>

</div>