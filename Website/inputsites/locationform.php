<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/formstyle.css">
    <title>Lokace</title>
</head>

<body>
    <div class="smallBox"> <!--Formular pro zadání nového zápisu do tabulky Locations-->
        <form action="../includes/locationformhandler.inc.php" method="post" autocomplete="off">
            <input type="text" id="locationName" name="locationName" placeholder="Jméno">
            <br>
            <input type="text" id="LCS" name="LCS" placeholder="LCS">
            <br>
            <input type="text" id="FCS" name="FCS" placeholder="FCS">
            <br>
            <input type="text" id="locationClass" name="locationClass" placeholder="Typ lokace">
            <br>
            <input type="text" id="locationPosi" name="locationPosi" placeholder="Pozice">
            <br><br>
            <textarea id="locationDesc" name="locationDesc" placeholder="Popis"></textarea>
            <br><br>
            <button type="submit" value="submit">Odeslat</button>
        </form>
        <a href="../index.php"><button>Zpět</button></a>
    </div>

    <div class="smallBox">
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