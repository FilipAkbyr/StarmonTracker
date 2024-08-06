<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/formstyle.css">
    <title>Záznamy opravy</title>
</head>

<body>
    <div class="smallBox"> <!--Formular pro zadání nového zápisu do tabulky MaintenanceLog-->
        <form action="../includes/maintenanceformhandler.inc.php" method="post" autocomplete="off">
            <input type="text" id="itemID" name="itemID" placeholder="ID prvku">
            <br>
            <input type="text" id="locationID" name="locationID" placeholder="ID lokace">
            <br>
            <input type="text" id="failureDate" name="failureDate" placeholder="Datum (YYYY-MM-DD)">
            <br>
            <input type="text" id="failureCode" name="failureCode" placeholder="Kód poruchy">
            <br><br>
            <textarea id="failureDesc" name="failureDesc" placeholder="Popis poruchy"></textarea>
            <textarea id="repairDesc" name="repairDesc" placeholder="Popis opravy"></textarea>
            <br><br>
            <button type="submit" value="submit">Odeslat</button>
        </form>
        <a href="../index.php"><button>Zpět</button></a>
    </div>

    <div class="rowBox">
        <table rules="all"> <!--Tabulka vypisujici veskere aktualni lokace v databazi-->
                <th>ID Zápisu</th>
                <th>ID Prvku</th>
                <th>ID Lokace</th>
                <th>Datum poruchy</th>
                <th>Kód poruchy</th>
                <th>Popis poruchy</th>
                <th>Popis opravy</th>
                <?php
                try {
                    require_once ("../includes/dbh.inc.php");
                    $query = "SELECT MaintenanceLog.id, MaintenanceLog.ItemID, LocationDictionary.LName, MaintenanceLog.FailureDate, MaintenanceLog.FailureCode, MaintenanceLog.FailureDesc, MaintenanceLog.RepairDesc FROM MaintenanceLog INNER JOIN Locations ON MaintenanceLog.LocationID = Locations.id INNER JOIN LocationDictionary ON Locations.TypeL = LocationDictionary.TypeL ORDER BY FailureDate;";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ItemID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['LName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FailureDate']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FailureCode']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FailureDesc']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['RepairDesc']) . "</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    die("Query failed:" . $e->getMessage());
                }
                ?>
        </table>

        <table rules="all"> <!--Tabulka vypisujici veskere aktualni prvky v databazi-->
                <th>ID</th>
                <th>Jméno</th>
                <th>Číslo</th>
                <th>Rok</th>
                <th>Stav</th>
                <?php
                try {
                    require_once ("../includes/dbh.inc.php");
                    $query = "SELECT Items.id, ItemDictionary.IName, Items.INumber, Items.IYear, Items.IState FROM Items INNER JOIN ItemDictionary ON Items.TypeI=ItemDictionary.TypeI;";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['IName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['INumber']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['IYear']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['IState']) . "</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    die("Query failed:" . $e->getMessage());
                }
                ?>
        </table>

        <table rules="all"> <!--Tabulka vypisujici veskere aktualni lokace v databazi-->
                <th>ID</th>
                <th>Jméno</th>
                <th>LCS</th>
                <th>FCS</th>
                <th>Typ</th>
                <th>Pozice</th>
                <th>Popis</th>
                <?php
                try {
                    require_once ("../includes/dbh.inc.php");
                    $query = "SELECT Locations.id, LocationDictionary.LName, Locations.LCS, Locations.FCS, Locations.LClass, Locations.LPosition, Locations.LDescription FROM Locations INNER JOIN LocationDictionary ON Locations.TypeL=LocationDictionary.TypeL;";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['LName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['LCS']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FCS']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['LClass']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['LPosition']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['LDescription']) . "</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    die("Query failed:" . $e->getMessage());
                }
                ?>
        </table>
    </div>
</body>

</html>