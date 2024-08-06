<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/formstyle.css">
    <title>Záznamy výskytu</title>
</head>

<body>
    <div class="smallBox"> <!--Formulář pro zadání informací -->
        <form action="../includes/historyformhandler.inc.php" method="post" autocomplete="off"> 
            <input type="text" id="itemID" name="itemID" placeholder="ID prvku">
            <br>
            <input type="text" id="locationID" name="locationID" placeholder="ID lokace">
            <br>
            <input type="text" id="date" name="date" placeholder="Datum výskytu (YYYY-MM-DD)">
            <br>
            <input type="text" id="historyIndex" name="historyIndex" placeholder="Příznak?">
            <br><br>
            <button type="submit" value="submit">Odeslat</button>
        </form>
        <a href="../index.php"><button>Zpět</button></a>
    </div>

    <div class="rowBox">
        <table rules="all"> <!--Tabulka vypisujici veskere zaznamy v tabulce History-->
                <th>ID Zápisu</th>
                <th>ID Prvku</th>
                <th>Lokace</th>
                <th>Pozice</th>
                <th>První výskyt</th>
                <th>Poslední výskyt</th>

                <?php
                try {
                    require_once ("../includes/dbh.inc.php");
                    //Vypíše všechny záznamy z tabulky History
                    $query = "SELECT History.id, History.ItemID, LocationDictionary.LName, History.FirstSeen, History.LastSeen, History.HIndex FROM History INNER JOIN Locations ON History.LocationID = Locations.id INNER JOIN LocationDictionary ON Locations.TypeL = LocationDictionary.TypeL;";
                    $stmt = $pdo->prepare($query);
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
                ?>
        </table>

        <table rules="all"> <!--Tabulka vypisujici veskere aktualni prvky v tabulkce Items-->
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

        <table rules="all"> <!--Tabulka vypisujici veskere aktualni lokace v tabulce Locations-->
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