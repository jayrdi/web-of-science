<?php namespace App\Models;

Class BubbleChartCompatible {

    public function __construct($dataArray) {
        $this->valueArray = $dataArray;
    }

    public function removeAttributes() {

        // sort value data so that it only has 2 values for bubble chart (author & value)
        for ($i = 0; $i < (count($this['valueArray'])); $i++) {
            unset($this['valueArray'][$i]['citations']);
            unset($this['valueArray'][$i]['pubyear']);
        };
    }
}