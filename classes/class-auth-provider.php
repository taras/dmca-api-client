<?php

class DMCA_Auth_Provider extends RESTian_Auth_Provider_Base {

  /**
   * @param RESTian_Response $response
   *
   * @return bool
   */
  function authenticated( $response ) {
    return ! preg_match( '#>Login failed<#', $response->body );
  }

  /**
   * @return array
   */
  function get_new_credentials() {
    return array(
      'email' => '',
      'password' => '',
    );
  }

  /**
   * @return array
   */
  function get_new_grant() {
    return array(
      'authenticated' => false,
    );
  }

  /**
   * @param array $credentials
   * @return bool
   */
  function is_credentials( $credentials ) {
    return ! empty( $credentials['email'] ) && ( ! empty( $credentials['password'] ) );
  }

  /**
   * @param array $grant
   * @return bool
   */
  function is_grant( $grant ) {
    return ! empty( $grant['authenticated'] );
  }


}
