<?php
require_once("../includes/dbh.inc.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $table = $_POST['table']; // Určuje, z jaké tabulky se má mazat

    // Ochrana proti SQL injection - povolené tabulky
    $allowedTables = ['History', 'Items', 'Locations'];

    if (!in_array($table, $allowedTables)) {
        die("Neplatná tabulka!");
    }

    try {
        $query = "DELETE FROM $table WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
       header("Location: ../inputsites/historyform.php");
        exit();
    } catch (PDOException $e) {
        die("Chyba při mazání: " . $e->getMessage());
    }
} else {
    die("Neplatná žádost!");
}
?>
