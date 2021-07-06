<?php
/**
 * One Tap Login Class.
 *
 * This class will be responsible for handling
 * Google's one tap login for web functioning.
 *
 * @package RtCamp\GoogleLogin\Modules
 * @since 1.0.16
 */

declare(strict_types=1);

namespace RtCamp\GoogleLogin\Modules;

use Mockery\Exception;
use RtCamp\GoogleLogin\Utils\Helper;
use RtCamp\GoogleLogin\Interfaces\Module;
use RtCamp\GoogleLogin\Utils\TokenVerifier;
use function RtCamp\GoogleLogin\plugin;

/**
 * Class OneTapLogin
 *
 * @package RtCamp\GoogleLogin\Modules
 */
class OneTapLogin implements Module {
	/**
	 * Settings Module.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
     * Token verifier.
     *
	 * @var TokenVerifier
	 */
	private $token_verifier;

	/**
	 * OneTapLogin constructor.
	 *
	 * @param Settings $settings Settings object.
	 */
	public function __construct( Settings $settings, TokenVerifier $verifier ) {
		$this->settings       = $settings;
		$this->token_verifier = $verifier;
	}

	/**
	 * Module name.
	 *
	 * @return string
	 */
	public function name(): string {
		return 'one_tap_login';
	}

	/**
	 * Module Initialization activity.
	 *
	 * Everything will happen if and only if one tap is active in settings.
	 */
	public function init(): void {
		if ( $this->settings->one_tap_login ) {
			add_action( 'login_enqueue_scripts', [ $this, 'one_tap_scripts' ] );
			add_action( 'login_footer', [ $this, 'one_tap_prompt' ] );
			add_action( 'wp_ajax_nopriv_validate_id_token', [ $this, 'validate_token' ] );
		}
	}

	/**
	 * Show one tap prompt markup.
	 *
	 * @return void
	 */
	public function one_tap_prompt(): void {?>
		<div id="g_id_onload"
		     data-client_id="<?php echo esc_html( $this->settings->client_id ); ?>"
		     data-login_uri="<?php echo wp_login_url(); ?>"
		     data-callback="LoginWithGoogleDataCallBack"
		</div>
		<?php
	}

	/**
	 * Enqueue one-tap related scripts.
     *
     * @return void
	 */
	public function one_tap_scripts(): void {
	    wp_enqueue_script(
	            'login-with-google-one-tap',
            'https://accounts.google.com/gsi/client'
        );

		$data = [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		];

		wp_register_script(
			'login-with-google-one-tap-js',
			trailingslashit( plugin()->url ) . 'assets/build/js/onetap.js',
			[],
			filemtime( trailingslashit( plugin()->path ) . 'assets/build/js/onetap.js' )
		);

		wp_add_inline_script(
		        'login-with-google-one-tap-js',
            'var TempAccessOneTap=' . json_encode( $data ),
            'before'
        );

		wp_enqueue_script( 'login-with-google-one-tap-js' );
    }

	/**
	 * Validate the ID token.
	 *
	 * @return void
	 */
	public function validate_token(): void {
	    try {
		    $token    = Helper::filter_input( INPUT_POST, 'token', FILTER_SANITIZE_STRING );
		    $verified = $this->token_verifier->verify_token( $token );

		    if ( ! $verified ) {
		        throw new Exception( __( 'Cannot verify the credentials', 'login-with-google' ) );
            }

		    wp_send_json_success();
		    die;

	    } catch ( Exception $e ) {
	        wp_send_json_error( $e->getMessage() );
        }
	}
}
