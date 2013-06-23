<?php
if ( ! class_exists( 'DMCA_API_Client' ) ) {
  /**
   * Class DMCA_API_Client
   *
   * @method array|object ()
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

//    $this->add_filter( 'filter_result_body' );
//    $this->add_action( 'prepare_request' );

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

    $this->register_resource( 'anonymous_badges', 		      'path=/GetAnonymousBadges|auth=false|request_settings=' );
    $this->register_resource( 'authenticated_badges',       'path=/GetAuthenticatedBadges' );
    $this->register_resource( 'watermarker_tokens',         'path=/GetWaterMarkerTokens' );
    $this->register_resource( 'watermarker_token',          'path=/GetWaterMarkerToken' );
    $this->register_resource( 'watermarker_pro_token',      'path=/GetWaterMarkerProToken' );

    $this->register_settings( 'post_json', 'method=POST|content_type=json|charset=utf-8' );

    $this->register_service_defaults( array(
      'request_settings'=> 'post_json'
    ));

    $this->register_action( 'authenticate', 			  		    'path=/GetAuthenticatedBadges|!has_body' );
    $this->register_action( 'register', 				 	  	      'path=/RegisterNewAccount|auth=false' );
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

  function filter_result_body( $body, $response ) {
    $body = str_replace( array( '&#xD;', '&#xA;', '&lt;', '&gt;' ), array( '', '', '<', '>' ), $body );
    return $body;
  }

  /**
   * @param RESTian_Request $request
   */
  function prepare_request( $request ) {
    switch ( $request->service->service_name ) {
      case 'authenticate':
        $request->add_header( 'Content-type', 'application/json; charset=utf-8' );
        $credentials = $request->get_credentials();
        $request->body = json_encode( array(
          'Email'     => $credentials['email'],
          'Password'  => $credentials['password'],
        ));
        break;
    }
  }
  function get_anonymous_badges() {
    $result = parent::get_anonymous_badges();
    return $result && isset( $result['a'] ) && is_array( $result['a'] ) ? $result['a'] : false;
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

//      $this->register_service_defaults( array(
//        'content_type'  => 'application/xml',
//        'not_vars' => array(
//          'content_type'  => 'all',
//          'search_terms'  => true,
//          'has_body'      => true
//         )
//        )
//      );
//
//      $this->register_var( 'content_id', 		'usage=path|type=number' );
//      $this->register_var( 'mediabox_id', 	'usage=path|type=number' );
//      $this->register_var( 'search_terms', 	'usage=path|type=string|transforms=fill[/]' );
//      $this->register_var( 'rpp', 					'usage=query|max=500|min=1|default=10|type=number' );
//      $this->register_var( 'order', 				'usage=query|options=date,downloads,editors_choice|type=string' );
//      $this->register_var( 'producer_id', 	'usage=both|type=number' );
//      $this->register_var( 'type', 					'usage=both|type=string|options=audio,video,ae,motion|not_vars=type:all' );
//
//      $this->register_var_set( 'search_vars', 'producer_id,rpp' );
//      $this->register_var_set( 'content_vars', 'producer_id,rpp,type' );
//
//      $this->register_action( 'authenticate', 								'path=/content/0|!has_body' );
//
//      $this->register_action( 'search_content_items', 				"path=/search/{search_terms}|var_set=search_vars" );
//      $this->register_action( 'search_typed_content_items', 	"path=/search-{type}/{search_terms}|var_set=search_vars" );
//
//      $this->register_resource( 'new_content_items', 					"path=/new|var_set=search_vars" );
//      $this->register_resource( 'new_typed_content_items', 		"path=/new/{type}|var_set=search_vars" );
//
//      $this->register_resource( 'mediabox_content_items', 		"path=/mediabox-content/{mediabox_id}|var_set=content_vars" );
//      $this->register_resource( 'producer_content_items', 		"path=/producer-content/{producer_id}|var_set=content_vars" );
//
//      $this->register_resource( 'content_item', 							"path=/content/{content_id}" );
//
//    /**
//     * @param       $search_terms
//     * @param array $vars
//     *
//     * @return object|RESTian_Response
//     */
//    function search_content_items( $search_terms, $vars = array() ) {
//      $args['search_terms'] = $search_terms;
//      if ( isset( $vars['type'] ) && 'all' != $vars['type'] ) {
//        $action_name = 'search_typed_content_items';
//      } else {
//        $action_name = 'search_content_items';
//        unset( $vars['type'] );
//      }
//      return $this->invoke_action( $action_name, $vars );
//    }
//
//    /**
//     * @param array $vars
//     * @param array|object $args
//     *
//     * @return object|RESTian_Response
//     */
//    function get_new_content_items( $vars = array(), $args = array() ) {
//      if ( isset( $vars['type'] ) && 'all' != $vars['type'] ) {
//        $resource_name = 'new_typed_content_items';
//      } else {
//        $resource_name = 'new_content_items';
//      }
//      if ( empty( $vars['group'] ) && empty( $vars['order'] ) )
//        $vars['order'] = 'date';
//      return $this->get_resource( $resource_name, $vars, $args );
//    }
//
//    /**
//     * @param string $group newest, most_downloaded, editors_choice
//     * @param array $vars
//     * @param array|object $args
//     *
//     * @return object|RESTian_Response
//     */
//    function get_group_content_items( $group, $vars = array(), $args = array() ) {
//      $vars['order'] = $this->_translate_group_to_order( $group );
//      unset( $vars['group'] );
//      return $this->get_new_content_items( $vars, $args );
//    }
//
//    /**
//     * @param string $group newest, most_downloaded, editors_choice
//     *
//     * @return string For 'order': date, downloaded, and editors_choice, respectively.
//     */
//    private function _translate_group_to_order( $group ) {
//      $order = $group;
//      switch ( $group ) {
//        case 'newest':
//          $order= 'date';
//          break;
//        case 'most_downloaded':
//          $order= 'downloads';
//          break;
//      }
//      return $order;
//    }
//
//    /**
//     * @param       $mediabox_id
//     * @param array $vars
//     * @param array|object $args
//     *
//     * @return object|RESTian_Response
//     */
//    function get_mediabox_content_items( $mediabox_id, $vars = array(), $args  = array() ) {
//      $args['mediabox_id'] = $mediabox_id;
//      return $this->get_resource( 'mediabox_content_items', $vars, $args );
//    }
//
//    /**
//     * @param       $producer_id
//     * @param array $vars
//     * @param array|object $args
//     *
//     * @return object|RESTian_Response
//     */
//    function get_producer_content_items( $producer_id, $vars = array(), $args  = array() ) {
//      $vars['producer_id'] = $producer_id;
//      if ( empty( $vars['group'] ) && empty( $vars['order'] ) )
//        $vars['order'] = 'date';
//      return $this->get_resource( 'producer_content_items', $vars, $args );
//    }
//
//    /**
//     * @param $content_id
//     * @param array|object $args
//     * @return object|RESTian_Response
//     */
//    function get_content_item( $content_id, $args  = array() ) {
//      return $this->get_resource( 'content_item', array( 'content_id' => $content_id ), $args );
//    }
//
//    /**
//     * Subclass RESTian to allow filtering $vars
//     *
//     *   Any 'group' specificed gets translated to the appropriate value for 'order'
//     *   If 'type' == 'all' then it is removed from the $vars array.
//     *
//     * @param string|RESTian_Service $resource_name
//     * @param array $vars
//     * @param array|object $args
//     * @return object|RESTian_Response
//     */
//    function get_resource( $resource_name, $vars = null, $args = null ) {
//      if ( ! empty( $vars['group'] ) )
//        $vars['order'] = $this->_translate_group_to_order( $vars['group'] );
//      if ( isset( $vars['type'] ) && 'all' == $vars['type'] )
//        unset( $vars['type'] );
//      return parent::get_resource( $resource_name, $vars, $args );
//    }
}
