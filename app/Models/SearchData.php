<?php namespace App\Models;

class SearchData {
    
    protected $dataFields = [
        'journal1',
        'journal2',
        'journal3',
        'title1',
        'title2',
        'title3',
        'from',
        'to'
    ];

    public function setJournal1($journal1) {

        $this->journal1 = $journal1;
    }

    public function setJournal2($journal2) {
        
        $this->journal2 = $journal2;
    }

    public function setJournal3($journal3) {
        
        $this->journal3 = $journal3;
    }

    public function setTitle1($title1) {

        $this->title1 = $title1;
    }

    public function setTitle2($title2) {

        $this->title2 = $title2;
    }

    public function setTitle3($title3) {

        $this->title3 = $title3;
    }

    public function setFrom($from) {

        $this->from = $from;
    }

    public function setTo($to) {

        $this->to = $to;
    }
}