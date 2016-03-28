# [INACTIVE] Codeigniter SmartyStreets.com LiveAddress API Integration

> **Warning:** This repo is no longer being mantianed. Use at your own risk.

Add address cleaning and verification to a Codeigniter site with this handy little lib.

Requires [Philip Sturgeon's](http://philsturgeon.co.uk/) [cURL lib](http://getsparks.org/packages/curl/show) (included).

Installation
------------

1. Copy the /config/smartystreets.php file into your application's config/ folder and make sure to paste in your own auth-token!
2. Copy /libraries/Smartystreets.php and /libraries/Curl.php into your application's libraries/ folder.

Usage
-----

This library will allow you to send an address to SmartyStreets.com to be verified by their database. If the address can be verified, it will be cleaned up with standard spelling and returned. If the address is not valid, an empty data set will be returned.

### Initialization

First and foremost, you must load the library of course:

	$this->load->library('smartystreets');

And as with most libraries, you can send in an array of config data.

### Send an address

Set up an associative array with the address in question.

	$send_address = array(
			'candidates' => 4, // Number of possible matches to get back (1-10)
			'street' => '3200 TATES CREEK ROAD',
			'street2' => '',
			'city' => 'LEXINGTON',
			'state' => 'KY',
			'zipcode' => '40502',
			);

Then send the array to the library.

	$this->smartystreets->setData($send_address);

And then make a request.

	$this->smartystreets->send()

The send() method will return TRUE on success or FALSE on failure. On success, the response array can be accessed like so:

	$res = $this->smartystreets->getResponse();

On failure, any error messages can be accessed like so:

	$error = $this->smartystreets->getError();

### See it in action

To see a full example in action, check out /controllers/example.php

Enjoy!
[SammyK](http://sammyk.me/)
