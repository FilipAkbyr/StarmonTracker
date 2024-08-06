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
    $locationID = $_POST["locationID"];
    if ($locationID == "" || !is_numeric($locationID)) {
        $errmsg .= "ID lokace, ";
        $throwerr = true;
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
            $rowID = $result[0]['id'];
            $locationLog = $result[0]['LocationID'];
            $indexLog = $result[0]['HIndex'];

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