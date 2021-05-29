
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="index.css" />
    </head>
    <body>
    <form method="get" action="bordereau.php">
        <div class="row">
            <div class="col">
                <label for="nom">Nom societé:</label>
                <input type="text" name="nom_societe" id="nom" />
            </div>

            <div class="col">
                <label for="banque">Nom de la banque:</label>
                <input type="text" name="banque" id="banque" />

            </div>
            <div class="col">
                <label for="lieu">Lieu de la banque:</label>
                <input type="text" name="lieu" id="lieu" />
            </div>

        </div>
        <div class="row">
            <input type="submit" value="Suivant" class="button" />
        </div>
    </form>
    </body>
</html>