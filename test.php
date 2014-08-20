<?php

	$client = new SoapClient("http://www.xmethods.net/sd/2001/DemoTemperatureService.wsdl");

	// return list of available functions from this Web Service
	print "<pre>\n";
	print_r($client->__getFunctions());
	print "</pre>";

	print $client->getTemp("12345");

?>