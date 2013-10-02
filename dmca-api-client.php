<?php
/**
 * DMCA API Client - Provides a PHP 5.2+ class for interfacing with the API at DMCA.com.
 *
 * @version 0.1
 *
 * @requires RESTian v0.4+
 * @see https://github.com/newclarity/restian/
 *
 * @copyright Copyright (c) 2013, NewClarity LLC
 * @author Mike Schinkel <mike@newclarity.net>
 * @license GPLv2
 *
 */

if ( ! class_exists( 'RESTian_Client' ) ) {
	echo 'ERROR: DMCA API Client requires RESTian v0.4+. See https://github.com/newclarity/restian.';
  die();
}

require( dirname( __FILE__ ) . '/classes/class-api-client.php');
