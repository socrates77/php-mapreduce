#!/usr/bin/env php
<?php

$mapped = [];

while ( !feof(STDIN) ) {
    $row = fgetcsv(STDIN);
    if (empty($row)) {
        continue;
    }
    $venditore = $row[1];
    $acquirente = $row[2];
    $date = strtotime($row[0]);
    $value = intval($row[3]);
    $mapped[] = [ $venditore, date( 'Y-m', $date), $value ];
    $mapped[] = [ $acquirente, date( 'Y-m', $date), -1 * $value ];
}

array_walk($mapped, function($item) {
    fputcsv(STDOUT, $item);
});
fflush(STDOUT);
fflush(STDERR);