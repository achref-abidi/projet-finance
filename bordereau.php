<?php session_start() ?>
<!DOCTYPE html>

<html>

<head>
    <link rel="stylesheet" href="index.css" />
</head>

<body>
    <?php
    $msgError = '<p class="error"> Erreur </p>';
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $_SESSION['nom_societe'] = empty($_GET['nom_societe']) ? die($msgError) : $_GET['nom_societe'];
        $_SESSION['banque'] = empty($_GET['banque']) ? die($msgError) : $_GET['banque'];
        $_SESSION['lieu'] = empty($_GET['lieu']) ? die($msgError) : $_GET['lieu'];
        $_SESSION['date'] = date('d/m/Y');
    }
    ?>


    <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>">

        <div class="row params">
            Prams:
            <div class="col">
                <label>Taux d’escompte:</label>
                <input type="text" name="taux_escompte" value="<?php if (!empty($_SESSION['params']['taux_escompte'])) echo $_SESSION['params']['taux_escompte'] ?>" />
            </div>

            <div class="col">
                <label>Jour de banque</label>
                <input type="text" name="jours_banque"  value="<?php if (!empty($_SESSION['params']['jours_banque'])) echo $_SESSION['params']['jours_banque'] ?>" />
            </div>

            <div class="col">
                <label>commission fixe effet placé domicilié </label>
                <input type="text" name="c1"  value="<?php if (!empty($_SESSION['params']['c1'])) echo $_SESSION['params']['c1'] ?>"/>
            </div>

            <div class="col">
                <label>commission fixe effet placé non domicilié </label>
                <input type="text" name="c2"  value="<?php if (!empty($_SESSION['params']['c1'])) echo $_SESSION['params']['c1'] ?>" />
            </div>

            <div class="col">
                <label>commission fixe effet déplacé domicilié </label>
                <input type="text" name="c3"  value="<?php if (!empty($_SESSION['params']['c3'])) echo $_SESSION['params']['c3'] ?>"/>
            </div>

            <div class="col">
                <label>commission fixe effet déplacé non domicilié </label>
                <input type="text" name="c4" value="<?php if (!empty($_SESSION['params']['c4'])) echo $_SESSION['params']['c4'] ?>" />
            </div>

        </div>



        <div class="row">
            <div class="col">
                <label for="valeur">Valeur Nominale:</label>
                <input type="text" name="valeur_nominale" id="valeur" />
            </div>

            <div class="col">
                <label for="lieu">Lieu de paiement:</label>
                <input type="text" name="lieu" id="lieu" />
            </div>
            <div class="col">
                <label for="echeance">Echéance(j/m/a):</label>
                <input type="text" name="echeance" id="echeance" />

            </div>
            <div class="col">
                <label for="banque">Banque domiciliatrice:</label>
                <input type="text" name="banque" id="banque" />

            </div>
        </div>
        <div class="row">
            <input type="submit" name="submit" value="Calculer" class="button" />
            <input type="submit" name="reset" value="Reset" class="button" />
        </div>
    </form>


    <?php
    $msgWarn = '<p class="warn"> Aucune données trouvées </p>';

    $bordereau = null;
    $total = null;

    /*$tabOperation = array(
        array(
            'lieu' => 'Tunis',
            'valeur nominale' => 810,
            'echeance' => '14/05/2006',
            'banque' => 'B.I.A.T'
        )
    );*/

    if (!empty($_POST['reset'])) {
        $_SESSION['operations'] = null;
        die($msgWarn);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!isset($_SESSION['params']))
            $_SESSION['params'] = array(
            'taux_escompte' => empty($_POST['taux_escompte']) ? die($msgError) : $_POST['taux_escompte'],
            'jours_banque' => empty($_POST['jours_banque']) ? die($msgError) : $_POST['jours_banque'],
            'c1' => empty($_POST['c1']) ? die($msgError) : $_POST['c1'],
            'c2' => empty($_POST['c2']) ? die($msgError) : $_POST['c2'],
            'c3' => empty($_POST['c3']) ? die($msgError) : $_POST['c3'],
            'c4' => empty($_POST['c4']) ? die($msgError) : $_POST['c4'],
        );

        if (!isset($_SESSION['operations']))
            $_SESSION['operations'] = array();

        array_push($_SESSION['operations'], array(
            'lieu' => empty($_POST['lieu']) ? die($msgError) : $_POST['lieu'],
            'valeur nominale' =>  empty($_POST['valeur_nominale']) ? die($msgError) : $_POST['valeur_nominale'],
            'echeance' =>  empty($_POST['echeance']) ? die($msgError) : $_POST['echeance'],
            'banque' =>  empty($_POST['banque']) ? die($msgError) : $_POST['banque']
        ));

        $tabOperation = $_SESSION['operations'];

        require_once('./operation.php');
        $params = array(
            'banque_origine' => $_SESSION['banque'],
            'lieu_origine' => $_SESSION['lieu'],
            'coms' => array($_SESSION['params']['c1'], $_SESSION['params']['c2'], $_SESSION['params']['c3'], $_SESSION['params']['c4']),
            'taux_escompte' => $_SESSION['params']['taux_escompte'],
            'jours_banque' => $_SESSION['params']['jours_banque']
        );

        mkBordereau($tabOperation, $bordereau, $total, $params);
        //calculMontant($bordereau, $total);
    }



    if (empty($bordereau)) die($msgWarn);
    //else afficher le bordereau

    ?>


    <table>
        <div class="info">
            <span><strong>Banque: <?php echo $_SESSION['banque'] . " " .  $_SESSION['lieu'] ?></strong></span>
            <br />
            <span><strong>Société: <?php echo $_SESSION['nom_societe'] ?></strong></span>
            <span style="float:right"><strong><?php echo $_SESSION['lieu'] . " le " . $_SESSION['date'] ?></strong></span>
        </div>
        <th>N° de l'effet</th>
        <th>Lieu de paiement</th>
        <th>Valeur nominale</th>
        <th>Echéance</th>
        <th>Jours d'agios</th>
        <th>Escompte</th>
        <th>Commission fixe</th>
        <th>TVA/COM</th>


        <?php

        for ($i = 0; $i < count($bordereau); $i++) {
            echo "<tr>";
            echo "<td>" . $i . "</td>";
            echo "<td>" . $bordereau[$i]['lieu'] . '-' . $bordereau[$i]['banque'] . "</td>";
            echo "<td>" . $bordereau[$i]['valeur nominale'] . "</td>";
            echo "<td>" . $bordereau[$i]['echeance'] . "</td>";
            echo "<td>" . $bordereau[$i]['jours agios'] . "</td>";
            echo "<td>" . $bordereau[$i]['escompte'] . "</td>";
            echo "<td>" . $bordereau[$i]['com fixe'] . "</td>";
            echo "<td>" . $bordereau[$i]['tva/com'] . "</td>";

            echo "</tr>";
        }

        echo "<tr> </tr>";

        echo "<tr>";
        echo '<td><strong>Total</strong></td>';
        echo '<td></td>';
        echo '<td>' . $total['total']['valeur nominale'] . '</td>';

        echo '<td></td>';
        echo '<td></td>';
        echo '<td>' . $total['total']['escompte'] . '</td>';
        echo '<td>' . $total['total']['com'] . '</td>';
        echo '<td>' . $total['total']['tva/com'] . '</td>';
        echo "</tr>";

        echo "<tr>";
        echo '<td><strong>Agios</strong></td>';
        echo '<td></td>';
        echo '<td>' . $total['agios'] . '</td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo "</tr>";

        echo "<tr>";
        echo '<td><strong>Montant net</strong></td>';
        echo '<td></td>';
        echo '<td><strong>' . $total['montant net'] . '</strong></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo "</tr>";

        ?>
    </table>





</body>

</html>