<?php

	// set variable to WSDL link to use elsewhere easily
	$wsdl = "http://www.restfulwebservices.net/wcf/StockQuoteService.svc?wsdl";

	// create a SoapClient object
	$client = new SoapClient($wsdl);

	// store value as variable to be passed into the 'request' tag in the XML document
	$stock = "NCR";
    
    // make an associative array and map 'request' to our previously established value (NCR)
    // HAS TO MATCH TAGS OF ELEMENT IN SOAP REQUEST (in this case 'request' //
    $parameters= array("request"=>$stock);
    
    // sending request to soap server, retrieves XML, sifts through and finds method 'GetStockQuote', passes "NCR" into <result> tag and gets data back from soap server, saving it as $value
    $values = $client->GetStockQuote($parameters);

    // use method 'GetStockQuoteResult' to return data as a stdClass Object with attributes
    $xml = $values->GetStockQuoteResult;
    
    // print out XML data for request and response, print_r prints 'human-readable' information about an object, <pre> provides preformatting, i.e. new line (\n) after each bit of data
    print "<pre>\n";
    print_r($xml);
    print "</pre>";

    // store data from XML document that is referenced by 'Last'
    $currentprice = $xml->Last;
    
    // print ONLY this bit of data from the object
    print "<br />\n Last Value: $currentprice";

?>