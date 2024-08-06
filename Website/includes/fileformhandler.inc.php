<?php
include ("functions.inc.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $file = $_FILES["file"];
    $path = $file["tmp_name"];
    $type = $file["type"];


    $fcs = 0;
    $lcs = 0;
    $date = "";
    $itemNum = 0;
    $itemYear = 0;
    $index = 0;

    //Kontroluje, zda je soubor typu .txt
    if (($type == "text/plain") && (file_exists($path)) && (is_readable($path))) {
        $obj = fopen($path, "r");
        require_once("dbh.inc.php");
        $pdo->beginTransaction(); //Začne SQL transakci
        try {
            while (!feof($obj)) {
                $line = fgets($obj);
                if (trim($line) != "") { //Kontroluje zda není řádek prázdný
                    $commands = explode("|", $line); //Rozdělí řádek na jednotlivé příkazy
                    if (count($commands) != 5) { //Pokud je v řádku špatný počet příkazů, vyhodí chybu
                        echo "Špatný počet příkazů";
                        echo "<a href=\"../inputsites/fileform.php\"><button class=\"button\">Zpět</button></a>";
                        return;
                    } else {
                        foreach ($commands as $command) { //Podle prvního písmena pozná o jakou informaci se jedná a uloží ji
                            $char = $command[0];
                            $command = substr($command, 1);

                            switch ($char) {
                                case "f":
                                    $fcs = (int)$command;
                                    break;
                                case "l":
                                    $lcs = (int)$command;
                                    break;
                                case "d":
                                    $date = $command;
                                    break;
                                case "n":
                                    $array = explode("/", $command);
                                    $itemNum = (int)$array[0];
                                    $itemYear = (int)$array[1];
                                    break;
                                case "i":
                                    $index = (int)$command;
                                    break;
                                default:
                                    echo "Něco se pokazilo";
                                    echo "<a href=\"../inputsites/fileform.php\"><button class=\"button\">Zpět</button></a>";
                                    return;
                            }
                        }
                        $query = "SELECT id FROM Locations WHERE FCS = :FCS AND LCS = :LCS;"; //Najde ID lokace podle FCS
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":FCS", $fcs, PDO::PARAM_INT);
                        $stmt->bindParam(":LCS", $lcs, PDO::PARAM_INT);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (empty($result)) {
                            throw new Exception("Lokace s daným FCS a LCS neexistuje: $fcs $lcs");
                        }
                        $locationID = $result[0]['id'];

                        $query = "SELECT id FROM Items WHERE INumber = :itemNum AND IYear = :itemYear;"; //Najde ID prvku podle čísla a roku
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":itemNum", $itemNum, PDO::PARAM_INT);
                        $stmt->bindParam(":itemYear", $itemYear, PDO::PARAM_INT);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (empty($result)) {
                            throw new Exception("Lokace s daným číslem a rokem neexistuje: $itemNum/$itemYear");
                        }
                        $itemID = $result[0]['id'];

                        $query = "SELECT * FROM History WHERE ItemID = :ItemID;"; //Kontroluje, zda daný prvek již nemá záznam výskytu
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":ItemID", $itemID, PDO::PARAM_INT);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($result) == 0) { //Pokud ne, vytvoří nový záznam
                            $query = "INSERT INTO History (ItemID, LocationID, FirstSeen, HIndex) VALUES (:ItemID, :LocationID, :FirstSeen, :HIndex);";
                            $stmt = $pdo->prepare($query);
                            $stmt->bindParam(":ItemID", $itemID, PDO::PARAM_INT);
                            $stmt->bindParam(":LocationID", $locationID, PDO::PARAM_INT);
                            $stmt->bindParam(":FirstSeen", $date, PDO::PARAM_STR);
                            $stmt->bindParam(":HIndex", $index, PDO::PARAM_INT);
                            $stmt->execute();
                        } else { //Pokud ano, najde poslední nedokončený záznam
                            $query = "SELECT id, LocationID, HIndex FROM History WHERE ItemID = :ItemID AND FirstSeen <= :logDate AND LastSeen IS NULL;";
                            $stmt = $pdo->prepare($query);
                            $stmt->bindParam(":ItemID", $itemID, PDO::PARAM_INT);
                            $stmt->bindParam(":logDate", $date, PDO::PARAM_STR);
                            $stmt->execute();
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($result)) {
                                $rowID = $result[0]['id'];
                                $locationLog = $result[0]['LocationID'];
                                $indexLog = $result[0]['HIndex'];

                                if ($locationLog != $locationID || $indexLog != $index) { //Pokud je zadaná lokace jíná, než poslední zaznamenaná
                                    $query = "UPDATE History SET LastSeen = :LastSeen WHERE id = :id;"; //Ukončí poslední záznam
                                    $stmt = $pdo->prepare($query);
                                    $stmt->bindParam(":LastSeen", $date, PDO::PARAM_STR);
                                    $stmt->bindParam(":id", $rowID, PDO::PARAM_INT);
                                    $stmt->execute();


                                    $query = "INSERT INTO History (ItemID, LocationID, FirstSeen, HIndex) VALUES (:ItemID, :LocationID, :FirstSeen, :HIndex);"; //A vytvoří nový
                                    $stmt = $pdo->prepare($query);
                                    $stmt->bindParam(":ItemID", $itemID, PDO::PARAM_INT);
                                    $stmt->bindParam(":LocationID", $locationID, PDO::PARAM_INT);
                                    $stmt->bindParam(":FirstSeen", $date, PDO::PARAM_STR);
                                    $stmt->bindParam(":HIndex", $index, PDO::PARAM_INT);
                                    $stmt->execute();
                                }
                            }
                        }
                    }
                }
            }
            $pdo->commit(); 
        } catch (Exception $e) {
            $pdo->rollBack(); 
            die("Error: " . $e->getMessage());
        }
        fclose($obj);
        header("Location: ../index.php");
        die();
    } else {
        echo "Něco se pokazilo";
        echo "<a href=\"../inputsites/fileform.php\"><button class=\"button\">Zpět</button></a>";
    }
} else {
    header("Location: ../index.php");
    die();
}
