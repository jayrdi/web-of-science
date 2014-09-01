<?php

    /*echo '<link rel="stylesheet" type="text/css" href="style.css"/>
          <link href="http://fonts.googleapis.com/css?family=Raleway:700" rel="stylesheet" type="text/css">
          <link href="http://fonts.googleapis.com/css?family=Lora:400,700" rel="stylesheet" type="text/css">
          <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>'; */

    // =================================================================== //
    // == Author: John Dawson                                           == //
    // == Date: 28/08/2014                                              == //
    // == Description: Processing for a website to query Web of Science == //
    // ==              Web Service using their API and return relevant  == //
    // ==              data                                             == //
    // =================================================================== //

    
    // =================================================================== //
    // ================ SET UP SOAP CLIENTS & AUTHENTICATE =============== //
    // =================================================================== //


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


    // =================================================================== //
    // ================= PRINT OUT REQUEST & RESPONSE ==================== //
    // ====================== OF AUTHORISATION =========================== //
    // =================================================================== // 


    // print details of XML request and response data for Authentication exchange
    /* print "<pre>\n";
    print "<br />\n Request : ".htmlspecialchars($auth_client->__getLastRequest());
    print "<br />\n Response: ".htmlspecialchars($auth_client->__getLastResponse());
    print "</pre>"; */


    // =================================================================== //
    // ============== PASS IN PARAMETERS FOR SOAP REQUEST ================ //
    // =================================================================== //


    // pass in relevant parameters for search
    $search_array = array(
        'queryParameters' => array(
            'databaseId' => 'WOS',
            'userQuery' => $_POST["type"].'='.$_POST["category"],
            'editions' => array('collection' => 'WOS', 'edition' => 'SCI'),
            'queryLanguage' => 'en'
        ),
        'retrieveParameters' => array(
            'count' => '100',
            'sortField' => array(
                array('name' => $_POST["sort"], 'sort' => 'D')
            ),
            'firstRecord' => '1'
        )
    );

    /* POPULATE ARRAY VARIABLE WITH <uid> VALUES FROM $search_array INTO $retrieve_array */

    // pass in parameters for retrieveById
    /* $retrieve_array = array(
        'databaseId' => 'WOS',
        'uid' => 'WOS:A1993LC48100001 WOS:A1993LE28400012',
        'queryLanguage' => 'en',
        'retrieveParameters' => array(
            'count' => '10',
            'firstRecord' => '1'
        )
    ); */


    // =================================================================== //
    // ======== PERFORM SEARCH USING PARAMETERS & SOAP CLIENT ============ //
    // =================================================================== //


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

    // =================================================================== //
    // ================ PRINT VALUES TO CHECK DATA ======================= //
    // =================================================================== //

    /* echo "</br>SEARCH_RESPONSE: </br>";
    print "<pre>\n";
    print_r($search_response);
    print "</pre>";

    echo "</br>SEARCH_CLIENT: </br>";
    print "<pre>\n";
    print_r($search_client);
    print "</pre>"; */

    /* $string = htmlspecialchars($search_client->__getLastResponse());

    // change all &lt; to <
    // $string = str_replace('(&lt;)', '<', $string);

    echo "</br></br>EXTRACTED STRING: </br></br>";
    print "<pre>\n";
    print_r ($string);
    print "</pre>"; */


    // =================================================================== //
    // ================= TURN SOAP RESPONSE STRING INTO ================== //
    // ================== SIMPLE XML ELEMENT OBJECT TO =================== //
    // ========== TRAVERSE AND EXTRACT INDIVIDUAL DATA ELEMENTS ========== //
    // =================================================================== //


    // turn Soap Client object into SimpleXMLElement
    $xml = new SimpleXMLElement($search_response->return->records);

    // register the namespaces
    // $xml->registerXPathNamespace("ns1", "http://scientific.thomsonreuters.com/schema/wok5.4/public/FullRecord");
    // $xml->registerXPathNamespace("ns2", "http://woksearch.v3.wokmws.thomsonreuters.com");
    // $xml->registerXPathNamespace("soap", "http://schemas.xmlsoap.org/soap/envelope/");

    // initiate the xpath
    // $xpath = "/soap:Envelope/soap:Body/ns2:searchResponse/return/records/ns1:records";

    // $result = $xml->xpath($xpath);

    /* echo "</br>SIMPLE XML ELEMENT OBJECT: </br>";
    print "<pre>\n";
    print_r($xml);
    print "</pre>"; */
    // print_r($result);

    // encode XML data as JSON for use with Javascript
    $json = json_encode($xml);
    /* echo "</br>JSON: </br>";
    print "<pre>\n";
    print_r($json);
    print "</pre>"; */


    // =================================================================== //
    // ============ CREATE VARIABLES TO STORE REQUIRED DATA ============== //
    // ================== FROM XML & DISPLAY IN TABLE ==================== //
    // =================================================================== //


    // journal name
    /* $journal = "";
    // publication name
    $publication = "";
    // various authors
    $author1 = "";
    $author2 = "";
    $author3 = "";
    $author4 = "";
    $authorLast = "";
    // address of author1
    $address = "";
    // number of citations for publication
    $citations = 0;
    // WoS unique identifier for publication
    $uid = "";
    // need to store all the authors for a given record as number of authors varies
    $personArray = array();

    // TESTS
    foreach($xml->REC as $record) {
        print "<pre>\n";
        echo $record->static_data->summary->titles->title[0]."</br>";
        echo $record->static_data->summary->titles->title[5]."</br>";
        echo $record->static_data->summary->names->name[0]->full_name."</br>";
        echo $record->static_data->fullrecord_metadata->addresses->address_name->address_spec->full_address."</br>";
        echo $record->dynamic_data->citation_related->tc_list->silo_tc->attributes()."</br>";
        echo $record->UID."</br>";
        print "</pre>";
    } */

    // print table with suitable headers
    echo '<table id="table" <tr>
                <th>Journal Name</th>
                <th>Publication Name</th>
                <th>Author</th>
                <th>Address</th>
                <th>Number of Citations</th>
                <th>Unique Identifier</th>
            </tr>>';

    // print data in table
    foreach($xml->REC as $record) {
        echo '<tr>';
        echo '<td>'.$record->static_data->summary->titles->title[0]."</td>";
        echo '<td>'.$record->static_data->summary->titles->title[5]."</td>";
        echo '<td>'.$record->static_data->summary->names->name[0]->full_name."</td>";
        echo '<td>'.$record->static_data->fullrecord_metadata->addresses->address_name->address_spec->full_address."</td>";
        echo '<td>'.$record->dynamic_data->citation_related->tc_list->silo_tc->attributes()."</td>";
        echo '<td>'.$record->UID."</td>";
        echo '</tr>';
    }
    echo '</table>';

    // file_put_contents("wosData.json", $json);


?>