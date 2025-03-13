<?php

// Připojení k neznámé databázi (změň DSN podle potřeby)
$unknownDbConfig = [
    'dsn' => 'firebird:dbname=localhost:D:\Prace\STARMONTRACKER.fdb',  //název target databáze
    'user' => 'SYSDBA',
    'password' => 'masterkey'
];

try {
    // Připojení k neznámé databázi
    $unknownDb = new PDO($unknownDbConfig['dsn'], $unknownDbConfig['user'], $unknownDbConfig['password']);
    $unknownDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Výběr všech dat z tabulky ZAZNAMDAT
    $query = $unknownDb->query("SELECT * FROM ZAZNAMDAT");
    $records = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($records)) {
        $jsonFile = 'data.json';
        file_put_contents($jsonFile, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(["success" => "Data byla uložena do JSON souboru.", "file" => $jsonFile]);
    } else {
        echo json_encode(["error" => "Žádná data k uložení."]);
    }

} catch (Exception $e) {
    echo json_encode(["error" => "Chyba při připojení k databázi: " . $e->getMessage()]);
}
?>
