<?php
require_once ("dbh.inc.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deletePrvky'])) {
        $id = $_POST['deletePrvky'];

        try {
            // Delete from History first
            $query = "DELETE FROM History WHERE ItemID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Then delete from Prvky
            $query = "DELETE FROM Items WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: ../inputsites/historyform.php");
            exit();
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    } elseif (isset($_POST['deleteLokace'])) {
        $id = $_POST['deleteLokace'];
        echo "ID to delete: $id<br>"; // Debugging statement to check ID

        try {
            // Only delete from Lokace
            $query = "DELETE FROM Locations WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo "Deleted successfully"; // Debugging statement to confirm deletion
            header("Location: ../inputsites/historyform.php");
            exit();
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>