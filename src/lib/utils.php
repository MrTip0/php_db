<?php
function to_csv(array $arr): string {
    $r = "";
    if (count($arr) == 0) {
        return $r;
    }

    $keys = array_keys($arr[0]);
    $l = count($keys);
    for ($i=0; $i < $l; $i++) { 
        $r .= $keys[$i];
        if ($i == $l-1) {
            $r .= "\n";
        } else {
            $r .= ';';
        }
    }

    foreach($arr as $el) {
        for ($i=0; $i < $l; $i++) { 
            $r .= $el[$keys[$i]];
            if ($i == $l-1) {
                $r .= "\n";
            } else {
                $r .= ';';
            }
        }
    }
    return $r;
}