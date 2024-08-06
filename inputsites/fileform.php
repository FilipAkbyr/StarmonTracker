<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nahrání ze souboru</title>
    <link rel="stylesheet" href="../styles/formstyle.css">
</head>
<body>
    <h2>Nahrání ze souboru</h2>
    <a href="filesyntax.php"><button>Jak má vypadat .txt soubor?</button></a>
    <h3>Vyberte soubor v .txt formátu</h3> <!--Vyber souboru -->

    <form method="post" enctype="multipart/form-data" action="../includes/fileformhandler.inc.php">
        <input type="file" name="file" accept=".txt"/>
    </form>
    <br>
    <a href="../index.php"><button>Zpět</button></a>



</body>
</html>