<?php

    // ====================================================== //
    // ==== TO RETURN THE NUMBER OF RECORDS FOR THE ========= //
    // ==== ENTERED SEARCH PARAMETERS ======================= //
    // ====================================================== //
    
    // set WSDL for authentication and create new SOAP client
    $auth_url  = "http://search.webofknowledge.com/esti/wokmws/ws/WOKMWSAuthenticate?wsdl";
    // array options are temporary and used to track request & response data in printout below (line 65)
    $auth_client = @new SoapClient($auth_url, array(
                     "trace" => 1,
                     "exceptions" => 0));
    // run 'authenticate' method and store as variable
    $auth_response = $auth_client->authenticate();

    // set WSDL for search and create new SOAP client
    $search_url = "http://search.webofknowledge.com/esti/wokmws/ws/WokSearch?wsdl";
    // array options are temporary and used to track request & response data in printout below (line 130)
    $search_client = @new SoapClient($search_url, array(
                     "trace" => 1,
                     "exceptions" => 0));
    // call 'setCookie' method on '$search_client' storing SID (Session ID) as the response (value) given from the 'authenticate' method
    $search_client->__setCookie('SID',$auth_response->return);


    // =================================================================== //
    // ============== PASS IN PARAMETERS FOR SOAP REQUEST ================ //
    // =================================================================== //


    // search type
    $queryType1 = $_POST["type1"];
    // keyword(s)
    $queryCategory1 = $_POST["category1"];
    // sort type
    $sortType = $_POST["sort"];

    // check if 'hidden' extra search facility is being used, if it is, populate variables
    if (!$_POST["category2"]) {
        $queryLogic = "";
        $queryType2 = "";
        $queryCategory2 = "";
    } else {
        $queryLogic = $_POST["logic"];
        $queryType2 = $_POST["type2"]."=";
        $queryCategory2 = $_POST["category2"];
    }

    // check if timespan fields have been populated
    if (!$_POST["timeStart"]) {
        $timeStart = "1864-01-01";
        $timeEnd = "2080-01-01";
    } else {
        $timeStart = $_POST["timeStart"];
        $timeEnd = $_POST["timeEnd"];
    }
    
    // pass in relevant parameters for search
    $search_array = array(
        'queryParameters' => array(
            'databaseId' => 'WOS',
            'userQuery' => $queryType1.'='.$queryCategory1. ' ' .$queryLogic. ' ' .$queryType2.$queryCategory2,
            'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
            'timeSpan' => array('begin' => $timeStart, 'end' => $timeEnd),
            'queryLanguage' => 'en'
        ),
        'retrieveParameters' => array(
            'count' => '100',
            'sortField' => array(
                array('name' => $sortType, 'sort' => 'D')
            ),
            'firstRecord' => '1'
        )
    );


    // =================================================================== //
    // ======== PERFORM SEARCH USING PARAMETERS & SOAP CLIENT ============ //
    // =================================================================== //


    // try to store as a variable the 'search' method on the '$search_array' called on the SOAP client with associated SID 
    try {
        $search_response = $search_client->search($search_array);
    } catch (Exception $e) {  
        echo $e->getMessage(); 
    };

    // number of records found by search, used to finish loop
    $len = $search_response->return->recordsFound;

    echo "</br>TOTAL RECORDS FOUND: </br>";
    print "<pre>\n";
    print $len;
    print "</pre>";

    ?>