<?php namespace App\Models;

use DB;
use Schema;

class DataSort {

    // create new array to store data from all years (after processing)
    public $allArray = [];
    // create new array to store data from years
    // specified by user in Time Span in form input
    public $timeArray = [];
    // create new arrays for previous 2, 5 and 10 years for dropdown menu options
    public $tenArray = [];
    public $fiveArray = [];
    public $twoArray = [];
    // create a new array to store 'values'
    public $valueArray = [];
    // all records constructed from $soap->records
    public function __construct($dataArray) {
        $this->records = $dataArray;
    }

    // function to assign weight to data for bubble chart
    public function assignValues() {

        // iterate each element (publication) in $records and assign weight
        // according to citations vs publication date
        for ($i = 0; $i < count($this->records); $i++) {
            // check publication year against current year
            switch (date('Y')) {
                case ($this->records[$i]['pubyear']) == (date('Y')):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 10);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-1):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 10);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-2):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 9);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-3):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 8);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-4):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 7);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-5):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 6);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-6):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 5);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-7):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 4);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-8):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 3);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-9):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 2);
                    break;
                case ($this->records[$i]['pubyear']) == ((date('Y'))-10):
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 1);
                    break;
                default:
                    $this->records[$i]['weight'] = (($this->records[$i]['citations']) * 0);
                    break;
            }
        };
    }

    // CALCULATIONS WITHOUT USING SQL (currently uses SQL so commented out)

    /* public function removeDuplicates() {

        // as length of $j loop will decrease each time because of 'unset' its elements, create a variable to dynamically store its length
        $length = count($this->records);
        $count = 0;

        // iterate each author in records, ignore last value otherwise would end up comparing
        // it to itself in inner loop
        for ($i = 0; $i < (count($this->records) - 1); $i++) {
            // iterate each author in records a step ahead of the outer loop, compare each
            // author with every other author in array
            for ($j = ($i + 1); $j < $length; $j++) {
                // if there is a match between author names then do:
                if ($this->records[$i]['authors'] === $this->records[$j]['authors']) {
                    // add second citations value to first
                    $this->records[$i]['citations'] += $this->records[$j]['citations'];
                    // add second value to first
                    $this->records[$i]['values'] += $this->records[$j]['values'];
                    // remove second instance
                    unset($this->records[$j]);
                    // add to a variable the number of times 'unset' has been used for this
                    // iteration of $i
                    $count++;
                }; // end if
            }; // end inner loop ($j)
            // decrease length of inner loop by $count, i.e. the number of elements that
            // were removed in the last iteration, to make the length of the inner loop correct
            $length -= $count;
            // reset $count for next iteration of $i
            $count = 0;
            // reset indices
            $this->records = array_values($this->records);
        }; // end outer loop ($i)
    } */

    /* public function timeSpan($timeStart, $timeEnd) {

        // echo $timeStart;
        // echo $timeEnd;

        // if the publication year of the current record is less than or equal to the end
        // of the time span AND greater than or equal to the start of the time span then
        // include the full record in $timeArray
        for ($i = 0; $i < count($this->records); $i++) {

            if (($this->records[$i]['pubyear'] <= $timeEnd) && ($this->records[$i]['pubyear'] >= $timeStart)) {
                array_push($this->timeArray, $this->records[$i]);
            }
            if ($this->records[$i]['pubyear'] >= (date("Y")-10)) {
                array_push($this->tenArray, $this->records[$i]);
            }
            if ($this->records[$i]['pubyear'] >= (date("Y")-5)) {
                array_push($this->fiveArray, $this->records[$i]);
            }
            if ($this->records[$i]['pubyear'] >= (date("Y")-2)) {
                array_push($this->twoArray, $this->records[$i]);
            }
        };
    } */

    public function sortData($arrayData, $arrayType, $sortBy) {
        // sort array according to $sortBy
        // make sure that data is sorted correctly (value, high -> low)
        usort($this->$arrayType, function ($a, $b) use ($sortBy){
            return $b[$sortBy] - $a[$sortBy];
        });
    }

    // creates relevant tables in database if they don't exist
    public function createTables() {
        // create table for all records if doesn't exist
        if (!Schema::hasTable('searchresponse')) {
            Schema::create('searchresponse', function($table1) {
                $table1->string('author');
                $table1->string('country');
                $table1->integer('year');
                $table1->string('ID');
                $table1->integer('citations');
                $table1->integer('weight');
            });
        };
        // create table for user defined time period if doesn't exist
        if (!Schema::hasTable('userdefined')) {
            Schema::create('userdefined', function($table2) {
                $table2->string('author');
                $table2->string('country');
                $table2->integer('year');
                $table2->string('ID');
                $table2->integer('citations');
                $table2->integer('weight');
            });
        };
        // create table for ten year time period if doesn't exist
        if (!Schema::hasTable('tenyear')) {
            Schema::create('tenyear', function($table3) {
                $table3->string('author');
                $table3->string('country');
                $table3->integer('year');
                $table3->string('ID');
                $table3->integer('citations');
                $table3->integer('weight');
            });
        };
        // create table for five year time period if doesn't exist
        if (!Schema::hasTable('fiveyear')) {
            Schema::create('fiveyear', function($table4) {
                $table4->string('author');
                $table4->string('country');
                $table4->integer('year');
                $table4->string('ID');
                $table4->integer('citations');
                $table4->integer('weight');
            });
        };
        // create table for five year time period if doesn't exist
        if (!Schema::hasTable('twoyear')) {
            Schema::create('twoyear', function($table5) {
                $table5->string('author');
                $table5->string('country');
                $table5->integer('year');
                $table5->string('ID');
                $table5->integer('citations');
                $table5->integer('weight');
            });
        };

        // remove any existing data from the tables
        DB::table('searchresponse')->truncate();
        DB::table('userdefined')->truncate();
        DB::table('tenyear')->truncate();
        DB::table('fiveyear')->truncate();
        DB::table('twoyear')->truncate();
    }

    // populate the tables in the database
    function populateTables($data, $from, $to) {
        // loop over the data and add to table
        for ($row = 0; $row < count($data); $row++) {
            // inner loop for array of authors per record
            foreach ($data[$row]['authors'] as $value) {
                DB::table('searchresponse')->insert(
                    [
                        'author'    => $value,
                        'country'   => $data[$row]['country'],
                        'year'      => $data[$row]['pubyear'],
                        'ID'        => $data[$row]['ID'],
                        'citations' => $data[$row]['citations'],
                        'weight'    => $data[$row]['weight']
                    ]
                );
            }
        };
        // populate other tables from searchresponse according to years
        // USER DEFINED
        $userData = DB::table('searchresponse')
            ->whereBetween('year', [$from, $to])
            ->get();
        // iterate data and insert into table
        foreach ($userData as $value) {
            DB::table('userdefined')->insert(
                [
                    'author'    => $value->author,
                    'country'   => $value->country,
                    'year'      => $value->year,
                    'ID'        => $value->ID,
                    'citations' => $value->citations,
                    'weight'    => $value->weight
                ]
            );
        }
        // LAST TEN YEARS
        $tenData = DB::table('searchresponse')
            ->whereBetween('year', [date("Y")-10, date("Y")])
            ->get();
        // iterate data and insert into table
        foreach ($tenData as $value) {
            DB::table('tenyear')->insert(
                [
                    'author'    => $value->author,
                    'country'   => $value->country,
                    'year'      => $value->year,
                    'ID'        => $value->ID,
                    'citations' => $value->citations,
                    'weight'    => $value->weight
                ]
            );
        }
        // LAST FIVE YEARS
        $fiveData = DB::table('searchresponse')
            ->whereBetween('year', [date("Y")-5, date("Y")])
            ->get();
        // iterate data and insert into table
        foreach ($fiveData as $value) {
            DB::table('fiveyear')->insert(
                [
                    'author'    => $value->author,
                    'country'   => $value->country,
                    'year'      => $value->year,
                    'ID'        => $value->ID,
                    'citations' => $value->citations,
                    'weight'    => $value->weight
                ]
            );
        }
        // LAST TWO YEARS
        $twoData = DB::table('searchresponse')
            ->whereBetween('year', [date("Y")-2, date("Y")])
            ->get();
        // iterate data and insert into table
        foreach ($twoData as $value) {
            DB::table('twoyear')->insert(
                [
                    'author'    => $value->author,
                    'country'   => $value->country,
                    'year'      => $value->year,
                    'ID'        => $value->ID,
                    'citations' => $value->citations,
                    'weight'    => $value->weight
                ]
            );
        }
    }

    // sum citations for duplicate authors
    function sumCitesAll() {
        // update searchresponse
        \DB::update('UPDATE searchresponse AS a 
                         JOIN(
                             SELECT author,
                             SUM(citations) AS citations,
                             COUNT(author) AS b FROM searchresponse GROUP BY author
                             ) grp
                         ON grp.author = a.author 
                         SET a.citations = grp.citations');

        //Now fetch all data from that table
        $sumAll = \DB::table('searchresponse')->get();
        return $sumAll;
    }
    // sum weight for duplicate authors
    function sumValuesAll() {
        // update searchresponse
        \DB::update('UPDATE searchresponse AS a 
                         JOIN(
                             SELECT author,
                             SUM(weight) AS weight,
                             COUNT(author) AS b FROM searchresponse GROUP BY author
                             ) grp
                         ON grp.author = a.author 
                         SET a.weight = grp.weight');

        //Now fetch all data from that table
        $sumAll = \DB::table('searchresponse')->get();
        return $sumAll;
    }
    function sumCitesUser() {
        // update userdefined
        \DB::update('UPDATE userdefined AS a 
                         JOIN(
                             SELECT author,
                             SUM(citations) AS citations,
                             COUNT(author) AS b FROM userdefined GROUP BY author
                             ) grp
                         ON grp.author = a.author 
                         SET a.citations = grp.citations');

        //Now fetch all data from that table
        $sumUserDefined = \DB::table('userdefined')->get();
        return $sumUserDefined;
    }
    // // sum weight for duplicate authors
    // function sumValuesUser() {
    //     // update userdefined
    //     \DB::update('UPDATE userdefined AS a 
    //                      JOIN(
    //                          SELECT author,
    //                          SUM(weight) AS weight,
    //                          COUNT(author) AS b FROM userdefined GROUP BY author
    //                          ) grp
    //                      ON grp.author = a.author 
    //                      SET a.weight = grp.weight');

    //     //Now fetch all data from that table
    //     $sumAll = \DB::table('userdefined')->get();
    //     return $sumAll;
    // }
    function sumCitesTen() {
        // update tenyear
        \DB::update('UPDATE tenyear AS a 
                         JOIN(
                             SELECT author,
                             SUM(citations) AS citations,
                             COUNT(author) AS b FROM tenyear GROUP BY author
                             ) grp 
                         ON grp.author = a.author 
                         SET a.citations = grp.citations');

        //Now fetch all data from that table
        $sumTen = \DB::table('tenyear')->get();
        return $sumTen;
    }
    // sum weight for duplicate authors
    // function sumValuesTen() {
    //     // update tenyear
    //     \DB::update('UPDATE tenyear AS a 
    //                      JOIN(
    //                          SELECT author,
    //                          SUM(weight) AS weight,
    //                          COUNT(author) AS b FROM tenyear GROUP BY author
    //                          ) grp
    //                      ON grp.author = a.author 
    //                      SET a.weight = grp.weight');

    //     //Now fetch all data from that table
    //     $sumAll = \DB::table('tenyear')->get();
    //     return $sumAll;
    // }
    function sumCitesFive() {
        // update fiveyear
        \DB::update('UPDATE fiveyear AS a 
                         JOIN(
                             SELECT author,
                             SUM(citations) AS citations,
                             COUNT(author) AS b FROM fiveyear GROUP BY author
                             ) grp 
                         ON grp.author = a.author 
                         SET a.citations = grp.citations');

        //Now fetch all data from that table
        $sumFive = \DB::table('fiveyear')->get();
        return $sumFive;
    }
    // sum weight for duplicate authors
    // function sumValuesFive() {
    //     // update fiveyear
    //     \DB::update('UPDATE fiveyear AS a 
    //                      JOIN(
    //                          SELECT author,
    //                          SUM(weight) AS weight,
    //                          COUNT(author) AS b FROM fiveyear GROUP BY author
    //                          ) grp
    //                      ON grp.author = a.author 
    //                      SET a.weight = grp.weight');

    //     //Now fetch all data from that table
    //     $sumAll = \DB::table('fiveyear')->get();
    //     return $sumAll;
    // }
    function sumCitesTwo() {
        // update twoyear
        \DB::update('UPDATE twoyear AS a 
                         JOIN(
                             SELECT author,
                             SUM(citations) AS citations,
                             COUNT(author) AS b FROM twoyear GROUP BY author
                             ) grp
                         ON grp.author = a.author 
                         SET a.citations = grp.citations');

        //Now fetch all data from that table
        $sumTwo = \DB::table('twoyear')->get();
        return $sumTwo;
    }
    // sum weight for duplicate authors
    // function sumValuesTwo() {
    //     // update twoyear
    //     \DB::update('UPDATE twoyear AS a 
    //                      JOIN(
    //                          SELECT author,
    //                          SUM(weight) AS weight,
    //                          COUNT(author) AS b FROM twoyear GROUP BY author
    //                          ) grp
    //                      ON grp.author = a.author 
    //                      SET a.weight = grp.weight');

    //     //Now fetch all data from that table
    //     $sumAll = \DB::table('twoyear')->get();
    //     return $sumAll;
    // }

    // to remove the unnecessary attributes from the values array
    public function removeAttributes($valuesData) {

        // sort value data so that it only has 2 values for bubble chart (author & weight)
        for ($i = 0; $i < (count($this->valueArray)); $i++) {
            unset($this->valueArray[$i]['citations']);
            unset($this->valueArray[$i]['ID']);
            unset($this->valueArray[$i]['country']);
            unset($this->valueArray[$i]['year']);
        };
    }

    // to pull back the data from the database to PHP arrays
    public function pullData($tableName) {
        $result =  DB::table($tableName)
            ->groupBy('author')
            ->get();
        return $result;
    }
}