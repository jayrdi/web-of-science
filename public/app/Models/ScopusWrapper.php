<?php namespace App\Models;

class ScopusWrapper {

    // array to store data
    public $scopusData = [];

    public function scopusWebExchange($search1, $search2, $search3) {

        // api key
        $apiKey = "&apiKey=c2cb86c3a511ed34dd6f03f481c637c1";
        // start search index
        $start = 0;
        // create an array to represent citation values to ignore, i.e. not interested
        // in any publications with less than 4 citations
        $ignore = array(0, 1, 2, 3);

        // REST HTTP GET Request searching for people associated with keywords (term)
        $searchLink = "http://api.elsevier.com/content/search/scopus?query=KEY(" . $search1 . $search2 . $search3 . ")" . $apiKey . "&sort=citedby-count&count=100&start=" . $start . "&view=complete";

        // save results to a variable
        @$searchResponse = file_get_contents($searchLink);

        // convert JSON to PHP variable
        $searchJson = json_decode($searchResponse, true);

        // get total number of results for query to know when to stop iterating data
        $total = $searchJson['search-results']['opensearch:totalResults'];

        // iterate data loading next page each time and adding new results to array
        for($i = $start; $i <= $total; $i+=100) {
            // REST HTTP GET Request searching for people associated with keywords (term)
            $eachLink = "http://api.elsevier.com/content/search/scopus?query=KEY(" . $search1 . $search2 . $search3 . ")" . $apiKey . "&sort=citedby-count&count=100&start=" . $i . "&view=complete";

            // save results to a variable
            $eachResponse = file_get_contents($eachLink);

            // convert JSON to PHP variable
            $eachJson = json_decode($eachResponse, true);

            foreach ($eachJson['search-results']['entry'] as $record) {
                // array to store authors
                $authors = [];
                if (isset($record['author'])) {
                    foreach ($record['author'] as $thisAuthor) {
                        // push initials and surname to array
                        array_push($authors, ($thisAuthor['initials'] . $thisAuthor['surname']));
                    }
                }
                // scopus ID
                $scopusID = $record['dc:identifier'];
                // date
                $date = substr($record['prism:coverDate'], 0, 4);
                // citations, if less than 4 then break out of iteration
                if (!in_array(($cites = $record['citedby-count']), $ignore)) {
                    $cites = $record['citedby-count'];
                } else {
                    break 2;
                }

                $thisData = [
                                "authors"   => $authors,
                                "country"   => "",
                                "ID"        => $scopusID,
                                "pubyear"   => $date,
                                "citations" => $cites
                ];

                array_push($this->scopusData, $thisData);
            }
        };

        // need to replace single quotes to avoid char escape
        for ($i = 0; $i < count($this->scopusData); $i++) {
            foreach ($this->scopusData[$i]['authors'] as &$edit) {
                $edit = str_replace("'", "", $edit);
            };
        }

        // for some reason Scopus returns duplicate authors for same record
        // this will remove duplicates within the same paper
        for ($i = 0; $i < count($this->scopusData); $i++) {
            $this->scopusData[$i]['authors'] = array_unique($this->scopusData[$i]['authors']);
            // reset indices for array
            $this->scopusData[$i]['authors'] = array_values($this->scopusData[$i]['authors']);
        };
    }
};

?>