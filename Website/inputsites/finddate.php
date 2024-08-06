<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Najít datum</title>
    <link rel="stylesheet" href="../styles/formstyle.css">
</head>

<body>
    <div class="smallBox">
        <form action="finddate.php" method="post" autocomplete="off">
            <input type="text" id="date" name="date" placeholder="Datum (YYYY-MM-DD)">
            <br><br>
            <button type="submit" value="submit">Odeslat</button>
        </form>
        <a href="../index.php"><button>Zpět</button></a>
    </div>
    <div class="rowBox">
        <table rules="all">
            <th>ID Prvku</th>
            <th>Z lokace</th>
            <th>Z pozice</th>
            <th>Do lokace</th>
            <th>Do pozice</th>
            <th>Datum</th>

        <?php
        try {
            require_once ("../includes/dbh.inc.php");
            $date = $_POST['date'];
            $locationFrom = "";
            $locationTo = "";

            $query = "SELECT t1.ItemID, ld1.LName AS FromLocationName, t1.HIndex AS FromHIndex, ld2.LName AS ToLocationName, t2.HIndex AS ToHIndex, t1.LastSeen AS MoveDate FROM History t1 JOIN History t2 ON t1.ItemID = t2.ItemID AND t1.LastSeen = t2.FirstSeen JOIN LocationDictionary ld1 ON t1.LocationID = ld1.TypeL JOIN LocationDictionary ld2 ON t2.LocationID = ld2.TypeL WHERE t1.LastSeen = :date;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":date", $date, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>". htmlspecialchars($row['ItemID']). "</td>";
                echo "<td>". htmlspecialchars($row['FromLocationName']). "</td>";
                echo "<td>". htmlspecialchars($row['FromHIndex']). "</td>";
                echo "<td>". htmlspecialchars($row['ToLocationName']). "</td>";
                echo "<td>". htmlspecialchars($row['ToHIndex']). "</td>";
                echo "<td>". htmlspecialchars($row['MoveDate']). "</td>";
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