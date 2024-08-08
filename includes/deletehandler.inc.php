<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    if (empty($id) || !is_numeric($id)) {
        die("Invalid ID.");
    }

//    try {
//        require_once("dbh.inc.php");
//        $queryLokace = "DELETE FROM Locations WHERE id = :id";
//        $stmtLokace = $pdo->prepare($queryLokace);
//        $stmtLokace->bindParam(':id', $id, PDO::PARAM_INT);
//    } catch (PDOException $e) {
//        die("Query failed: " . $e->getMessage());
//    }
    try {
        require_once("dbh.inc.php");

        // Debugging: Check if the ID is correctly received
        echo "ID received: " . htmlspecialchars($id) . "<br>";

        // Delete from Locations
        $queryLokace = "DELETE FROM Locations WHERE id = :id";
        $stmtLokace = $pdo->prepare($queryLokace);
        $stmtLokace->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the statement for Locations
        if ($stmtLokace->execute()) {
            // Check if any row was affected in Locations
            if ($stmtLokace->rowCount() > 0) {
                // Redirect back to the appropriate page or show success message
                header("Location: ../inputsites/historyform.php");
                exit();
            } else {
                echo "No record found with ID: " . htmlspecialchars($id) . " in Locations.<br>";
            }
        } else {
            echo "Failed to delete record from Locations.<br>";
        }
    } catch (PDOException $e) {
        // Debugging: Output the error message
        die("Query failed: " . $e->getMessage());
    }

    try {
        require_once("dbh.inc.php");

        // Debugging: Check if the ID is correctly received
        echo "ID received: " . htmlspecialchars($id) . "<br>";

        // Delete from Prvky
        $queryPrvky = "DELETE FROM Items WHERE id = :id";
        $stmtPrvky = $pdo->prepare($queryPrvky);
        $stmtPrvky->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the statement for Prvky
        if ($stmtPrvky->execute()) {
            // Check if any row was affected in Prvky
            if ($stmtPrvky->rowCount() > 0) {
                // Delete from History
                $queryHistory = "DELETE FROM History WHERE id = :id";
                $stmtHistory = $pdo->prepare($queryHistory);
                $stmtHistory->bindParam(':id', $id, PDO::PARAM_INT);

                // Execute the statement for History
                if ($stmtHistory->execute()) {
                    // Redirect back to the history form
                    header("Location: ../inputsites/historyform.php");
                    exit();
                } else {
                    echo "Failed to delete record from History.<br>";
                }
            } else {
                echo "No record found with ID: " . htmlspecialchars($id) . " in Prvky.<br>";
            }
        } else {
            echo "Failed to delete record from Prvky.<br>";
        }
    } catch (PDOException $e) {
        // Debugging: Output the error message
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>