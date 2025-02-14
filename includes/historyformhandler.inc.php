<?php
include ("functions.inc.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errmsg = "Nastala chyba v zadávání: ";
    $throwerr = false; //Urcuje zda ma byt zadani informaci provedeno znovu

    //vezme data z formulare, pokud data nesplnuji omezeni, vyhodi chybu, a vrati zpet na formular
    $itemID = $_POST["itemID"];
    if ($itemID == "" || !is_numeric($itemID)) {
        $errmsg .= "ID prvku, ";
        $throwerr = true;
    }
//    $locationID = $_POST["locationID"];
//    if ($locationID == "" || !is_numeric($locationID)) {
//        $errmsg .= "ID lokace, ";
//        $throwerr = true;
//    }
        if (!empty($_POST['itemID']) && isset($_POST['locationID']) && is_array($_POST['locationID'])) {

        $itemID = htmlspecialchars($_POST['itemID'], ENT_QUOTES, 'UTF-8'); // Item ID (ID prvku)
        $locations = $_POST['locationID']; // Array of selected locations (already an array due to is_array check)

        // Sanitize and validate dates
        $firstSeen = !empty($_POST['date']) ? htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8') : null;
        $lastSeen = !empty($_POST['lastseendate']) ? htmlspecialchars($_POST['lastseendate'], ENT_QUOTES, 'UTF-8') : null;
        $historyIndex = !empty($_POST['historyIndex']) ? htmlspecialchars($_POST['historyIndex'], ENT_QUOTES, 'UTF-8') : null;
         
        try {
            // Database connection
            require_once("../includes/dbh.inc.php"); 

            // Loop through each selected location and insert it into the History table
            foreach ($locations as $locationID) {
                // Sanitize each location ID
                $locationID = htmlspecialchars($locationID, ENT_QUOTES, 'UTF-8');

                // Prepare and execute the insert query
                $query = "INSERT INTO History (ItemID, LocationID, FirstSeen, LastSeen, HIndex) 
                      VALUES (:itemID, :locationID, :firstSeen, :lastSeen, :historyIndex)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':itemID' => $itemID,
                    ':locationID' => $locationID,
                    ':firstSeen' => $firstSeen,
                    ':lastSeen' => $lastSeen,
                    ':historyIndex' => $historyIndex
                ]);
            }

            echo "Data successfully saved to the database.";

        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }

    } else {
        echo "Please provide an Item ID and select at least one location.";
    }

    $date = $_POST["date"];
    if ($date == "" || !validateDate($date)) {
        $errmsg .= "Datum výskytu, ";
        $throwerr = true;
    }
    $lastseendate = $_POST["lastseendate"];
    if ($lastseendate == "" || !validateDate($lastseendate)) {
        $errmsg .= "Datum posledního výskytu, ";
        $throwerr = true;
    }
    $index = $_POST["historyIndex"];
    if ($index < 1 || $index > 255) {
        $errmsg .= "Příznak";
        $throwerr = true;
    }


    if ($throwerr) { //Pokud data nesplnuji omezeni, vyhodi chybu, a vrati zpet
        echo $errmsg;
        echo "<br>";
        echo "<a href=\"../inputsites/historyform.php\"><button>Zpět</button></a>";
        die();
    }

    $typeL = 0;

    try {
        require_once ("dbh.inc.php");
        $query = "SELECT * FROM History WHERE ItemID = :ItemID;"; //Pokud daný prvek ještě nemá záznam výskytu, vytvoří nový
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":ItemID", $itemID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) == 0) { //Vytvoří nový záznam
            $query = "INSERT INTO History (ItemID, LocationID, FirstSeen, LastSeen, HIndex) VALUES (:ItemID, :LocationID, :FirstSeen, :LastSeen, :HIndex);";
            $stmt = $pdo->prepare($query);
            //Dosadí proměnné
            $stmt->bindParam(":ItemID", $itemID, PDO::PARAM_INT);
            $stmt->bindParam(":LocationID", $locationID, PDO::PARAM_INT);
            $stmt->bindParam(":FirstSeen", $date, PDO::PARAM_STR);
            $stmt->bindParam(":LastSeen", $lastseendate, PDO::PARAM_STR);
            $stmt->bindParam(":HIndex", $index, PDO::PARAM_INT);
            $stmt->execute();
        } else { //Najde poslední (nedokončený) záznam výskytu prvku
            $query = "SELECT id, LocationID, HIndex FROM History WHERE ItemID = :ItemID AND FirstSeen <= :Date AND LastSeen IS NULL;";
            $stmt = $pdo->prepare($query);
            //Dosadí proměnné
            $stmt->bindParam(":ItemID", $itemID, PDO::PARAM_INT);
            $stmt->bindParam(":Date", $date, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($result)) {
                $rowID = $result[0]['id'];
                $locationLog = $result[0]['LocationID'];
                $indexLog = $result[0]['HIndex'];
            } else {
                echo "No matching records found in the History table.";
            }

            if ($locationLog != $locationID || $indexLog != $index) {
                $query = "UPDATE History SET LastSeen = :LastSeen WHERE id = :id;";
                $stmt = $pdo->prepare($query);
                //Dosadí proměnné
                $stmt->bindParam(":LastSeen", $lastseendate, PDO::PARAM_STR);
                $stmt->bindParam(":id", $rowID, PDO::PARAM_INT);
                $stmt->execute();

                $query = "INSERT INTO History (ItemID, LocationID, FirstSeen, LastSeen, HIndex) VALUES (:ItemID, :LocationID, :FirstSeen, :LastSeen, :HIndex);";
                $stmt = $pdo->prepare($query);
                //Dosadí proměnné
                $stmt->bindParam(":ItemID", $itemID, PDO::PARAM_INT);
                $stmt->bindParam(":LocationID", $locationID, PDO::PARAM_INT);
                $stmt->bindParam(":FirstSeen", $date, PDO::PARAM_STR);
                $stmt->bindParam(":LastSeen", $lastseendate, PDO::PARAM_STR);
                $stmt->bindParam(":HIndex", $index, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        $pdo = null;
        $stmt = null;
        header("Location: ../inputsites/historyform.php");
        die();
    } catch (PDOException $e) {
        die("Query failed:" . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    die();
}