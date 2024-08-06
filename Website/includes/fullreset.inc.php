<?php
try { //Postupne vycisti vsechny tabulky
    require_once ("dbh.inc.php");
    $query = "DELETE FROM History;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "DELETE FROM MaintenanceLog;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "DELETE FROM Items;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "DELETE FROM Locations;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "DELETE FROM ItemDictionary;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "DELETE FROM LocationDictionary;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "ALTER TABLE History AUTO_INCREMENT = 1;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "ALTER TABLE MaintenanceLog AUTO_INCREMENT = 1;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "ALTER TABLE Items AUTO_INCREMENT = 1;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $query = "ALTER TABLE Locations AUTO_INCREMENT = 1;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "ALTER TABLE ItemDictionary AUTO_INCREMENT = 1;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $query = "ALTER TABLE LocationDictionary AUTO_INCREMENT = 1;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();


    header("Location: ../index.php");
    die();

} catch (PDOException $e) {
    die("Query failed:" . $e->getMessage());
}