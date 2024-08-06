<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errmsg = "Nastala chyba v zadávání: ";
    $throwerr = false; //Určuje zda má být zadání informací provedeno znovu

    //Vezme data z formulare, pokud data nesplňují omezení, vyhodí chybu, a vratí zpět na formulář
    $locationName = $_POST["locationName"];
    if ($locationName == "") {
        $errmsg .= "Jméno, ";
        $throwerr = true;
    }
    $lcs = $_POST["LCS"];
    if (!is_numeric($lcs) || $lcs < 1 || $lcs > 65535) {
        $errmsg .= "LCS, ";
        $throwerr = true;
    }
    $fcs = $_POST["FCS"];
    if (!is_numeric($fcs) || $fcs < 1) {
        $errmsg .= "FCS, ";
        $throwerr = true;
    }
    $locationPosi = $_POST["locationPosi"];
    if (!is_numeric($locationPosi) || $locationPosi < 1 || $locationPosi > 65535) {
        $errmsg .= "Pozice, ";
        $throwerr = true;
    }
    $locationClass = $_POST["locationClass"];
    if (!is_numeric($locationClass) || $locationClass < 1 || $locationClass > 65535) {
        $errmsg .= "Typ, ";
        $throwerr = true;
    }
    $locationDesc = $_POST["locationDesc"];

    if ($throwerr) { //Pokud data nesplnuji omezeni, vyhodi chybu, a vrati zpet
        echo $errmsg;
        echo "<br>";
        echo "<a href=\"../inputsites/locationform.php\"><button>Zpět</button></a>";
        die();
    }

    $typeL = 0;

    try {
        require_once ("dbh.inc.php");

        //Kontroluje, jestli je zadané jméno již ve slovníku
        $query = "SELECT * FROM LocationDictionary WHERE LName = :locationName;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":locationName", $locationName, PDO::PARAM_STR);
        $stmt->execute(); //Spustí příkaz
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) == 0) {

            //Pokud není, vloží jmeno lokace do tabulky LocationDictionary
            $query = "INSERT INTO LocationDictionary (LName) VALUES (:locationName);";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":locationName", $locationName, PDO::PARAM_STR);
            $stmt->execute(); //Spustí příkaz
        }

        //Vezme z tabulky LocationDictionary hodnotu TypeL odpovídající jménu lokace a uloží ji do $TypeL
        $query = "SELECT * FROM LocationDictionary WHERE LName = :locationName;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":locationName", $locationName, PDO::PARAM_STR);
        $stmt->execute(); //Spustí příkaz
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $typeL = $result[0]['TypeL'];

        //Vloží informace do tabulky Locations, s TypeL odkazujícím na jméno v LocationDictionary
        $query = "INSERT INTO Locations (TypeL, LCS, FCS, LPosition, LClass, LDescription) VALUES (:TypeL, :LCS, :FCS, :LPosition, :LType, :LDescription);";
        $stmt = $pdo->prepare($query);
        //Dosadí proměnné
        $stmt->bindParam(":TypeL", $typeL, PDO::PARAM_INT);
        $stmt->bindParam(":LCS", $lcs, PDO::PARAM_INT);
        $stmt->bindParam(":FCS", $fcs, PDO::PARAM_INT);
        $stmt->bindParam(":LPosition", $locationPosi, PDO::PARAM_INT);
        $stmt->bindParam(":LType", $locationClass, PDO::PARAM_INT);
        $stmt->bindParam(":LDescription", $locationDesc, PDO::PARAM_STR);
        $stmt->execute(); //Spustí příkaz



        $pdo = null;
        $stmt = null;

        header("Location: ../inputsites/locationform.php");
        die();
    } catch (PDOException $e) {
        die("Query failed:" . $e->getMessage());
    }


} else {
    header("Location: ../index.php");
    die();
}