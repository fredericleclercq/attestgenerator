<?php

/* Importateur de fichier pdf */

use setasign\Fpdi\Fpdi;

/* Fonction de conversions des caractères accentués */

function strconv($str)
{
    return iconv('UTF-8', 'windows-1252', $str);
}
/* Fuseau horaire de la France pour la date/heure */
date_default_timezone_set('Europe/Paris');

/* Si le formulaire est posté */
if (!empty($_POST)) {

    /* classes de lecture et ecriture de PDF */
    require_once('fpdf/fpdf.php');
    require_once('fpdfi/autoload.php');

    $empties = 0;
    /* Assainissement des données */
    foreach ($_POST as $key => $value) {
        if ($key != 'raisons') $_POST[$key] = htmlspecialchars($value, ENT_QUOTES);
    }

    if (!empty($_POST['data_signature'])) {

        $data_uri = $_POST['data_signature'];
        $encoded_image = explode(",", $data_uri)[1];
        $decoded_image = base64_decode($encoded_image);
        $id = time();
        /* Génération de l'image de signature */
        file_put_contents("signatures/" . $id . "_signature.png", $decoded_image);

        $pdf = new FPDI();
        $pdf->AddPage();
        /* chargement du document officiel */
        $pdf->setSourceFile('attestation-deplacement-fr.pdf');
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx);

        /* Insertion des champs */
        $pdf->SetFont('Arial', '', '12');
        $pdf->SetXY(42, 49);
        $pdf->Write(10, strconv(mb_strtoupper($_POST['nom']) . ' ' . ucfirst($_POST['prenom'])));
        $pdf->SetXY(42, 57.5);
        $pdf->Write(10, date('d/m/Y', strtotime($_POST['date_naissance'])));
        $pdf->SetXY(32, 65.9);
        $pdf->Write(10, strconv($_POST['lieu_naissance']));
        $pdf->SetXY(46, 74.5);
        $pdf->Write(10, strconv($_POST['adresse'] . ' ' . $_POST['code_postal'] . ' ' . $_POST['ville']));
        $pdf->SetFont('Arial', '', '10');
        $pdf->SetXY(38, 211.3);
        $pdf->Write(10, strconv($_POST['ville']));
        $pdf->SetXY(35, 219.8);
        $pdf->Write(10, date('d/m/Y'));
        $pdf->SetXY(69, 219.8);
        $pdf->Write(10, date('H'));
        $pdf->SetXY(77, 219.8);
        $pdf->Write(10, date('i'));        
        $pdf->Image("signatures/" . $id . "_signature.png", 45, 232, -300);        

        /* Placement des coches dans les cases */
        $pdf->SetFont('ZapfDingbats', '', '18');
        if (in_array('r1', $_POST['raisons'])) {
            $pdf->SetXY(25, 104.5);
            $pdf->Write(10, '4');
        }

        if (in_array('r2', $_POST['raisons'])) {
            $pdf->SetXY(25, 122);
            $pdf->Write(10, '4');
        }

        if (in_array('r3', $_POST['raisons'])) {
            $pdf->SetXY(25, 136.5);
            $pdf->Write(10, '4');
        }

        if (in_array('r4', $_POST['raisons'])) {
            $pdf->SetXY(25, 149.3);
            $pdf->Write(10, '4');
        }

        if (in_array('r5', $_POST['raisons'])) {
            $pdf->SetXY(25, 169);
            $pdf->Write(10, '4');
        }

        if (in_array('r6', $_POST['raisons'])) {
            $pdf->SetXY(25, 185.3);
            $pdf->Write(10, '4');
        }

        if (in_array('r7', $_POST['raisons'])) {
            $pdf->SetXY(25, 198);
            $pdf->Write(10, '4');
        }
        /* Destruction de la signature */
        unlink("signatures/" . $id . "_signature.png");
        /* Affichage du PDF en sortie */
        $pdf->Output('I', 'ADDD_' . (date('Y-m-d')) . '.pdf');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Générateur d'attestation de déplacement dérogatoire</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="bg-dark  py-4 text-light text-center">
        <h1>Générateur d'attestation de déplacement dérogatoire</h1>
    </header>

    <main class="container">

        <div class="row">
            <div class="col py-4">
                <div class="alert alert-success text-justify">
                    NOUVELLE VERSION DU 23 MARS 2020</a>
                </div>

                <div class="alert alert-info text-justify">
                    Durant la période de confinement due au COVID-19, ce générateur a été créé dans le but de faciliter la rédaction et
                    l'impression de l'attestation quotidienne pour vos déplacements en respectant <a href="https://www.interieur.gouv.fr/Actualites/L-actu-du-Ministere/Attestation-de-deplacement-derogatoire-et-justificatif-de-deplacement-professionnel" target="_blank">les consignes du gouvernement</a><br>
                    <em>Ce site ne contient AUCUN cookie, et aucune information n'est enregistrée ou stockée sur le serveur. L'image de votre signature est détruite après génération du fichier PDF. (code source consultable sur <a href="https://github.com/fredericleclercq/attestgenerator">Github</a>)</em>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col py-4">
                <h3>Merci de remplir les champs obligatoires :</h3>
                <form action="" method="post" id="formulaire" target="_blank">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="nom">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="prénom">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="date_naissance">Date de naissance</label>
                            <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="lieu_naissance">Lieu de naissance</label>
                            <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="code_postal">Code postal</label>
                            <input type="text" class="form-control" id="code_postal" name="code_postal" required title="5 chiffres" pattern="[0-9]{5}" maxlength="5">
                        </div>
                        <div class="form-group col-md-9">
                            <label for="ville">Ville</label>
                            <input type="text" class="form-control" id="ville" name="ville">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="raisons">Selectionnez la ou les raison(s) des déplacements de la journée</label>
                        <div>
                            <input type="checkbox" id="r1" name="raisons[]" value="r1">
                            Déplacements entre le domicile et le lieu d’exercice de l’activité professionnelle, lorsqu’ils sont indispensables à l’exercice d’activités ne pouvant être organisées sous forme de télétravail ou déplacements professionnels ne pouvant être différés.
                        </div>
                        <div>
                            <input type="checkbox" id="r2" name="raisons[]" value="r2">
                            Déplacements pour effectuer des achats de fournitures nécessaires à l’activité
                            professionnelle et des achats de première nécessité dans des établissements dont les
                            activités demeurent autorisées(liste sur <a href="https://www.gouvernement.fr/" target="_blank">gouvernement.fr</a>)
                        </div>
                        <div>
                            <input type="checkbox" id="r3" name="raisons[]" value="r3">
                            Consultations et soins ne pouvant être assurés à distance et ne pouvant être différés , consultations et soins des patients atteints d'une affection de longue durée.
                        </div>
                        <div>
                            <input type="checkbox" id="r4" name="raisons[]" value="r4">
                            Déplacements pour motif familial impérieux, pour l’assistance auxpersonnes vulnérables ou la garde d’enfants
                        </div>
                        <div>
                            <input type="checkbox" id="r5" name="raisons[]" value="r5">
                            Déplacements brefs, dans la limite d'une heure quotidienne et dans un rayon maximal
                            d'un kilomètre autour du domicile, liés soit à l'activité physique individuelle des
                            personnes, à l'exclusion de toute pratique sportive collective et de toute proximité avec
                            d'autres personnes, soit à la promenade avec les seules personnes regroupées dans un
                            même domicile, soit aux besoins des animaux de compagnie.
                        </div>
                        <div>
                            <input type="checkbox" id="r6" name="raisons[]" value="r6">
                            Convocation judiciaire ou administrative
                        </div>
                        <div>
                            <input type="checkbox" id="r7" name="raisons[]" value="r7">
                            Participation à des missions d’intérêt général sur demande de l’autorité administrative.
                        </div>


                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-12 col-md-6">
                            <label>Dessiner votre signature</label>
                            <canvas id="canvas_signature" name="canvas_signature" width="360" height="240" class="d-block border"></canvas>
                            <textarea class="d-none" id="data_signature" name="data_signature"></textarea>
                            <span class="d-block my-2" id="error_sign"></span>
                            <a href="#" id="reset_signature">Effacer la signature</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <input type="submit" value="Générer une attestation pour aujourd'hui et à cette heure" class="btn btn-primary">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </main>


    <footer class="bg-dark py-4  text-light text-center"><?= date('Y') ?> - GADDD - F.LECLERCQ - Ce site n'a aucune
        affiliation avec <a href="https://www.gouvernement.fr/" target="_blank">https://www.gouvernement.fr/</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="functions.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>

</body>

</html>