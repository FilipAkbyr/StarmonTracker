<?php

// Konfigurace připojení k databázím
$sourceConnStr = "firebird:dbname=192.168.30.57:C:\WEB\database\DIAGOC.fdb;host=localhost";
$targetConnStr = "firebird:dbname=192.168.30.127:D:\\Prace\\STARMONTRACKER.fdb;host=localhost";

// Připojení k původní databázi
$sourceDb = new PDO($sourceConnStr, "SYSDBA", "masterkey");
$sourceDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($sourceDb)
{
    echo "Success ";
}
else
{
    echo "Failed ";
}

// Připojení k cílové databázi
$targetDb = new PDO($targetConnStr, "SYSDBA", "masterkey");
$targetDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($targetDb)
{
    echo "Success";
}
else
{
    echo "Failed";
}

try {

    $query = $sourceDb->query("SELECT * FROM ZAZNAMDAT ROWS 1 TO 100");
    $records = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($records)) {
        echo json_encode(array("error" => "Žádná data k přenosu."));
        exit;
    }

    // Vymazání dat v cílové databázi (pokud je třeba)
    // $targetDb->exec("DELETE FROM ZAZNAMDAT");

    // Přidání nových dat do cílové databáze
    $stmt = $targetDb->prepare("
        INSERT INTO ZAZNAMDAT (TYPEL, POSITIONL, LCS, FCS, CREATEYEAR, POSITIONYEAR, DATETIME, DATADIAG)
        VALUES (:TYPEL, :POSITIONL, :LCS, :FCS, :CREATEYEAR, :POSITIONYEAR, :DATETIME, :DATADIAG)
    ");

    foreach ($records as $record) {
        // Oprava DATETIME pro Firebird
        $record['DATETIME'] = date('Y-m-d H:i:s', strtotime($record['DATETIME']));

        // Oprava DATADIAG, pokud je NULL
        $record['DATADIAG'] = is_null($record['DATADIAG']) ? null : $record['DATADIAG'];

        $stmt->execute(array(
            'TYPEL' => $record['TYPEL'],
            'POSITIONL' => $record['POSITIONL'],
            'LCS' => $record['LCS'],
            'FCS' => $record['FCS'],
            'CREATEYEAR' => $record['CREATEYEAR'],
            'POSITIONYEAR' => $record['POSITIONYEAR'],
            'DATETIME' => $record['DATETIME'],
            'DATADIAG' => $record['DATADIAG']
        ));
    }

    // Výběr dat z cílové databáze
    $query = $targetDb->query("SELECT * FROM ZAZNAMDAT FETCH FIRST 400 ROWS ONLY");
    $records = $query->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Data byla úspěšně přenesena do STARMONTRACKER.fdb</h2>";

    // Vykreslení tabulky
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>TYPEL</th>
            <th>POSITIONL</th>
            <th>LCS</th>
            <th>FCS</th>
            <th>CREATEYEAR</th>
            <th>POSITIONYEAR</th>
            <th>DATETIME</th>
            <th>DATADIAG</th>
          </tr>";

    foreach ($records as $row) {
        echo "<tr>";
        echo "<td>{$row['TYPEL']}</td>";
        echo "<td>{$row['POSITIONL']}</td>";
        echo "<td>{$row['LCS']}</td>";
        echo "<td>{$row['FCS']}</td>";
        echo "<td>{$row['CREATEYEAR']}</td>";
        echo "<td>{$row['POSITIONYEAR']}</td>";
        echo "<td>{$row['DATETIME']}</td>";
        echo "<td>{$row['DATADIAG']}</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Funkce pro práci s daty
    function getTotalRecords($records) {
        return count($records);
    }

    function deleteAllRecords() {
        global $targetDb;
        $targetDb->exec("DELETE FROM ZAZNAMDAT");
    }

    function getSto($records) {
        return array_slice($records, 0, 100);
    }

    function getPadesat($records) {
        return array_slice($records, 0, 50);
    }

    function getDeset($records) {
        return array_slice($records, 0, 10);
    }

    function getFirstRecord($records) {
        return isset($records[0]) ? $records[0] : null;
    }

    function getLastRecord($records) {
        return end($records);
    }

    // Zpracování akcí z požadavků POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akce'])) {
        $akce = $_POST['akce'];

        switch ($akce) {
            case 'pocet':
                echo json_encode(array("pocet" => getTotalRecords($records)));
                break;

            case 'prvni':
                $firstRecord = getFirstRecord($records);
                echo json_encode($firstRecord ? $firstRecord : array("error" => "Žádný záznam nenalezen."));
                break;

            case 'sto':
                $firstHundredRecords = getSto($records);
                echo json_encode($firstHundredRecords);
                break;

            case 'padesat':
                $firstFiftyRecords = getPadesat($records);
                echo json_encode($firstFiftyRecords);
                break;

            case 'deset':
                $firstTenRecords = getDeset($records);
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
        echo json_encode(array("success" => "Succesfully converted to STARMONTRACKER.fdb"));
    }

} catch (Exception $e) {
    echo json_encode(array("error" => "Chyba: " . $e->getMessage()));
}

?>
