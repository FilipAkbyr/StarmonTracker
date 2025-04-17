<?php
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

// Připojení k databázím
$sourceConnStr = "firebird:dbname=192.168.30.124:C:\WEB\database\DIAGOC.fdb;host=localhost";
$targetConnStr = "firebird:dbname=192.168.30.117:D:\\Prace\\STARMONTRACKER.fdb;host=localhost";

try {
    $sourceDb = new PDO($sourceConnStr, "SYSDBA", "masterkey");
    $sourceDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $targetDb = new PDO($targetConnStr, "SYSDBA", "masterkey");
    $targetDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2 style='color: #1b6ff2'>Přenos dat ze ZAZNAMDAT</h2>";


    // Výběr data
    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $filterDate = $_GET['date'];
    } else {
        $filterDate = (new DateTime('yesterday'))->format('Y-m-d');
    }
    ?>

    <!-- Formulář pro výběr data -->
    <form method="get">
        <label for="date">Zvol datum:</label>
        <input type="date" id="date" name="date" value="<?= $filterDate ?>">
        <button type="submit">Zobrazit</button>
    </form>
    <hr>

    <?php
    echo "<p>Datum filtrování: <strong>$filterDate</strong></p>";

    // Získání záznamů ze zdrojové databáze podle data z DIAGOC
    $stmt = $sourceDb->prepare("SELECT * FROM ZAZNAMDAT WHERE CAST(DATETIME AS DATE) = ?");
    $stmt->execute([$filterDate]);
    $sourceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Počet záznamů k přenosu: " . count($sourceRecords) . "</h3>";

    if (count($sourceRecords) === 0) {
        echo "<p>Žádná data k přenosu.</p>";
        exit;
    }

    echo "<table border='1' cellpadding='5' cellspacing='0'><tr>";
    if (!empty($sourceRecords)) {
        foreach (array_keys($sourceRecords[0]) as $column) {
            echo "<th>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";

        // Výpis dat
        foreach ($sourceRecords as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    // Přenos záznamů bez duplicit do STARMONTRACKER
    $inserted = 0;

    $checkStmt = $targetDb->prepare("SELECT COUNT(*) FROM ZAZNAMDAT WHERE DATETIME = ?");
    $insertStmt = $targetDb->prepare("
        INSERT INTO ZAZNAMDAT (TYPEL, POSITIONL, LCS, FCS, CREATEYEAR, POSITIONYEAR, DATETIME, DATADIAG)
        VALUES (:TYPEL, :POSITIONL, :LCS, :FCS, :CREATEYEAR, :POSITIONYEAR, :DATETIME, :DATADIAG)
    ");

    foreach ($sourceRecords as $record) {
        // Kontrola, jestli už záznam se stejným DATETIME v cílové DB existuje
        $checkStmt->execute([$record['DATETIME']]);
        $exists = $checkStmt->fetchColumn();

        if (!$exists) {
            $insertStmt->execute([
                ':TYPEL' => $record['TYPEL'],
                ':POSITIONL' => $record['POSITIONL'],
                ':LCS' => $record['LCS'],
                ':FCS' => $record['FCS'],
                ':CREATEYEAR' => $record['CREATEYEAR'],
                ':POSITIONYEAR' => $record['POSITIONYEAR'],
                ':DATETIME' => $record['DATETIME'],
                ':DATADIAG' => $record['DATADIAG']
            ]);
            $inserted++;
        }
    }

    echo "<p><strong>$inserted nových záznamů bylo úspěšně přeneseno do STARMONTRACKER.</strong></p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>Chyba: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
