<?php

// ========== COLLECT DATA FROM SIMPLEXMLELEMENT ========== //


// use xml file as a stored variable
// $xml = simplexml_load_file("edit.xml");

//variables to store data for searchResponse table
$uid = "";
$journal = "";
$publication = "";
$year = 0;
$author1 = "";
$address = "";
$author2 = "";
$author3 = "";
$citations = 0;
// create an array to store data for each record per iteration
$record = array();

foreach ($c02 as $c03) {
	// finds 1st children of c02 node (under c01/c02[0], poetry by author) stored as variable $first_gen
	foreach ($c03->children() as $first_gen) {
		// unique identifier is found under xml tag <unitid> under the atttribute "identifier"
		$uid == $first_gen->xpath('//ead/archdesc/dsc/c01//c02[1]/c03/did/unitid//@identifier');
		$journal == $first_gen->xpath('//ead/archdesc/dsc/c01/c02[1]/c03/did/unittitle[1]');
		$publication == $first_gen->xpath('//ead/archdesc/dsc/c01/c02[1]/c03/bioghist//p');
		$year == $first_gen->xpath('//ead/archdesc/dsc/c01//c02[1]/c03/c04/did//unittitle');
		$author1 ==
		$address ==
		$author2 ==
		$author3 ==
		$citations ==

		// for this iteration map all the values recorded into a temporary array variable
		$arecord = array("uid"=>$uid,
						 "journal"=>$journal,
						 "publication"=>$publication,
						 "year"=>$year,
						 "author1"=>$author1,
						 "address"=>$address,
						 "author2"=>$author2,
						 "author3"=>$author3,
						 "citations"=>$citations );

		// pass the data from this iteration into the array variable 'record'
		array_push($record, $arecord) ;
	}
}

print_r($record);

// ========== IMPORT DATA INTO DATABASE ========== //

$recordTable = implode("','",$record[1]);

mysql_query("INSERT INTO searchresponse (uid, journal, publication, year, author1, address, author2, author3, citations) VALUES ('$recordTable')");


// need to pass data stored in 'personArray' into the database 'poeticarchive' into table 'Person'
/* $sql = "INSERT INTO Person (AuthorID, FirstName, LastName, Age, Biography, DateOfBirth) VALUES";

// create a new iterator to iterate personArray
$itr = new ArrayIterator($personArray);

// create new CachingIterator to provide access to hasNext() to tell iterator when to terminate
$citr = new CachingIterator($itr);

// loop over the array
foreach ($citr as $val) {
	// add to the query
	$sql .= "('".$citr->key()."','" .$citr->current()."')";
	// if there's another entry, add a comma
	if( $citr->hasNext() )
    {
        $sql .= ",";
    }
} */

/* mysqli_query($connect, $sql);

foreach ($personArray as $val) {
	$sql = ($connect, "INSERT INTO Person (AuthorID, FirstName, LastName, Age, Biography, DateOfBirth)
						VALUES ('$aID', '$firstName', '$lastname', '$age', '$bio', '$dob')");
	if (!mysqli_query($connect, $sql)) {
  		die('Error: ' . mysqli_error($con));
	}
} */