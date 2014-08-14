<?php

  // arogozin@nyu.edu

  // set WSDL for authentication and create new SOAP client
  $auth_url  = "http://search.webofknowledge.com/esti/wokmws/ws/WOKMWSAuthenticate?wsdl";
  // array options are temporary and used to track request & response data in printout below (line 25)
  $auth_client = @new SoapClient($auth_url, array(
                                "trace"=>1,
                                "exceptions"=>0));
  // run 'authenticate' method and store as variable
  $auth_response = $auth_client->authenticate();
  
  // set WSDL for search and create new SOAP client
  $search_url = "http://search.webofknowledge.com/esti/wokmws/ws/WokSearch?wsdl";
  // array options are temporary and used to track request & response data in printout below (line 58)
  $search_client = @new SoapClient($search_url, array(
                                "trace"=>1,
                                "exceptions"=>0));
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
          'userQuery' => 'TS=botany',
          'editions' => array(
              array('collection' => 'WOS', 'edition' => 'SSCI'),
              array('collection' => 'WOS', 'edition' => 'SCI')
          ),
          'queryLanguage' => 'en'
      ),
      'retrieveParameters' => array(
          'count' => '10',
          'sortField' => array(
              array('name' => 'RS', 'sort' => 'D')
          ),
          'firstRecord' => '1'
      )
  );

  // pass in parameters for retrieveById
  $retrieve_array = array(
  );
  
  // try to store as a variable the 'search' method on the '$search_array' called on the SOAP client with associated SID 
  try {
      $search_response = $search_client->search($search_array);
  } catch (Exception $e) {  
      echo $e->getMessage(); 
  }
  
  // print details of XML request and response data for Search exchange
  print "<pre>\n";
  print "<br />\n Request : ".htmlspecialchars($search_client->__getLastRequest());
  print "<br />\n Response: ".$search_client->__getLastResponse();
  print "</pre>";

  // print the results stored in the variable above
  print "<pre>\n";
  print_r($search_response);
  print "</pre>";

  // store an individual value from the data (address) and print
  $address = $search_response.recordsFound;

  print"<br />\n Address: $address";

?>