<?php

require( "{$_SERVER['HOME']}/Libraries/restian/restian.php" );
require( dirname( dirname( __FILE__ ) ) . '/dmca-api-client.php' );

/**
 * This email has been registered and it's password set as below.
 */
define( "DMCA_ACCOUNT_EMAIL", "dmca-api-test@newclarity.net" );
define( "DMCA_ACCOUNT_PASSWORD", "abc123" );

/**
 * If WP_DIR is set in CLI environment then it will use the WordPress HTTP Agent.
 */
if ( $wp_dir = getenv("WP_DIR") ) {
  chdir( $wp_dir );
  require( "{$wp_dir}/wp-load.php" );
}

/**
 * Class DMCA_API_ClientTest
 */
class DMCA_API_ClientTest extends PHPUnit_Framework_TestCase {
  /**
   * @var DMCA_API_Client
   */
  var $api;

  public function setup() {
    $this->api = new DMCA_API_Client();
  }

  /**
   *
   */
  function testGetAnonymousBadges() {
    $badges = $this->api->get_anonymous_badges();
    $this->assertTrue( is_array( $badges ) );
  }

  /**
   *
   */
  function testRegister() {
    $email = "dmca-api-tester-" . rand( 1000000, 9999999 ) . '@newclarity.net';
    $response = $this->api->register( array(
      'first_name'    => 'DMCA API',
      'last_name'     => 'Tester',
      'company_name'  => 'NewClarity LLC',
      'email'         => $email,
    ));
    if ( $response->is_error() ) {
      $error = $response->get_error();
    } else {
      $result = "\nEmail: {$email} registered.";
    }
    $this->setResult( isset( $error ) ? $error : $result );
    $this->assertFalse( $response->is_error() );
  }
  /**
   *
   */
  function testAuthenticate() {
    $response = $this->api->authenticate( array(
      "email"     => DMCA_ACCOUNT_EMAIL,
      "password"  => DMCA_ACCOUNT_PASSWORD
    ));
    $this->assertTrue( $response->authenticated );
  }

}

