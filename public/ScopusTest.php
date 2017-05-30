<?php

$apiKey = "&apiKey=7804b8bef2d4dc6e5a85ef2dfb84a87c";
$search1 = urlencode("badgers");
$search2 = urlencode("%20OR%20weasels");
$journal1 = urlencode("nature");
$journal2 = urlencode("%20OR%20plos one");
$start = 0;
$scopusData = [];
$finalScopus = [];
// create an array to represent citation values to ignore, i.e. not interested
// in any publications with less than 4 citations
$ignore = array(0, 1, 2, 3);

// set processing time for browser before timeout
ini_set('max_execution_time', 3600);
// override default PHP memory limit
ini_set('memory_limit', '-1');

// REST HTTP GET Request searching for people associated with keywords (term)
$searchLink = "https://api.elsevier.com/content/search/scopus?query=TITLE%28" . $search1 . $search2 . "%29&SRCTITLE%28" . $journal1 . $journal2 . "%29&PUBYEAR>2004" . $apiKey . "&sort=citedby-count&view=COMPLETE";

echo "</br>QUERY URL: </br>";
print "<pre>\n";
print_r($searchLink);
print "</pre>";

// save results to a variable
$searchResponse = file_get_contents($searchLink);

// convert JSON to PHP variable
$searchJson = json_decode($searchResponse, true);

echo "</br>RESULTS:</br>";
print "<pre>\n";
print_r($searchJson);
print "</pre>";

// get total number of results for query to know when to stop iterating data
$total = $searchJson['search-results']['opensearch:totalResults'];

// iterate data loading next 200 results (max) each time and adding new results to array
for ($i = $start; $i <= $total; $i+=25) {
    // REST HTTP GET Request searching for people associated with keywords (term)
    $eachLink = "https://api.elsevier.com/content/search/scopus?query=TITLE%28" . $search1 . $search2 . "%29&SRCTITLE%28" . $journal1 . $journal2 . "%29" . $apiKey . "&sort=citedby-count&view=COMPLETE&start=" . $i;

    // save results to a variable
    $eachResponse = file_get_contents($eachLink);

    $eachJson = json_decode($eachResponse, true);

    foreach ($eachJson['search-results']['entry'] as $record) {

        // array to store authors
        $authors = [];
        // iterate each author subset
        foreach ($record['author'] as $thisAuthor) {
            // check if there is a value first
            if (isset($thisAuthor['surname'])) {
                // populate array with author name
                array_push($authors, ($thisAuthor['given-name'] . " " . $thisAuthor['surname']));
            };
        };
        // country
        $country = "";
        if (isset($record['affiliation']['affiliation-country'])) {
            $country = $record['affiliation']['affiliation-country'];
        }
        // scopus ID
        $scopusID = $record['dc:identifier'];
        // paper title
        $title = $record['dc:title'];
        // date
        $date = substr($record['prism:coverDate'], 0, 3);
        // citations, if less than 4 then break out of iteration
        if (!in_array(($cites = $record['citedby-count']), $ignore)) {
            $cites = $record['citedby-count'];
        } else {
            break 2;
        }

        $thisData = [
                        "authors" => $authors,
                        "country" => $country,
                        "ID"      => $scopusID,
                        "title"   => $title,
                        "date"    => $date,
                        "cites"   => $cites
        ];

        array_push($scopusData, $thisData);
    }
};

// need to replace single quotes to avoid char escape
for ($i = 0; $i < count($scopusData); $i++) {
    foreach ($scopusData[$i]['authors'] as &$edit) {
        $edit = str_replace("'", "", $edit);
    };
    $scopusData[$i]['title'] = str_replace("'", "", $scopusData[$i]['title']);
};

// for some reason Scopus returns duplicate authors for same record
// this will remove duplicates within the same paper
for ($i = 0; $i < count($scopusData); $i++) {
    $scopusData[$i]['authors'] = array_unique($scopusData[$i]['authors']);
    // reset indices for array
    $scopusData[$i]['authors'] = array_values($scopusData[$i]['authors']);
};

// echo "</br>RECORDS:</br>";
// print "<pre>\n";
// print_r($finalScopus);
// print "</pre>";

?>