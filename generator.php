#!/usr/bin/env php
<?php
/**
 * Genera un file csv con 1000000 di record della forma Data,IDVenditore,IDAcuirente,Valore, 
 * la data deve essere uniformemente distribuita negli utlimi cinque anni
 */
$file = './records.csv';
$header = ['Data', 'IDVenditore', 'IDAcquirente', 'Valore'];
$record_number = (10 ** 5) * 6;

// Open the file for writing
$handle = fopen($file, 'w');

// Write the header to the file
fputcsv($handle, $header);

// Generate and write the records to the file
$startDate = strtotime('-5 years');
$endDate = time();
$interval = intval(ceil(($endDate - $startDate) / $record_number));

for ($i = 1; $i <= $record_number; $i++) {
    $data = date('Y-m-d', $startDate + ($interval * $i));
    $idVenditore = generateRandomId('utente_', 1, 100);
    $idAcquirente = generateRandomId('utente_', 1, 100);
    while($idVenditore == $idAcquirente) {
        $idAcquirente = generateRandomId();
    }
    $valore = generateRandomValue();

    $record = [$data, $idVenditore, $idAcquirente, $valore];
    fputcsv($handle, $record);
}

// Close the file
fclose($handle);

function generateRandomId( $prefix = 'utente_', $start_value = 1, $end_value = 10 )
{
    return $prefix . rand($start_value, $end_value);
}

function generateRandomValue()
{
    return rand(1, 1000);
}