<?php 
/*
 * This is the core file belonging to the WooP-Nonce package.
 *
 * (c) Jack Johansson
 *
 */
namespace WoopNonce;

/**
 * The Nonce class that handles generating and verifying nonces and referers.
 *
 * @link    https://github.io
 *
 * @since   1.0
 *
 * @package WoopNonce
 */
class Nonce {

	/**
	 * An array of arguments passed by the user to the class.
	 *
	 * Passing the array to the class is optional, and only
	 * intended for advanced uses. A user can contro the
	 * output of the class by defining one of more of the
	 * arguments.
	 *
	 * @since  1.0
	 *
	 * @access private
	 *
	 * @var $default_args []
	 *
	 *           ['type']   :  Possible values plain|url|field. 'plain' will
	 *                         simply output a nonce string, 'url' will add the
	 *                         nonce to the passed URL and 'field' will
	 *                         generate hidden fields to be used in a form.
	 *                         Default 'plain'.
	 *
	 *           ['action'] :  The action's name to be used for generating the
	 *                         nonce. If left blank, the default action will be
	 *                         used. a value of 'rand' can be passed, to
	 *                         generate a random action.
	 *
	 *           ['name']   :  The name of the field. This is useful when
	 *                         generating a field type. Default '_wpnonce'.
	 *
	 *           ['referer']:  Whether the referer field should be output or
	 *                         not. This is intended to be used while
	 *                         generating 'field' type. Default is true.
	 *
	 *           ['count']  :  The number of nonces to create. Only works for
	 *                         plain type.
	 *
	 *           ['object]  :  The output type of the GenerateNonce() method.
	 *                         By default, the output is a string, but advanced
	 *                         users can set it to true, to get an object in
	 *                         the output. Default false.
	 *
	 *           ['url']    :  The URL to create a URL nonce. If none of
	 *                         invalid URL is provided, a NULL will be returned
	 *                         as the result.
	 */
	private $default_args = [
		'type'    => 'plain',
		'action'  => - 1,
		'name'    => '_wpnonce',
		'referer' => true,
		'object'  => false,
		'count'   => 1,
		'url'     => '',
	];

	/**
	 * Public method to generate a nonce based on user's
	 * input. The input arguments are explained above.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @param array $args
	 *
	 * @return null|object|string
	 */
	public function GenerateNonce( $args = [] ) {

		/**
		 * Generate a random action if the action is set to 'rand'. We use
		 * the wp_generate_password() function to generate a random action.
		 * We also want to make sure the action is passed to the user, to
		 * be used later ( maybe in a transient ) , so we additionally check
		 * the output type.
		 *
		 */

		if ( $args['action'] == 'rand' && $args['object'] === true ) {

			$args['action'] = wp_generate_password( 12, false );

		}

		/**
		 * Loop through the passed arguments, and check
		 * which one of them is set. If a value is
		 * set, then override the default value.
		 *
		 */
		foreach ( $this->default_args as $key => $arg ) {

			if ( isset( $args[ $key ] ) ) {
				$this->default_args[ $key ] = $args[ $key ];
			}

		}

		// Set a local variable to use in our switch
		$args = $this->default_args;

		/**
		 * A switch to output the proper type of nonce
		 * based on the passed arguments. If none or invalid
		 * argument is passed, then it will fallback to the
		 * default plain.
		 *
		 */
		switch ( $args['type'] ) {

			// Render a nonce field to be used in a form
			case 'field':

				$nonce = wp_nonce_field( $args['action'], $args['name'], $args['referer'], false );

				break;

			// Append a nonce to the URL, if this is the type
			case 'url':

				// First, check if the URL is valid. If not, return NULL.
				if (
					empty( $args['url'] ) ||
					! filter_var( $args['url'], FILTER_VALIDATE_URL )
				) {
					$nonce = null;
					break;
				}

				// Sanitize, trailing slash and create the nonce.
				$nonce = wp_nonce_url( trailingslashit( esc_url( $args['url'] ) ), $args['action'], $args['name'] );

				break;

			// This is the fallback switch, which will run if the type is set to
			// an invalid type or to 'plain'.
			default:

				// Check the count argument
				if ( $args['count'] > 1 ) {

					for ( $i = 0; $i < (int) $args['count']; $i ++ ) {

						// Create a nonce based on a random action
						$nonce[ $i ]['nonce']  = wp_create_nonce( $rand_action = wp_generate_password( 12, false ) );
						$nonce[ $i ]['action'] = $rand_action;
					}

				} else {
					$nonce = wp_create_nonce( $args['action'] );
				}

				break;
		}

		// If the user wants the output as an object, return an object instead.
		if ( $args['object'] === true ) {

			// Create an empty object
			$object = new \stdClass();

			// Check how many nonces did we create
			if ( $args['count'] > 1 ) {

				// Run a loop and generate data for each nonce
				foreach ( $nonce as $key => $value ) {

					// Create a sub-object for each nonce
					$object->$key = new \stdClass();

					// Fill the nonce data into the object
					$object->$key->nonce   = $value ['nonce'];
					$object->$key->action  = $value ['action'];
					$object->$key->referer = $_SERVER['REQUEST_URI'];

				}

			} else {

				// Add the single nonce data to the object
				$object->nonce   = $nonce;
				$object->action  = $args['action'];
				$object->name    = $args['name'];
				$object->referer = $_SERVER['REQUEST_URI'];
			}

			// Return the object
			return $object;

		}

		// Return the nonce
		return $nonce;
	}

	/**
	 * Verify a given nonce, and return the status.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @param     $nonce
	 * @param int $action
	 *
	 * @return false|int
	 */
	public function VerifyNonce( $nonce, $action = - 1 ) {

		return wp_verify_nonce( $nonce, $action );

	}

	/**
	 * Verify the referer to be used in AJAX requests.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @param int  $action
	 * @param bool $query_arg
	 * @param bool $die
	 *
	 * @return false|int
	 */
	public function VerifyReferrer( $action = - 1, $query_arg = false, $die = true ) {

		return check_ajax_referer( $action, $query_arg, $die );

	}

	/**
	 * Verify the referer passed in an admin screen.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @param int  $action
	 * @param bool $query_arg
	 *
	 * @return false|int
	 */
	public function VerifyAdminReferer( $action = - 1, $query_arg = false ) {

		return check_admin_referer( $action, $query_arg );
	}


	/**
	 * Return the referrer. If the nonce output type is set to
	 * object, this is already output as a part of the object.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function GetReferer() {

		return wp_referer_field( false );

	}

}

class NonceOptions {

	/**
	 * Property to hold the lifetime of the nonce. The lifetime
	 * is measured in seconds.
	 *
	 * @since  1.0
	 *
	 * @access private
	 *
	 * @var mixed
	 */
	private $life;

	/**
	 * Property to hold the message passed by the wp_nonce_ays().
	 *
	 * @since  1.0
	 *
	 * @access private
	 *
	 * @var mixed
	 */
	private $message;

	/**
	 * NonceOptions constructor. An argument containing two options can be
	 * passed to this function. 'nonce_life' is used to set the lifetime
	 * of a nonce, and 'message' to override the default message of the
	 * wp_nonce_ays() function.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @param array $args
	 */
	public function __construct(
		$args = [
			'nonce_life' => DAY_IN_SECONDS,
			'message'    => 'Are you sure you want to do this?',
		]
	) {

		// Set the lifetime property's method
		$this->life = $args['nonce_life'];

		// Set the message property's method
		$this->message = $args['message'];

		// Actually set the lifetime
		$this->NonceLife();

		// Actually set the message
		$this->NonceMessage();

	}

	/**
	 * Filter the default nonce lifetime.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function NonceLife() {

		add_filter( 'nonce_life', [ $this, 'ReturnNonceLife' ] );

	}


	/**
	 * Filter the default message of the wp_nonce_ays() function.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function NonceMessage() {

		add_filter( 'gettext', [ $this, 'ReturnTranslation' ] );

	}


	/**
	 * Return the current lifetime of the nonce. This can be used both
	 * locally and by the user.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @return string|int
	 */
	public function ReturnNonceLife() {

		return $this->life;

	}

	/**
	 * Return the current translation message. This can be used both locally
	 * and by the user.
	 *
	 * @since  1.0
	 *
	 * @access public
	 *
	 * @param $translation
	 *
	 * @return mixed
	 */
	public function ReturnTranslation( $translation ) {

		if ( $translation == 'Are you sure you want to do this?' ) {

			return $this->message;
		}

		return $translation;
	}

}