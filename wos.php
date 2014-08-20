<?php

    // arogozin@nyu.edu

    // set WSDL for authentication and create new SOAP client
    $auth_url  = "http://search.webofknowledge.com/esti/wokmws/ws/WOKMWSAuthenticate?wsdl";
    // array options are temporary and used to track request & response data in printout below (line 25)
    $auth_client = @new SoapClient($auth_url, array(
                     "trace" => 1,
                     "exceptions" => 0));
    // run 'authenticate' method and store as variable
    $auth_response = $auth_client->authenticate();

    // set WSDL for search and create new SOAP client
    $search_url = "http://search.webofknowledge.com/esti/wokmws/ws/WokSearch?wsdl";
    // array options are temporary and used to track request & response data in printout below (line 58)
    $search_client = @new SoapClient($search_url, array(
                     "trace" => 1,
                     "exceptions" => 0));
    // call 'setCookie' method on '$search_client' storing SID (Session ID) as the response (value) given from the 'authenticate' method
    $search_client->__setCookie('SID',$auth_response->return);

    // print details of XML request and response data for Authentication exchange
    print "<pre>\n";
    print "<br />\n Request : ".htmlspecialchars($auth_client->__getLastRequest());
    print "<br />\n Response: ".htmlspecialchars($auth_client->__getLastResponse());
    print "</pre>";

    // pass in relevant parameters for search
    $search_array = array(
        'queryParameters' => array(
            'databaseId' => 'WOS',
            'userQuery' => $_POST["type"].'='.$_POST["category"],
            'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
            'queryLanguage' => 'en'
        ),
        'retrieveParameters' => array(
            'count' => '5',
            'sortField' => array(
                array('name' => $_POST["sort"], 'sort' => 'D')
            ),
            'firstRecord' => '1'
        )
    );

    /* POPULATE ARRAY VARIABLE WITH <uid> VALUES FROM $search_array INTO $retrieve_array */

    // pass in parameters for retrieveById
    /*$retrieve_array = array(
        'databaseId' => 'WOS',
        'uid' => 'WOS:A1993LC48100001 WOS:A1993LE28400012',
        'queryLanguage' => 'en',
        'retrieveParameters' => array(
            'count' => '10',
            'firstRecord' => '1'
        )
    ); */

    // try to store as a variable the 'search' method on the '$search_array' called on the SOAP client with associated SID 
    try {
        $search_response = $search_client->search($search_array);
    } catch (Exception $e) {  
        echo $e->getMessage(); 
    };

    /* try {
        $retrieve_response = $search_client->retrieveById($retrieve_array);
    } catch (Exception $e) {  
        echo $e->getMessage(); 
    }; */

    echo "</br>SEARCH_RESPONSE: </br>";
    print "<pre>\n";
    print_r($search_response);
    print "</pre>";

    echo "</br>SEARCH_CLIENT: </br>";
    print "<pre>\n";
    print_r($search_client);
    print "</pre>";

    // echo "</br>Search Response: </br>".$search_response->return->recordsFound;
    // echo "</br>Search Client: </br>".$search_client->_cookies[0];

    /* echo "</br>RETRIEVE_RESPONSE: </br>";
    print "<pre>\n";
    print_r($retrieve_response);
    print "</pre>"; */

    // print details of XML request and response data for Search exchange
    /* print "<pre>\n";
    print "<br />\n Request : ".$search_client->__getLastRequest();
    print "<br />\n Response: ".$search_client->__getLastResponse();
    print "</pre>"; */

    /* echo "</br>var_dump on search client: </br>";
    var_dump($search_client->__last_response); */

    echo "</br>================================================================================================</br>";

    $string = $search_client->__getLastResponse();

    echo "STRING: </br></br>".($string);

    $xml = simplexml_load_string($string);
    $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $xml->registerXPathNamespace('ns2', 'http://woksearch.v3.wokmws.thomsonreuters.com');
    $xml->registerXPathNamespace('xmlns', 'http://scientific.thomsonreuters.com/schema/wok5.4/public/FullRecord');

    foreach ($xml->xpath('//records/REC/UID/text()') as $item) {
        print_r($item);
    };

?>