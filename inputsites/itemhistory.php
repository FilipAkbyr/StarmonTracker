<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/formstyle.css">
    <title>Historie prvku</title>
</head>

<body>
    <div class="smallBox">
        <form action="itemhistory.php" method="post" autocomplete="off">
            <input type="text" id="itemID" name="itemID" placeholder="ID hledaného prvku">
            <br><br>
            <button type="submit" value="submit">Odeslat</button>
        </form>
    </div>
    <a href="../index.php"><button>Zpět</button></a>

    <?php


    ?>
    <div>
        <h2>Výskyt</h2>
        <table rules="all"> <!--Tabulka vypisujici veskere aktualni lokace v databazi-->
            <th>ID Zápisu</th>
            <th>ID Prvku</th>
            <th>Lokace</th>
            <th>Pozice</th>
            <th>První výskyt</th>
            <th>Poslední výskyt</th>
            <?php
                //vezme data z formulare, pokud data nesplnuji omezeni, vyhodi chybu, a vrati zpet na formular
                $itemID = isset($_POST["itemID"]) ? $_POST["itemID"] : null;

                if (is_numeric($itemID) && $itemID >= 0) {
                    try {
                        require_once ("../includes/dbh.inc.php");
                        $query = "SELECT History.id, History.ItemID, LocationDictionary.LName, History.FirstSeen, History.LastSeen, History.HIndex FROM History INNER JOIN Locations ON History.LocationID = Locations.id INNER JOIN LocationDictionary ON Locations.TypeL = LocationDictionary.TypeL WHERE History.ItemID = :itemID;";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":itemID", $itemID, PDO::PARAM_INT);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                        foreach ($result as $row) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['ItemID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['LName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['HIndex']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['FirstSeen']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['LastSeen']) . "</td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        die("Query failed:" . $e->getMessage());
                    }
                }
            ?>
        </table>
                <h2>Opravy</h2>
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
                    $query = "SELECT MaintenanceLog.id, MaintenanceLog.ItemID, LocationDictionary.LName, MaintenanceLog.FailureDate, MaintenanceLog.FailureCode, MaintenanceLog.FailureDesc, MaintenanceLog.RepairDesc FROM MaintenanceLog INNER JOIN Locations ON MaintenanceLog.LocationID = Locations.id INNER JOIN LocationDictionary ON Locations.TypeL = LocationDictionary.TypeL WHERE ItemID = :itemID;";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(":itemID", $itemID, PDO::PARAM_INT);
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
    </div>


</body>

</html>