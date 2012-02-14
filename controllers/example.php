<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Example extends CI_Controller
{	
	function __construct()
	{
		parent::__construct();
	}
	
	// Look up address
	function lookup()
	{
		// Load the SmartyStreets lib
		$this->load->library('smartystreets');
		
		echo '<h1>Looking Up Address</h1>';
		
		$send_address = array(
			'candidates' => 4, // Number of possible matches to get back
			'street' => '3200 TATES CREEK ROAD',
			'street2' => '',
			'city' => 'LEXINGTON',
			'state' => 'KY',
			'zipcode' => '40502',
			);
		
		$this->smartystreets->setData($send_address);
		
		// Send request
		if( $this->smartystreets->send() )
		{
			echo '<h2>Success!</h2>';
			
			echo '<h3>Before</h3>';
			echo '<p>';
			echo $send_address['street'] . "<br>\n";
			if( !empty($send_address['street2']) )
			{
				echo $send_address['street2'] . "<br>\n";
			}
			echo $send_address['city'] . ', ' . $send_address['state'] . ' ' . $send_address['zipcode'] . "<br>\n";
			echo '</p>';

			$res = $this->smartystreets->getResponse();
			
			if( isset($res[0]) )
			{
				echo '<h3>After</h3>';
				echo '<p>';
				echo $res[0]['delivery_line_1'] . "<br>\n";
				if( isset($res[0]['delivery_line_2']) )
				{
					echo $res[0]['delivery_line_2'] . "<br>\n";
				}
				echo $res[0]['components']['city_name'] . ', ' . $res[0]['components']['state_abbreviation'] . ' ' . $res[0]['components']['zipcode'] . "<br>\n";
				echo '</p>';
			}
		}
		else
		{
			echo '<h2>Epic Fail!</h2>';
			echo '<p>' . $this->smartystreets->getError() . '</p>';
		}
		
		// Show debug data
		$this->smartystreets->debug();
	}
	
}

/* EOF */