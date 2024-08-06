<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syntaxe souboru</title>
</head>

<body>
        <h2>Jak vytvořit soubor pro načtení výskytu prvku:</h2> 
        Každý řádek reprezentuje jeden zápis, zápisy MUSÍ být na samostatných řádcích <br>
        Jednotlivé informace jsou rozděleny pomocí "|", na pořadí informací nezáleží <br>
        Každá informace má jeden počáteční znak určující o jakou informaci se jedná: <br>
        <br>
        f: FCS stanice do které chceme přidat prvek <br>
        l: LCS lokace do které chceme přidat prvek
        d: Datum výskytu prvku, ve formátu YYYY-MM-DD <br>
        n: Číslo a rok prvku ve formátu 00/0000 <br>
        i: Specifická pozice prvku v lokaci <br>
        <br>
        Příklad zápisu:<br>
        f2|i1|d2006-01-01|n1/2000|i2<br>
        Tento zápis vloží prvek s číslem 1/2000, na stanici s FCS 2 na pozici 2, a datem zápisu 2006-01-01<br>
        <br>
        Zápisy různých prvků a lokací lze kombinovat<br>
        Například:<br>
        f2|i1|d2006-06-07|n1/2000|i2<br>
        f2|i1|d2007-01-05|n1/2013|i1<br>
        f1|i1|d2008-04-01|n4/2002|i2<br>
        f3|i1|d2005-02-01|n1/2000|i4<br>
        <br><br>
    <a href="fileform.php"><button>Zpět</button></a>
</body>

</html>