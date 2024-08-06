<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errmsg = "Nastala chyba v zadávání: ";
    $throwerr = false; //Urcuje zda ma byt zadani informaci provedeno znovu

    //vezme data z formulare, pokud data nesplnuji omezeni, vyhodi chybu, a vrati zpet na formular
    $itemName = $_POST["itemName"];
    if ($itemName == "") {
        $errmsg .= "Jméno, ";
        $throwerr = true;
    }
    $itemNumber = $_POST["itemNumber"];
    if (!is_numeric($itemNumber) || $itemNumber < 1 || $itemNumber > 999) {
        $errmsg .= "Číslo, ";
        $throwerr = true;
    }
    $itemYear = $_POST["itemYear"];
    if (!is_numeric($itemYear) || $itemYear < 2000 || $itemYear > 9999) {
        $errmsg .= "Rok, ";
        $throwerr = true;
    }
    $itemState = $_POST["itemState"];
    if (!is_numeric($itemState) || $itemState < 1 || $itemState > 255) {
        $errmsg .= "Stav";
        $throwerr = true;
    }

    if ($throwerr) { //Pokud data nesplnuji omezeni, vyhodi chybu, a vrati zpet
        echo $errmsg;
        echo "<br>";
        echo "<a href=\"../inputsites/itemform.php\"><button>Zpět</button></a>";
        die();
    }

    $typeI = 0;

    try {
        require_once ("dbh.inc.php");
        $pdo->beginTransaction();

        //Kontroluje, jestli je zadané jméno již ve slovníku
        $query = "SELECT * FROM ItemDictionary WHERE IName = :itemName;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":itemName", $itemName, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) == 0) {

            //Pokud není, vloží jmeno prvku do tabulky ItemDictionary
            $query = "INSERT INTO ItemDictionary (IName) VALUES (:itemName);";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":itemName", $itemName, PDO::PARAM_STR);
            $stmt->execute();
        }
        //Vezme z tabulky ItemDictionary hodnotu TypeI odpovidajici jmenu prvku a ulozi ji do $TypeI
        $query = "SELECT * FROM ItemDictionary WHERE IName = :itemName;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":itemName", $itemName, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $typeI = $result[0]['TypeI'];

        //inserte informace do tabulky Items, s TypeI odkazujicim na jmeno v ItemDictionary
        $query = "INSERT INTO Items (TypeI, INumber, IYear, IState) VALUES (:TypeI, :INumber, :IYear, :IState);";
        $stmt = $pdo->prepare($query);
        //Dosadí proměnné
        $stmt->bindParam(":TypeI", $typeI, PDO::PARAM_INT);
        $stmt->bindParam(":INumber", $itemNumber, PDO::PARAM_INT);
        $stmt->bindParam(":IYear", $itemYear, PDO::PARAM_INT);
        $stmt->bindParam(":IState", $itemState, PDO::PARAM_INT);
        $stmt->execute();

        $pdo->commit();

        $pdo = null;
        $stmt = null;


        header("Location: ../inputsites/itemform.php");
        die();
    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: ../inputsites/itemform.php");
        die("Query failed:" . $e->getMessage());
    }


} else {
    header("Location: ../index.php");
    die();
}