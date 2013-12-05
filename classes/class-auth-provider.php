<?php

class DMCA_Auth_Provider extends RESTian_Auth_Provider_Base {

  /**
   * @param RESTian_Response $response
   *
   * @return bool
   */
  function authenticated( $response ) {

    $account_id = 0;
    $xml = null;
    $error = null;

    if ( 500 === $response->status_code ) {
      $error = json_decode($response->body);
      $response->set_error('500', $error->Message);
    } else {
      try {
        $xml = new SimpleXMLElement( $response->body );
      } catch (Exception $e) {
        $response->set_error('parsing', $e->getMessage());
      }

      if ( !$response->has_error() ) {
        if ( isset( $xml->a ) ) {
          $a = $xml->a[0];
          $attrs = $a->attributes();
          $href = (string) $attrs->href;
          $params = array();
          $query = parse_url( $href, PHP_URL_QUERY );
          parse_str( $query, $params );
          if ( isset( $params[ 'ID' ] ) ) {
            $account_id = $params[ 'ID' ];
          }
        }
      }
    }

    return $account_id;
  }

  function capture_grant( $response ) {
    $new_grant = $this->get_new_grant();
    $new_grant[ 'AccountID' ] = $response->authenticated;
    $response->grant = $new_grant;
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
    // TODO: change authenticated to AccountID
    return array(
      'AccountID' => '',
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
    return isset( $grant[ 'AccountID' ] ) && !empty( $grant[ 'AccountID' ] );
  }

}
