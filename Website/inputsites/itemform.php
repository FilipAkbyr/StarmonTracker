<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/formstyle.css">
    <title>Prvky</title>
</head>

<body>
    <div class="smallBox">
        <form action="../includes/itemformhandler.inc.php" method="post" autocomplete="off"> <!--Formular pro zadavani informaci -->
            <input type="text" id="itemName" name="itemName" placeholder="Jméno">
            <br>
            <input type="text" id="itemNumber" name="itemNumber" placeholder="Číslo (1-999)">
            <br>
            <input type="text" id="itemYear" name="itemYear" placeholder="Rok (2000-9999)">
            <br>
            <input type="text" id="itemState" name="itemState" placeholder="Stav (1-255)">
            <br><br>
            <button type="submit" value="submit">Odeslat</button>
        </form>
        <a href="../index.php"><button>Zpět</button></a>
    </div>
    <div class="smallBox">
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
    </div>


</body>

</html>