<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Smartystreets
 *
 * Interface with the SmartyStreets.com LiveAddress API
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author		SammyK (http://sammyk.me/)
 * @link		https://github.com/SammyK/codeigniter-smartystreets-api
 */
class Smartystreets
{
    private $CI;						// CodeIgniter instance

    private $api_auth_token = '';		// API Authentication Token
    private $api_url = '';				// Where we postin' to?
	
    private $post_vals = array();		// Values that get posted to SmartyStreets
	
	/*
	 * If your installation of cURL works without the "CURLOPT_SSL_VERIFYHOST"
	 * and "CURLOPT_SSL_VERIFYPEER" options disabled, then remove them
	 * from the array below for better security.
	 */
    private $curl_options = array(		// Additional cURL Options
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		);
	
    private $response = '';				// Response from SmartyStreets
	
    private $error = '';				// Error to show to the user

	public function __construct( $config = array() )
	{
		$this->CI =& get_instance();
		
		// Load config file
		$this->CI->config->load('smartystreets', TRUE);
		
		foreach( $this->CI->config->item('smartystreets') as $key => $value )
		{
			if( isset($this->$key) )
			{
				$this->$key = $value;
			}
		}

		$this->initialize($config);
	}

	// Initialize the lib
	public function initialize( $config )
	{
		foreach( $config as $key => $value )
		{
			if( isset($this->$key) )
			{
				$this->$key = $value;
			}
		}
	}
	
	// Set the data that we're going to send
	public function setData( $data )
	{
		$this->post_vals = $data;
	}
	
	// Get the values we're going to send
	public function getPostVals()
	{
		$auth_vals = array(
			'auth-token'	=> $this->api_auth_token,
			);
		
		return array_merge($this->post_vals, $auth_vals);
	}
	
	// Send data to API
	public function send()
	{
		// Load cURL lib
		$this->CI->load->library('curl');
		
		/*
		 * Very helpful debugging info if you need it
		 */
		//$f = fopen('request.txt', 'w');
		//$this->curl_options[CURLOPT_VERBOSE] = 1;
		//$this->curl_options[CURLOPT_STDERR] = $f;
		
		//$response = $this->CI->curl->simple_post( // According to documentation, this should work, but doesn't
		$response = $this->CI->curl->simple_get(
				$this->api_url,
				$this->getPostVals(),
				$this->curl_options);
		
		//fclose($f);
		
		return $this->parseResponse($response);
	}
	
	// Parse the response back from Authorize.net
	public function parseResponse( $response )
	{
		if( $response === FALSE )
		{
			// Default error
			$this->error = 'There was a problem while contacting the address verification API. Please try again.';
			
			if( isset($this->CI->curl->info['http_code']) )
			{
				switch( $this->CI->curl->info['http_code'] )
				{
					case '400';
					$this->error = 'Malformed request – required fields missing from request.';
					break;

					case '401';
					$this->error = 'Authentication failure - invalid credentials; check credentials and try again.';
					break;

					case '402';
					$this->error = 'Unauthorized access; no active subscription can be found.';
					break;

					case '500';
					$this->error = 'General service failure, retry request.';
					break;
				}
			}
			
			return FALSE;
		}
		elseif( is_string($response) )
		{
			$res = json_decode($response, TRUE);
			
			if( $res !== NULL && is_array($res) )
			{
				$this->response = $res;
				return TRUE;
			}
		}
		
		$this->error = 'Received an unknown response from the address verification API. Please try again.';
		return FALSE;
	}
	
	// Get the response
	public function getResponse()
	{
		return $this->response;
	}
	
	// Get the error text
	public function getError()
	{
		return $this->error;
	}
	
	// Dump some debug data to the screen
	public function debug( $show_curl_debug = FALSE )
	{
		echo '<h1>SmartyStreets.com API Debug Info</h1>';
		$url = $this->CI->curl->debug_request();
		echo '<h3>URL: ' . $url['url'] . '</h3>';
		
		if( !empty($this->error) )
		{
			echo '<p>' . $this->error . '</p>';
		}
		
		echo '<h1>Send Data</h1>';
		echo '<pre>';
		echo http_build_query($this->getPostVals(), NULL, '&');
		echo '</pre>';
		
		if( isset($this->response) )
		{
			echo '<h1>Response</h1>';
			echo '<pre>';
			print_r($this->response);
			echo '</pre>';
		}
		
		if( $show_curl_debug )
		{
			echo '<h1>cURL Debug Data</h1>';
			$this->CI->curl->debug();
		}
	}
	
	// Reset everything so we can try again
	public function clear()
	{
		$this->response = '';
		$this->error = '';
		$this->post_vals = array();
	}

}

/* EOF */