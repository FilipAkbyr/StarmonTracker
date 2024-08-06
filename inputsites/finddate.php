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
    <form action="" method="post" autocomplete="off">
        <input type="text" id="date" name="date" placeholder="Datum (YYYY-MM-DD)">
        <br><br>
        <button type="submit" value="submit">Odeslat</button>
    </form>
</div>
<a href="../index.php"><button>Zpět</button></a>
<div class="rowBox">
    <table rules="all">
        <tr>
            <th>ID Prvku</th>
            <th>Z lokace</th>
            <th>Z pozice</th>
            <th>Do lokace</th>
            <th>Do pozice</th>
            <th>Datum</th>
        </tr>
        <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['date'])) {
            try {
                require_once ("../includes/dbh.inc.php");

                $date = $_POST['date'];
                echo "Submitted date: " . htmlspecialchars($date) . "<br>";

                $query = "SELECT 
                            t1.ItemID, 
                            ld1.LName AS FromLocationName, 
                            t1.HIndex AS FromHIndex, 
                            ld2.LName AS ToLocationName, 
                            t2.HIndex AS ToHIndex, 
                            t1.LastSeen AS MoveDate 
                          FROM 
                            History t1 
                          JOIN 
                            History t2 
                          ON 
                            t1.ItemID = t2.ItemID 
                          JOIN 
                            LocationDictionary ld1 
                          ON 
                            t1.LocationID = ld1.TypeL 
                          JOIN 
                            LocationDictionary ld2 
                          ON 
                            t2.LocationID = ld2.TypeL č
                          WHERE 
                            t1.LastSeen = :date";

                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":date", $date, PDO::PARAM_STR);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($result) {
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ItemID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FromLocationName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FromHIndex']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ToLocationName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ToHIndex']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['MoveDate']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No results found for the selected date.</td></tr>";
                }

            } catch (PDOException $e) {
                echo "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo "<tr><td colspan='6'>Please enter a date.</td></tr>";
            }
        }
        ?>
    </table>
</div>
</body>
</html>