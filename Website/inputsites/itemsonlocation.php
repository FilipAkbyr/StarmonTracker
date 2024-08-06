<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/formstyle.css">
    <title>Výpis prvků v lokaci</title>
</head>

<body>
    <div class="smallBox"> <!--Tabulka pro zadání požadovaného LocationID-->
        <form action="itemsonlocation.php" method="post" autocomplete="off">
            <input type="text" id="locationID" name="locationID" placeholder="ID lokace"><br>
            <label for="onlyCurrent">Pouze aktuální prvky</label>
            <input type="checkbox" id="onlyCurrent" name="onlyCurrent">
            <br><br>
            <button type="submit" value="submit">Odeslat</button>
        </form>
        <a href="../index.php"><button>Zpět</button></a>
        <p id="itemCounter">Celkem prvků v lokaci:</p>
    </div>
    <div class="smallBox"> <!--Tabulka pro vypsání History-->
        <table rules="all">
            <th>ID zápisu</th>
            <th>ID prvku</th>
            <th>Lokace</th>
            <th>Pozice</th>
            <th>První výskyt</th>
            <th>Poslední výskyt</th>
            <?php
            $locationID = $_POST["locationID"];
            $onlyCurrent = $_POST["onlyCurrent"];

            if (is_numeric($locationID) && $locationID >= 0) {
                try {
                    require_once ("../includes/dbh.inc.php"); //Najde všechny nezakončené záznamy výskytu prvku v dané lokaci
                    if($onlyCurrent){
                        $query = "SELECT History.id, HIndex, ItemID, LName, FirstSeen, LastSeen FROM History INNER JOIN Locations ON History.LocationID = Locations.id INNER JOIN LocationDictionary ON Locations.TypeL = LocationDictionary.TypeL WHERE History.LocationID = :locationID AND LastSeen IS NULL;";
                    }
                    else{
                        $query = "SELECT History.id, HIndex, ItemID, LName, FirstSeen, LastSeen FROM History INNER JOIN Locations ON History.LocationID = Locations.id INNER JOIN LocationDictionary ON Locations.TypeL = LocationDictionary.TypeL WHERE History.LocationID = :locationID;";
                    }
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":locationID", $locationID, PDO::PARAM_INT);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $numberOfItems = 0;

                    foreach ($result as $row) {
                        $numberOfItems += 1;
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ItemID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['LName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['HIndex']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FirstSeen']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['LastSeen']) . "</td>";
                        echo "</tr>";
                    }

                    $query = "SELECT LName FROM LocationDictionary WHERE TypeL = :locationID;";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(":locationID", $locationID, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $locationName = $result[0]['LName'];


                } catch (PDOException $e) {
                    die("Query failed:" . $e->getMessage());
                }
            }
            ?>
        </table>
    </div>

</body>

</html>

<script> //Script pro aktualizaci jména lokace a počtu prvků
    var numberOfItems = <?php echo json_encode($numberOfItems, JSON_HEX_TAG); ?>;
    var nameOfLocation = <?php echo json_encode($locationName, JSON_HEX_TAG); ?>;
    document.getElementById('itemCounter').innerHTML = `Celkem prvků v ${nameOfLocation}: ${numberOfItems}`; 
</script>