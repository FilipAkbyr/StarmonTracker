<?php
session_start();
require_once("../includes/dbh.inc.php");

$sourceDbConfig = [
    'dsn' => 'firebird:dbname=localhost:D:\Prace\STARMONTRACKER.fdb',
    'user' => 'SYSDBA',
    'password' => '123456789'
];

$sourceDb = null;



try {
    // Připojení k databázi
    $sourceDb = new PDO($sourceDbConfig['dsn'], $sourceDbConfig['user'], $sourceDbConfig['password']);
    $sourceDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ověření, zda byl odeslán požadavek POST a obsahuje parametr 'akce'
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akce'])) {
        $akce = $_POST['akce'];

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

        // Zpracování požadavku podle hodnoty 'akce'
        switch ($akce) {
            case 'pocet':
                echo json_encode(["pocet" => getTotalRecords($records)]);
                break;

            case 'prvni':
                $firstRecord = getFirstRecord($records);
                echo json_encode($firstRecord ?: ["error" => "Žádný záznam nenalezen."]);
                break;

            case 'smazatvse':
                deleteAllRecords($records);
                echo json_encode(["success" => "Všechny záznamy byly smazány."]);
                break;

            case 'posledni':
                $lastRecord = getLastRecord($records);
                echo json_encode($lastRecord ?: ["error" => "Žádný záznam nenalezen."]);
                break;

            case 'vse':
                echo json_encode($records);
                break;

            default:
                echo json_encode(["error" => "Neplatná akce"]);
                break;
        }
    } else {
        echo json_encode(["error" => "Chybný požadavek"]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Chyba: " . $e->getMessage()]);
}
?>
