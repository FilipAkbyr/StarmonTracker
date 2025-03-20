<?php

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die("Chyba: Očekáván GET request.");
}

// Konfigurace připojení k databázím
$sourceConnStr = "firebird:dbname=192.168.30.57:C:\WEB\database\DIAGOC.fdb;host=localhost";
$targetConnStr = "firebird:dbname=192.168.30.120:D:\\Prace\\STARMONTRACKER.fdb;host=localhost";

// Připojení k původní databázi
$sourceDb = new PDO($sourceConnStr, "SYSDBA", "masterkey");
$sourceDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Připojení k cílové databázi
$targetDb = new PDO($targetConnStr, "SYSDBA", "masterkey");
$targetDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $batchSize = 250; // Velikost dávky
    $startRow = 1;    // Počáteční řádek (Firebird používá 1-based index)

    while (true) {
        // Načítání dávky dat z původní databáze
        $query = $sourceDb->prepare("SELECT * FROM ZAZNAMDAT ROWS :start TO :end");
        $query->execute([
            ':start' => $startRow,
            ':end' => $startRow + $batchSize - 1
        ]);
        $records = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($records)) {
            break;  // Konec, pokud nejsou žádná data
        }

        // Přidání nových dat do cílové databáze
        $stmt = $targetDb->prepare("
            INSERT INTO ZAZNAMDAT (TYPEL, POSITIONL, LCS, FCS, CREATEYEAR, POSITIONYEAR, DATETIME, DATADIAG)
            VALUES (:TYPEL, :POSITIONL, :LCS, :FCS, :CREATEYEAR, :POSITIONYEAR, :DATETIME, :DATADIAG)
        ");

        foreach ($records as $record) {

            // Kontrola, zda záznam již existuje
            $checkQuery = $targetDb->prepare("
                SELECT COUNT(*) 
                FROM ZAZNAMDAT 
                WHERE TYPEL = :TYPEL 
                AND POSITIONL = :POSITIONL 
                AND LCS = :LCS 
                AND FCS = :FCS 
                AND CREATEYEAR = :CREATEYEAR
            ");
            $checkQuery->execute([
                'TYPEL' => $record['TYPEL'],
                'POSITIONL' => $record['POSITIONL'],
                'LCS' => $record['LCS'],
                'FCS' => $record['FCS'],
                'CREATEYEAR' => $record['CREATEYEAR']
            ]);

            $existingRecordCount = $checkQuery->fetchColumn();

            if ($existingRecordCount == 0) {
                // Oprava DATETIME pro Firebird
                $record['DATETIME'] = date('Y-m-d H:i:s', strtotime($record['DATETIME']));

                // Oprava DATADIAG, pokud je NULL
                $record['DATADIAG'] = is_null($record['DATADIAG']) ? null : $record['DATADIAG'];

                // Vložení nového záznamu
                $stmt->execute([
                    'TYPEL' => $record['TYPEL'],
                    'POSITIONL' => $record['POSITIONL'],
                    'LCS' => $record['LCS'],
                    'FCS' => $record['FCS'],
                    'CREATEYEAR' => $record['CREATEYEAR'],
                    'POSITIONYEAR' => $record['POSITIONYEAR'],
                    'DATETIME' => $record['DATETIME'],
                    'DATADIAG' => $record['DATADIAG']
                ]);
            }
        }

        $startRow += $batchSize;  // Posun na další dávku
    }

    // Výběr dat z cílové databáze
    $query = $targetDb->query("SELECT * FROM ZAZNAMDAT");
    $records = $query->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Data byla úspěšně přenesena do STARMONTRACKER.fdb</h2>";

    header('Content-Type: application/json');
    echo json_encode($records, JSON_PRETTY_PRINT);

    // Funkce pro práci s daty
    function getTotalRecords($records)
    {
        return count($records);
    }

    function deleteAllRecords()
    {
        global $targetDb;
        $targetDb->exec("DELETE FROM ZAZNAMDAT");
    }

    function getSto($records)
    {
        return array_slice($records, 0, 100);
    }

    function getPadesat($records)
    {
        return array_slice($records, 0, 50);
    }

    function getDeset($records)
    {
        return array_slice($records, 0, 10);
    }

    function getFirstRecord($records)
    {
        return isset($records[0]) ? $records[0] : null;
    }

    function getLastRecord($records)
    {
        return end($records);
    }

    // Zpracování akcí z GET parametru
    if (isset($_GET['akce'])) {
        $akce = $_GET['akce'];

        switch ($akce) {
            case 'pocet':
                echo json_encode(array("pocet" => getTotalRecords($records)), JSON_PRETTY_PRINT);
                break;

            case 'sto':
                $firstHundredRecords = getSto($records);
                echo json_encode($firstHundredRecords, JSON_PRETTY_PRINT);
                break;

            case 'padesat':
                $firstFiftyRecords = getPadesat($records);
                echo json_encode($firstFiftyRecords, JSON_PRETTY_PRINT);
                break;

            case 'prvni':
                $firstRecord = getFirstRecord($records);
                echo json_encode($firstRecord ? $firstRecord : array("error" => "Žádný záznam nenalezen."), JSON_PRETTY_PRINT);
                break;

            case 'deset':
                $firstTenRecords = getDeset($records);
                echo json_encode($firstTenRecords, JSON_PRETTY_PRINT);
                break;

            case 'smazatvse':
                deleteAllRecords();
                echo json_encode(array("success" => "Všechny záznamy byly smazány."), JSON_PRETTY_PRINT);
                break;

            case 'posledni':
                $lastRecord = getLastRecord($records);
                echo json_encode($lastRecord ? $lastRecord : array("error" => "Žádný záznam nenalezen."), JSON_PRETTY_PRINT);
                break;

            case 'vse':
                echo json_encode($records, JSON_PRETTY_PRINT);
                break;

            default:
                echo json_encode(array("error" => "Neplatná akce"), JSON_PRETTY_PRINT);
                break;
        }
    } else {
        echo json_encode(array("error" => "Neplatný požadavek"), JSON_PRETTY_PRINT);
    }

} catch (Exception $e) {
    echo json_encode(array("error" => "Chyba: " . $e->getMessage()), JSON_PRETTY_PRINT);
}

?>
