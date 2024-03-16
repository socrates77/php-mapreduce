#!/usr/bin/env php
<?php

$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
   2 => array("pipe", "w"),  // stderr is a file to write to
);
$pipes = [];
$file = './records_head.csv';
$handle = fopen($file, 'r');
// drop header
fgets( $handle );
$concurrency = 10;

$mappers = [];
$reducers = [];

for ( $i = 0; $i < $concurrency; $i++ ) {
    $process = proc_open( './map.php', $descriptorspec, $pipes );
    $mappers[$i] = [
        'process' => $process,
        'pipes' => $pipes,
    ];
}

for ( $i = 0; $i < $concurrency; $i++ ) {
    $process = proc_open( './reduce.php', $descriptorspec, $pipes );
    $reducers[$i] = [
        'process' => $process,
        'pipes' => $pipes,
    ];
}

echo 'Inizio dei mappers' . PHP_EOL;
while(!feof($handle)) {
    for ( $i = 0; $i < count( $mappers ); $i++ ) {
        $pumpit = '';
        for($j = 0; $j < 100; $j++ ) {
            $pumpit .= fgets( $handle );
        }
        fwrite( $mappers[$i]['pipes'][0], $pumpit );
        fflush( STDIN );
    }
}
echo 'Fine dei mappers' . PHP_EOL;
echo 'Chiudo STDIN dei mappers' . PHP_EOL;
for ( $i = 0; $i < count( $mappers ); $i++ ) {
    fclose($mappers[$i]['pipes'][0]);
}
echo 'Inizio dei reducers' . PHP_EOL;
for ( $i = 0; $i < count( $mappers ); $i++ ) {
    $pipes = $mappers[$i]['pipes'];
    while( !feof( $pipes[1] ) ) {
        for ( $j = 0; $j < count( $reducers ); $j++ ) {
            $pumpit = '';
            for($k = 0; $k < 100; $k++ ) {
                $pumpit .= fgets( $pipes[1] );
            }
            fwrite( $reducers[$j]['pipes'][0], $pumpit );
            fflush( STDIN );
        }
    }
}
echo 'Fine dei reducers' . PHP_EOL;

for ( $i = 0; $i < count( $reducers ); $i++ ) {
    while(!feof($reducers[$i]['pipes'][1])) {
        fwrite(STDOUT, fgets($reducers[$i]['pipes'][1]));
    }
    fclose( $mappers[$i]['pipes'][0] );
}

for ( $i = 0; $i < count( $mappers ); $i++ ) {
    $process = $mappers[$i]['process'];
    proc_close( $process );
}

for ( $i = 0; $i < count( $reducers ); $i++ ) {
    $process = $reducers[$i]['process'];
    proc_close( $process );
}