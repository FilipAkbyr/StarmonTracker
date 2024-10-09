<?php
// Firebird database connection details
$host = 'localhost:\D:\Prace\Starmontracker.fdb';
$username = 'SYSDBA';
$password = '123456789';


$connection = ibase_connect($host, $username, $password);

if (!$connection) {
    die('Connection failed: ' . ibase_errmsg());
}

// Path to data file
$filePath = 'path/to/your/data.csv';


if (($handle = fopen($filePath, 'r')) !== FALSE) {

    fgetcsv($handle);


    $sql = "INSERT INTO your_table_name (field1, field2) VALUES (?, ?)";
    $stmt = ibase_prepare($connection, $sql);

    if ($stmt === false) {
        die('Query preparation failed: ' . ibase_errmsg());
    }


    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $result = ibase_execute($stmt, $data[0], $data[1]);     

        if ($result === false) {
            echo 'Data insertion failed for row: ' . implode(', ', $data) . '. Error: ' . ibase_errmsg() . "\n";
        } else {
            echo 'Inserted row: ' . implode(', ', $data) . "\n";
        }
    }


    fclose($handle);
} else { 
    die('Failed to open the file: ' . $filePath);
}


ibase_close($connection);
?>

