<?php

namespace App;

class OrderFile {
    public function createArray($handle, $firstLine = false) {
        $data = [];
        if ( $handle !== FALSE) { 
            while (($line = fgetcsv($handle, 0, '|')) !== FALSE) {
                $data[] = $line;
            }
            fclose($handle);
        }
        // Optionnal remove first line
        if ( $firstLine === false ) {
            array_shift($data);
        }
        return $data;
    }

    public function orderBySkus($data) {
        $skus = [];

        foreach ($data as $key => $item) {
            $skus[] = $item[1];
        }

        $result = [];

        foreach ($skus as $sku) {
            $customers = [];
            $prices = [];
            for ($i=0; $i < count($data) ; $i++) { 
                if(in_array($sku, $data[$i])) {
                    // $customers[] = [$data[$i] , $data[$i][0]];
                    $arr = $data[$i];
                    $customers[] = $data[$i][0];
                    $prices[] = $data[$i][2];
                }
            }
            var_dump(
                [$arr, $prices, $customers]
            );
        }

    }

}
