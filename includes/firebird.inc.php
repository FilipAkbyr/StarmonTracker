<?php
require_once("../includes/dbh.inc.php");

$sourceDbConfig = [
    'dsn' => 'firebird:dbname=localhost:D:\Prace\STARMONTRACKER.fdb',
    'user' => 'SYSDBA',
    'password' => 'masterkey'
];

try {
    // Připojení k databázi
    $sourceDb = new PDO($sourceDbConfig['dsn'], $sourceDbConfig['user'], $sourceDbConfig['password']);
    $sourceDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Načtení JSON souboru
    $jsonFile = 'data.json';
    if (!file_exists($jsonFile)) {
        echo json_encode(["error" => "JSON soubor neexistuje."]);
        exit;
    }

    $jsonData = file_get_contents($jsonFile);
    $records = json_decode($jsonData, true);

    if (empty($records)) {
        echo json_encode(["error" => "Soubor je prázdný nebo neobsahuje validní data."]);
        exit;
    }

    // Smazání starých dat před importem
    // $sourceDb->exec("DELETE FROM ZAZNAMDAT");

    // Přidání nových dat
    $stmt = $sourceDb->prepare("
        INSERT INTO ZAZNAMDAT (TYPEL, POSITIONL, LCS, FCS, CREATEYEAR, POSITIONYEAR, DATETIME, DATADIAG)
        VALUES (:TYPEL, :POSITIONL, :LCS, :FCS, :CREATEYEAR, :POSITIONYEAR, :DATETIME, :DATADIAG)
    ");

    foreach ($records as $record) {
        $stmt->execute([
            ':TYPEL' => $record['TYPEL'],
            ':POSITIONL' => $record['POSITIONL'],
            ':LCS' => $record['LCS'],
            ':FCS' => $record['FCS'],
            ':CREATEYEAR' => $record['CREATEYEAR'],
            ':POSITIONYEAR' => $record['POSITIONYEAR'],
            ':DATETIME' => $record['DATETIME'],
            ':DATADIAG' => $record['DATADIAG']
        ]);
    }

    // Výběr dat z tabulky
    $query = $sourceDb->query("SELECT * FROM ZAZNAMDAT");
    $records = $query->fetchAll(PDO::FETCH_ASSOC);

    // Funkce pro zobrazení celkového počtu záznamů
    function getTotalRecords($records) {
        return count($records);
    }

    // Funkce pro smazání všech záznamů
    function deleteAllRecords($records) {
        global $sourceDb;
        $sourceDb->exec("DELETE FROM ZAZNAMDAT");
    }

    // Funkce pro zobrazení prvního záznamu
    function getFirstRecord($records) {
        return isset($records[0]) ? $records[0] : null;
    }

    // Funkce pro zobrazení posledního záznamu
    function getLastRecord($records) {
        return isset($records[count($records) - 1]) ? $records[count($records) - 1] : null;
    }

    // Ověření, zda byl odeslán požadavek POST a obsahuje parametr 'akce'
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akce'])) {
        $akce = $_POST['akce'];

        // Zpracování požadavku podle hodnoty 'akce'
        switch ($akce) {
            case 'pocet':
                echo json_encode(array("pocet" => getTotalRecords($records)));
                break;

            case 'prvni':
                $firstRecord = getFirstRecord($records);
                echo json_encode($firstRecord ? $firstRecord : array("error" => "Žádný záznam nenalezen."));
                break;

            case 'sto':
                $firstHundredRecords = array_slice($records, 0, 100);
                echo json_encode($firstHundredRecords);
                break;

            case 'padesat':
                $firstFiftyRecords = array_slice($records, 0, 50);
                echo json_encode($firstFiftyRecords);
                break;

            case 'deset':
                $firstTenRecords = array_slice($records, 0, 10);
                echo json_encode($firstTenRecords);
                break;


            case 'smazatvse':
                deleteAllRecords();
                echo json_encode(array("success" => "Všechny záznamy byly smazány."));
                break;

            case 'posledni':
                $lastRecord = getLastRecord($records);
                echo json_encode($lastRecord ? $lastRecord : array("error" => "Žádný záznam nenalezen."));
                break;

            case 'vse':
                echo json_encode($records);
                break;

            default:
                echo json_encode(array("error" => "Neplatná akce"));
                break;
        }
    } else {
        echo json_encode(["success" => "Data byla úspěšně nahrána do STARMONTRACKER.fdb"]);
    }

} catch (Exception $e) {
    echo json_encode(["error" => "Chyba při importu dat: " . $e->getMessage()]);
}
?>
