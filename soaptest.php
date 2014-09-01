<?php

	// set variable to WSDL link to use elsewhere easily
	$wsdl = "http://msrmaps.com/TerraService2.asmx?WSDL";

    // for tracking errors
    $trace = true;
    $exceptions = true;

    // create new Soap Client object with relevant params
    $client = new SoapClient ($wsdl, array(
                                     'trace' => $trace,
                                     'exceptions' => $exceptions
                                          )
                             );

    // create data array to send in SOAP request params
    $xml_array['placeName'] = 'Pomona';
    $xml_array['MaxItems'] = 3;
    $xml_array['imagePresence'] = true;

    // try to send the request to the web service
    try {
        $response = $client->GetPlaceList($xml_array);
    } catch (Exception $e) {
        echo "Error!";
        echo $e -> getMessage();
        echo 'Last response: '.$client->__getLastResponse();
    }

    // display results
    print "<pre>\n";
    print_r($response);
    print "</pre>";

?>