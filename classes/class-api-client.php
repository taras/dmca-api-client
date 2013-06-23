<?php
if ( ! class_exists( 'DMCA_API_Client' ) ) {
  /**
   * Class DMCA_API_Client
   *
   * @author Mike Schinkel <mike@newclarity.net>
   * @license GPLv2
   *
   * @requires RESTian v4.0+
   * @see https://github.com/newclarity/restian/
   *
   */
  class DMCA_API_Client extends RESTian_Client {
      /**
       *
       */
    function initialize() {

      $this->add_filter( 'result_body' );
      $this->add_action( 'prepare_request' );

      $this->api_name = 'DMCA.com API';
      $this->base_url = 'https://www.dmca.com/rest';
      $this->api_version = '1.0.0';
      $this->use_cache = false;

      RESTian::register_auth_provider( 'dmca_auth', 'DMCA_Auth_Provider', dirname( __FILE__ ) . '/class-auth-provider.php' );
      $this->auth_type = 'dmca_auth';

      $this->register_service_defaults( array(
        'content_type'    => 'application/xml',
        'not_vars'        => array(),
      ));

  //      $this->register_var( 'FirstName',   'usage=json|type=string' );
  //      $this->register_var( 'LastName',    'usage=json|type=string' );
  //      $this->register_var( 'CompanyName', 'usage=json|type=string' );
  //      $this->register_var( 'Email', 		  'usage=json|type=string' );
  //      $this->register_var( 'Password',    'usage=json|type=string' );

      $this->register_resource( 'anonymous_badges',           'path=/GetAnonymousBadges|auth=false|request_settings=' );
      $this->register_resource( 'authenticated_badges',       'path=/GetAuthenticatedBadges' );
      $this->register_resource( 'watermarker_tokens',         'path=/GetWaterMarkerTokens' );
      $this->register_resource( 'watermarker_token',          'path=/GetWaterMarkerToken' );
      $this->register_resource( 'watermarker_pro_token',      'path=/GetWaterMarkerProToken' );

      $this->register_settings( 'post_json', 'method=POST|content_type=json|charset=utf-8' );

      $this->register_service_defaults( array(
        'request_settings'=> 'post_json'
      ));

      $this->register_action( 'authenticate',                 'path=/GetAuthenticatedBadges|!has_body' );
      $this->register_action( 'register',                     'path=/RegisterNewAccount|auth=false' );
      $this->register_action( 'create_watermarker_token',     'path=/CreateWaterMarkerToken' );
      $this->register_action( 'update_watermarker_token',     'path=/UpdateWaterMarkerToken' );
      $this->register_action( 'delete_watermarker_token',     'path=/DeleteWaterMarkerToken' );
      $this->register_action( 'create_watermarker_pro_token', 'path=/CreateWaterMarkerProToken' );
      $this->register_action( 'update_watermarker_pro_token', 'path=/UpdateWaterMarkerProToken' );
      $this->register_action( 'delete_watermarker_pro_token', 'path=/DeleteWaterMarkerProToken' );
      $this->register_action( 'reset_watermarker_token',      'path=/ResetWaterMarkerToken' );
    }

    function register( $args ) {
      /**
       * @var RESTian_Http_Agent_Base $http_agent
       */
      if ( ! $this->validate_email( $args['email'] ) ) {
        $response = new RESTian_Response( array(
          'authenticated' => false,
        ));
        $response->set_http_error( '400', 'Bad Request' );
        $response->set_error( '100', "Email not valid: {$args['email']}" );
      } else {
        $fields = explode( '|', 'first_name|last_name|company_name|email' );
        $args = array_merge( array_fill_keys( $fields, false ), $args );
        $response = $this->invoke_action( 'register', array(
          'FirstName'   => $this->sanitize_string( $args['first_name'] ),
          'LastName'    => $this->sanitize_string( $args['last_name'] ),
          'CompanyName' => $this->sanitize_string( $args['company_name'] ),
          'Email'       => $this->sanitize_email( $args['email'] ),
        ));
      }
      return $response;
    }

    protected function _result_body( $body, $response ) {
      $body = str_replace( array( '&#xD;', '&#xA;', '&lt;', '&gt;' ), array( '', '', '<', '>' ), $body );
      return $body;
    }

    /**
     * @param RESTian_Request $request
     *
     * This should not be needed once we update RESTian to have the ability to "pass" vars in the request body.
     */
    protected function _prepare_request( $request ) {
      switch ( $request->service->service_name ) {
        case 'authenticate':
          $credentials = $request->get_credentials();
          $request->body = json_encode( array(
            'Email'     => $this->sanitize_email( $credentials['email'] ),
            'Password'  => $credentials['password'],
          ));
          break;
      }
    }

    /**
     * Returns a list of badge URLs as an array.
     *
     * @return bool|array
     */
    function get_anonymous_badges() {
      $badges = false;
      $data = parent::get_anonymous_badges();
      if ( $data && isset( $data['a'] ) && is_array( $data['a'] ) ) {
        $badges = array();
        foreach( $data['a'] as $badge ) {
          if ( isset( $badge->img->{'@attributes'}['src'] ) )
            $badges[] = $badge->img->{'@attributes'}['src'];
        }
      }
      return $badges;
    }

    function get_authenticated_badges() {
      return false;
    }
    function register_new_account() {
      return false;
    }
    function create_watermarker_token() {
      return false;
    }
    function create_watermarker_pro_token() {
      return false;
    }
    function get_watermarker_token() {
      return false;
    }
    function get_watermarker_tokens() {
      return false;
    }
    function delete_watermarker_token() {
      return false;
    }
    function update_watermarker_token() {
      return false;
    }
    function update_watermarker_pro_token() {
      return false;
    }
    function reset_watermarker_token() {
      return false;
    }
  }

}
