<?php
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

$sourceConnStr = "firebird:dbname=192.168.30.61:C:\WEB\database\DIAGOC.fdb;host=localhost";
$targetConnStr = "firebird:dbname=192.168.30.110:D:\\Prace\\STARMONTRACKER.fdb;host=localhost";

try {
$sourceDb = new PDO($sourceConnStr, "SYSDBA", "masterkey");
$sourceDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$targetDb = new PDO($targetConnStr, "SYSDBA", "masterkey");
$targetDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Zpracování parametrů
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $filterDate = $_GET['date'];
} else {
    $filterDate = (new DateTime('yesterday'))->format('Y-m-d');
}

$rangeStart = isset($_GET['range_start']) ? $_GET['range_start'] : '';
$rangeEnd = isset($_GET['range_end']) ? $_GET['range_end'] : '';
$showAll = isset($_GET['show_all']) && $_GET['show_all'] == '1';
$showSummary = isset($_GET['show_summary']) && $_GET['show_summary'] == '1';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <title>Přenos dat ze ZAZNAMDAT</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
<div class="container">

    <h2 class="text-primary mb-4">Přenos dat ze ZAZNAMDAT</h2>

    <!-- Formulář pro výběr jednoho dne -->
    <form method="get" class="row g-3 align-items-center">
        <div class="col-auto">
            <label for="date" class="col-form-label">Zvol datum:</label>
        </div>
        <div class="col-auto">
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($filterDate) ?>" class="form-control">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Zobrazit jeden den</button>
        </div>
    </form>

    <!-- Formulář pro výběr rozsahu -->
    <form method="get" class="row g-3 align-items-center mt-3">
        <div class="col-auto">
            <label for="range_start" class="col-form-label">Od:</label>
        </div>
        <div class="col-auto">
            <input type="date" id="range_start" name="range_start" value="<?= htmlspecialchars($rangeStart) ?>" class="form-control">
        </div>
        <div class="col-auto">
            <label for="range_end" class="col-form-label">Do:</label>
        </div>
        <div class="col-auto">
            <input type="date" id="range_end" name="range_end" value="<?= htmlspecialchars($rangeEnd) ?>" class="form-control">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Zobrazit rozsah</button>
        </div>
    </form>

    <!-- Tlačítko pro zobrazení shrnutí -->
    <form method="get" class="mt-3">
        <input type="hidden" name="show_summary" value="1">
        <button type="submit" class="btn btn-warning">Zobrazit počet záznamů dle data</button>
    </form>

    <hr>

    <?php
    if ($showSummary) {
        echo "<h3 class='text-warning'>Počet záznamů podle data</h3>";

        $stmt = $sourceDb->query("SELECT CAST(DATETIME AS DATE) AS day_date, COUNT(*) AS record_count FROM ZAZNAMDAT GROUP BY CAST(DATETIME AS DATE) ORDER BY day_date");
        $recordsByDate = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($recordsByDate) > 0) {
            echo "<table class='table table-striped table-bordered'>";
            echo "<thead class='table-dark'><tr><th>Datum</th><th>Počet záznamů</th><th>Kontrola</th></tr></thead>";
            echo "<tbody>";

            $prevCount = null;
            foreach ($recordsByDate as $row) {
                $day = $row['DAY_DATE'];
                $count = $row['RECORD_COUNT'];
                $warning = '';

                if ($prevCount !== null && $count < $prevCount) {
                    $warning = "<span class='text-danger fw-bold'>⚠️ Počet záznamů je nižší než předchozí den</span>";
                }

                echo "<tr>";
                echo "<td>" . htmlspecialchars($day) . "</td>";
                echo "<td>" . htmlspecialchars($count) . "</td>";
                echo "<td>$warning</td>";
                echo "</tr>";

                $prevCount = $count;
            }

            echo "</tbody></table>";
        } else {
            echo "<p>Žádné záznamy nenalezeny.</p>";
        }

        exit;
    }

    if (!empty($rangeStart) && !empty($rangeEnd)) {
        echo "<h3 class='text-primary'>Zpracování rozsahu: " . htmlspecialchars($rangeStart) . " až " . htmlspecialchars($rangeEnd) . "</h3>";

        $stmt = $sourceDb->prepare("SELECT * FROM ZAZNAMDAT WHERE CAST(DATETIME AS DATE) BETWEEN ? AND ?");
        $stmt->execute([$rangeStart, $rangeEnd]);
        $rangeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h4>Počet záznamů v rozsahu: " . count($rangeRecords) . "</h4>";

        if (count($rangeRecords) > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered table-hover'>";
            echo "<thead class='table-light'><tr>";
            foreach (array_keys($rangeRecords[0]) as $column) {
                echo "<th>" . htmlspecialchars($column) . "</th>";
            }
            echo "</tr></thead><tbody>";

            foreach ($rangeRecords as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "<p>Žádná data v tomto rozsahu.</p>";
        }

    } else {
        echo "<h3 class='text-success'>Zpracování data: " . htmlspecialchars($filterDate) . "</h3>";

        $stmt = $sourceDb->prepare("SELECT * FROM ZAZNAMDAT WHERE CAST(DATETIME AS DATE) = ?");
        $stmt->execute([$filterDate]);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h4>Počet záznamů: " . count($records) . "</h4>";

        if (count($records) > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered table-hover'>";
            echo "<thead class='table-light'><tr>";
            foreach (array_keys($records[0]) as $column) {
                echo "<th>" . htmlspecialchars($column) . "</th>";
            }
            echo "</tr></thead><tbody>";

            foreach ($records as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "<p>Žádné záznamy pro daný den.</p>";
        }
    }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'><strong>Chyba připojení nebo dotazu:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    ?>
</div>
</body>
</html>
