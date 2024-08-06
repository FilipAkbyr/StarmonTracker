<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nahrání ze souboru</title>
</head>
<body>
    <h2>Vyberte soubor v .txt formátu</h2> <!--Vyber souboru -->
    <form method="post" enctype="multipart/form-data" action="../includes/fileformhandler.inc.php">
        <input type="file" name="file" accept=".txt"/>
        <button type="submit" value="submit">Odeslat</button>
    </form>

    <a href="filesyntax.php"><button>Jak má vypadat .txt soubor?</button></a>
    <a href="../index.php"><button>Zpět</button></a>
</body>
</html>