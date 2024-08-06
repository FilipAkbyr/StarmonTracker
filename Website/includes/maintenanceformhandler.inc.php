<?php
include("../includes/functions.inc.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errmsg = "Nastala chyba v zadávání: ";
    $throwerr = false; //Určuje zda má být zadání informací provedeno znovu

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
    $failureDate = $_POST["failureDate"];
    if ($failureDate == "" || !validateDate($failureDate)) {
        $errmsg .= "Datum poruchy, ";
        $throwerr = true;
    }
    $failureCode = $_POST["failureCode"];
    if ($failureCode == "") {
        $errmsg .= "Kód poruchy, ";
        $throwerr = true;
    }
    $failureDesc = $_POST["failureDesc"];
    $repairDesc = $_POST["repairDesc"];


    if ($throwerr) { //Pokud data nesplnuji omezeni, vyhodi chybu, a vrati zpet
        echo $errmsg;
        echo "<br>";
        echo "<a href=\"../inputsites/maintenanceform.php\"><button>Zpět</button></a>";
        die();
    }

    $typeL = 0;

    try {
        require_once ("dbh.inc.php");
        //inserte informace do tabulky MaintenanceLog
        $query = "INSERT INTO MaintenanceLog (ItemID, LocationID, FailureDate, FailureCode, FailureDesc, RepairDesc) VALUES (:ItemID, :LocationID, :FailureDate, :FailureCode, :FailureDesc, :RepairDesc);";
        $stmt = $pdo->prepare($query);
        //Dosadí proměnné
        $stmt->bindParam(":ItemID", $itemID, PDO::PARAM_INT);
        $stmt->bindParam(":LocationID", $locationID, PDO::PARAM_INT);
        $stmt->bindParam(":FailureDate", $failureDate, PDO::PARAM_STR);
        $stmt->bindParam(":FailureCode", $failureCode, PDO::PARAM_STR);
        $stmt->bindParam(":FailureDesc", $failureDesc, PDO::PARAM_STR);
        $stmt->bindParam(":RepairDesc", $repairDesc, PDO::PARAM_STR);
        //Spustí příkaz
        $stmt->execute();



        $pdo = null;
        $stmt = null;

        header("Location: ../inputsites/maintenanceform.php");
        die();
    } catch (PDOException $e) {
        die("Query failed:" . $e->getMessage());
    }


} else {
    header("Location: ../index.php");
    die();
}