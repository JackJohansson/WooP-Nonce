<?php
/**
 * PhpUnit test package file.
 *
 * This file belongs to the WoopNonce package.
 *
 * (c) Jack Johansson
 *
 */
namespace WoopNonce\Test;

use WoopNonce;

class WoopNonceTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test the nonce generation method without passing
	 * any arguments. The output should be string.
	 *
	 */
	public function testNonceGenerationMethodWithNoArgs(){

		$object = new WoopNonce\Nonce();

		$nonce = $object->GenerateNonce();

		$this->assertTrue( is_string ( $nonce ) );

	}

	/**
	 * Test the URL type nonce generation. Let's pass a valid
	 * URL and see if the output is a string.
	 *
	 */
	public function testNonceGenerationMethodUrlType(){


		$object = new WoopNonce\Nonce();

		$nonce = $object->GenerateNonce( [ 'type' => 'url', 'url' => 'http://google.com' ] );

		$this->assertTrue( is_string ( $nonce ) );

	}

	/**
	 * Test the URL type nonce generation. Let's pass a invalid
	 * URL. The output should be NULL.
	 *
	 */
	public function testNonceGenerationMethodInvalidUrlType(){


		$object = new WoopNonce\Nonce();

		$nonce = $object->GenerateNonce( [ 'type' => 'url', 'url' => 'invalidurl' ] );

		$this->assertTrue( is_null ( $nonce ) );

	}

	/**
	 * Test the field type nonce generation. The output
	 * must be a string.
	 *
	 */
	public function testNonceGenerationMethodFieldType(){


		$object = new WoopNonce\Nonce();

		$nonce = $object->GenerateNonce( [ 'type' => 'field' ] );

		$this->assertTrue( is_string ( $nonce ) );

	}

	/**
	 *
	 * Let's see what happens if we pass an invalid
	 * nonce type. The result should be a valid nonce
	 * string.
	 *
	 */
	public function testNonceGenerationMethodInvalidType(){


		$object = new WoopNonce\Nonce();

		$nonce = $object->GenerateNonce( [ 'type' => 'some-random-type' ] );

		$this->assertTrue( is_string ( $nonce ) );

	}

	/**
	 * We try and test if the object type nonce works.
	 *
	 */
	public function testNonceGenerationMethodObjectOutput(){


		$object = new WoopNonce\Nonce();

		$nonce = $object->GenerateNonce( [ 'type' => 'plain', 'object' => true ] );

		$this->assertTrue( is_object ( $nonce ) );

	}

	/**
	 * Let's try and create multiple nonces in
	 * the object mode.
	 *
	 */
	public function testNonceGenerationMethodObjectOutputMultipleNonces(){


		$object = new WoopNonce\Nonce();

		$nonce = $object->GenerateNonce( [ 'type' => 'plain', 'object' => true, 'count' => 4 ] );

		$this->assertTrue( is_object ( $nonce ) );

	}

	/**
	 *
	 * Pass an invalid input to every arguemnt. The result should
	 * be a valid single nonce string.
	 *
	 */
	public function testNonceGenerationMethodAllInvalidArgs(){


		$object = new WoopNonce\Nonce();

		$nonce = $object->GenerateNonce( $args = [ 'type' => 'random-invalid', 'action' => '', 'name' => 123, 'referer' => 'invalid', 'object' => 'invalid', 'count' => -5, 'url' => '' ] );

		$this->assertTrue( is_string ( $nonce ) );

	}

	/**
	 * Verify a given nonce. If the test fails, the nonce
	 * should be invalid.
	 *
	 */
	public function testSimpleNonceVerification(){

		$object = new WoopNonce\Nonce();

		$nonce = $object->GenerateNonce();

		$verification = $object->VerifyNonce( $nonce );

		$this->assertTrue( is_int ( $verification ) );

	}

	/**
	 *
	 * Get an ajax referer. This is a simple field output.
	 *
	 */
	public function testGetNonceReferer(){

		$object = new WoopNonce\Nonce();

		$referer = $object->GetReferer();

		$this->assertTrue( is_string ( $referer ) );

	}

	/**
	 *
	 * Test the status of nonce life time filter set
	 * by the 'nonce_life'.
	 *
	 */
	public function testCheckNonceLifeTimeFilter(){

		$options = new WoopNonce\NonceOptions( $args = [ 'nonce_life' => 60, 'message' => 'UnitTest Message' ] );

		$filterStatus = $options->NonceLife();

		$this->assertTrue( $filterStatus );

	}

	/**
	 * Check the nonce message filter status set by
	 * the 'gettext' filter.
	 *
	 */
	public function testCheckNonceAreYouSureMessageFilter(){

		$options = new WoopNonce\NonceOptions( $args = [ 'nonce_life' => 60, 'message' => 'UnitTest Message' ] );

		$filterStatus = $options->NonceMessage();

		$this->assertTrue( $filterStatus );

	}

	/**
	 * Get the value passed by the nonce life time
	 * method. It should be same as what we passed to.
	 *
	 */
	public function testReturnedNonceLifeTime() {

		$options = new WoopNonce\NonceOptions( $args = [ 'nonce_life' => 60, 'message' => 'UnitTest Message' ] );

		$lifetime = $options->ReturnNonceLife();

		$this->assertEquals( $args['nonce_life'] , $lifetime );

	}

	/**
	 * Get the value pass by the nonce message string.
	 * It should be the same as what we passed to it.
	 *
	 */
	public function testReturnedNonceAreYouSureMessage() {

		$options = new WoopNonce\NonceOptions( $args = [ 'nonce_life' => 60, 'message' => 'UnitTest Message' ] );

		$message = $options->ReturnTranslation( $args['message'] );

		$this->assertEquals( $args['message'] , $message );

	}

}