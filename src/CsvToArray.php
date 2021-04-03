<?php

namespace WalkerSpider\FileManager;

use Exception;

class CsvToArray {

    public $refColElems;

    public $data;

    public $result;

    /**
     * Set $this->data from the given file
     * @param $file (from fopen)
     * @param String $delimiter (eg. | , ; -)
     */
    public function __construct($file, $delimiter, $groupBy = false) {
        $data = [];
        if ( $file !== FALSE) { 
            while (($line = fgetcsv($file, 0, $delimiter)) !== FALSE) {
                $data[] = $line;
            }
            fclose($file);
            $this->data = $data;
        }
    }

    /**
     * Method Dynamically Generated
     * @param $method
     * @param $arguments
     * @return Mixed
     */
    public function __call($method, $arguments) {
        if ( ! isset($this->methods[$method]) ) {
            if ( strpos($method, 'groupBy') === 0 ) {
                $col = str_replace('groupBy', '', $method);
                try {
                    return $this->groupBy($col);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }

    /**
     * Set the col reference to regroup the futurs selected data
     * @param String $col 
     * @return CsvToArray $this
     */
    public function groupBy($col, $strict = false) {
        $col = $strict === false ? strtolower($col) : $col;
        $data = $this->data;
        for ($i=0; $i < count($data) ; $i++) { 
            $search = $data[$i][$this->colIndex($col, $strict)];
            if ($search) {
                $refColElems[] = $search;
            }
        }
        if ($refColElems) {
            $this->refColElems = array_values(array_unique($refColElems)); // remove duplicate values
            $this->refCol = $col;
            array_shift($this->refColElems);
            return $this;
        } else {
            throw new Exception("column $col not found.");
        }
    }

    /**
     * Select columns to retrieve
     * @param Array $cols
     * @return CsvToArray $this
     */
    public function select(Array $cols) {
        $this->result = [];
        try {
            $this->checkValidCols($cols);
            foreach ($this->refColElems as $refColElem) {
                for ($i=0; $i < count($this->data) ; $i++) { 
                    if(in_array($refColElem, $this->data[$i])) {
                        foreach ($cols as $col) {
                            ${$col}[] = $this->data[$i][$this->colIndex($col)];
                        }
                    }
                }
                $resItem[$this->refCol] = $refColElem;
                foreach ($cols as $col) {
                    $resItem[$col] = ${$col};
                    ${$col} = []; // clear array
                }
                array_push($this->result, $resItem);
            }
            return $this;
            // return $this->result;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Return the result
     * @param String $el
     */
    public function get($el = false) {
        if ($el) {
            foreach ($this->result as $key => $val) {
                if ( $val[$this->refCol] === $el ) {
                    return $val;
                }
            }
        } else {
            return $this->result;
        }
    }

    /**
     * Retrieve the column index
     * @param String $col
     * @param Boolean $strict 
     */
    protected function colIndex($col, $strict = false) {
        $firstline = $this->data[0];
        for ($i=0; $i < count($firstline) ; $i++) { 
            $search = $strict === false ? strtolower($firstline[$i]) : $firstline[$i];
            if ( $search === $col) {
                return $i;
            }
        }
    }

    /**
     * If column not found in firstline file throw Exception
     * @param String|Array $cols 
     */
    protected function checkValidCols($cols) {
        $cols = is_string($cols) ? [$cols] : $cols;
        foreach ($cols as $col) {
            if ( ! $this->data[0][$this->colIndex($col)] ) {
                throw new Exception("Column $col not found");
            }
        }
    }

}
