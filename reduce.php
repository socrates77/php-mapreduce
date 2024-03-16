#!/usr/bin/env php
<?php

$reduced = [];

while ( !feof(STDIN) ) {
    $row = fgetcsv(STDIN);
    if (empty($row)) {
        continue;
    }
    $user = $row[0];
    $date = $row[1];
    $value = intval($row[2]);
    if ( empty($reduced[$user]) ) {
        $reduced[$user] = [];
    }
    if ( empty($reduced[$user][$date]) ) {
        $reduced[$user][$date] = 0;
    }
    $reduced[$user][$date] = $value + $reduced[$user][$date];
}

array_walk($reduced, function($item, $user) {
    array_walk($item, function($value, $date) use ($user) {
        fputcsv(STDOUT, [ $user, $date, $value ]);
    });
});
fflush(STDOUT);
fflush(STDERR);