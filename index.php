<?php
session_start();

if (!isset($_SESSION['historique'])) {
    $_SESSION['historique'] = array();
}

date_default_timezone_set('Africa/Dakar');

$nom = "";
$prenom = "";
$temperature = "";
$poids = "";
$maux_tete = "";
$diarrhee = "";
$perte_odorat = "";
$date = date("Y-m-d H:i:s"); 
$score = 0;
$resultats_utilisateur = array();
$erreurs = array();

function validerNomPrenom($nom, $prenom) {
    if ((strlen($nom) >= 2 && strlen($nom) <= 40) && (strlen($prenom) >= 3 && strlen($prenom) <= 50)) {
        return preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/", $nom) && preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+$/", $prenom);
    } else {
        return false;
    }
}



function validerTemperaturePoids($temperature, $poids) {
    return $temperature >= 30 && $temperature <= 45 && $poids >= 2 && $poids <= 300;
}

function validerChampsRadio($maux_tete, $diarrhee, $perte_odorat) {
    return !empty($maux_tete) && !empty($diarrhee) && !empty($perte_odorat);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = isset($_POST['nom']) ? $_POST['nom'] : "";
    $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : "";
    $temperature = isset($_POST['temperature']) ? $_POST['temperature'] : "";
    $poids = isset($_POST['poids']) ? $_POST['poids'] : "";
    $maux_tete = isset($_POST['maux_tete']) ? $_POST['maux_tete'] : "";
    $diarrhee = isset($_POST['diarrhee']) ? $_POST['diarrhee'] : "";
    $age = isset($_POST['age']) ? $_POST['age'] : "";
    $perte_odorat = isset($_POST['perte_odorat']) ? $_POST['perte_odorat'] : "";

    if (!validerNomPrenom($nom, $prenom)) {
        array_push($erreurs, "Entrez un nom et un prénom valides.");
    }

    if (!validerTemperaturePoids($temperature, $poids)) {
        array_push($erreurs, "Entrez des valeurs valides pour la température et le poids.");
    }

    if (!validerChampsRadio($maux_tete, $diarrhee, $perte_odorat)) {
        array_push($erreurs, "Veuillez répondre à toutes les questions.");
    } else {
        switch ($maux_tete) {
            case 'oui':
                $score += 15;
                break;
            case 'non':
                $score += 0;
                break;
            default:
                break;
        }

        $score += ($diarrhee === 'oui') ? 15 : 0;

        switch ($age) {
            case '0-14':
                $score += 20;
                break;
            case '15-40':
                $score += 0;
                break;
            default:
                $score += 30;
        }

        if ($temperature > 38 || $temperature < 36) {
            $score += 20;
        }

        $score += ($perte_odorat === 'oui') ? 20 : 0;

        switch (true) {
            case ($score <= 49):
                $resultat = "Vous ne risquez rien";
                break;
            case ($score <= 79):
                $resultat = "Vous êtes susceptibles d'être malade";
                break;
            default:
                $resultat = "Vous êtes malade";
                break;
        }

        if (empty($erreurs)) {
            $saisie = array(
                "Nom" => $nom,
                "Prénom" => $prenom,
                "Température (°C)" => $temperature,
                "Poids (kg)" => $poids,
                "Date" => $date, 
                "Maux de Tête" => $maux_tete,
                "Diarrhée" => $diarrhee,
                "Âge" => $age,
                "Perte odorat" => $perte_odorat,
                "Score" => $score,
                "Résultat" => $resultat 
            );

            $_SESSION['historique'][] = $saisie;
            $resultats_utilisateur = $saisie;
        }
    }
}

if (!empty($erreurs)) {
    echo "<h2>Erreurs :</h2>";
    echo "<ul>";
    foreach ($erreurs as $erreur) {
        echo "<li style='color: red;'>$erreur</li>";
    }
    echo "</ul>";
}

if (!empty($resultats_utilisateur)) {
    echo "<h2>Résultats de $saisie[Nom] $saisie[Prénom] :</h2>";
    echo "<ul>";
    foreach ($resultats_utilisateur as $label => $valeur) {
        echo "<li>$label : $valeur</li>";
    }
    echo "</ul>";
}

echo '<input type="submit" onclick="window.location.href = \'soumettre.php\';" value="Voir l\'historique complet">';
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Formulaire d'Évaluation de la Covid19</title>
</head>
<body>
    <form action="index.php" method="post">
        <h1>Test Covid19</h1>
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" required pattern="[A-Za-z]+" title="Entrez un nom valide (lettres uniquement)" value="<?php echo $nom; ?> ">
        
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" required pattern="[A-Za-z]+" title="Entrez un prenom valide (lettres uniquement)" >
        
        <label for="temperature">Température (°C)</label>
        <input type="number" id="temperature" name="temperature" step="0.01" min="30" max="45" required>
 
        <label for="poids">Poids (kg)</label>
        <input type="number" id="poids" name="poids" step="0.01" min="2" max="300" required>
        
        <label for="maux_tete">Avez-vous des maux de tête ?</label>
        <input type="radio" id="maux_tete_oui" name="maux_tete" value="oui" required>
        <label for="maux_tete_oui">Oui</label>
        <input type="radio" id="maux_tete_non" name="maux_tete" value="non" required>
        <label for="maux_tete_non">Non</label>
        
        <label for="diarrhee">Avez-vous la diarrhée</label>
        <input type="radio" id="diarrhee_oui" name="diarrhee" value="oui" required>
        <label for="diarrhee_oui">Oui</label>
        <input type="radio" id="diarrhee_non" name="diarrhee" value="non" required>
        <label for="diarrhee_non">Non</label>

        <label for="perte_odorat">Avez-vous une perte d'odeur</label>
        <input type="radio" id="perte_odorat_oui" name="perte_odorat" value="oui" required>
        <label for="perte_odorat_oui">Oui</label>
        <input type="radio" id="perte_odorat_non" name="perte_odorat" value="non" required>
        <label for="perte_odorat_non">Non</label>
    
        <label for="age">Âge</label>
        <input type="radio" id="age_0_14" name="age" value="0-14" required>
        <label for="age_0_14">0-14 ans</label>
        <input type="radio" id="age_15_40" name="age" value="15-40" required>
        <label for="age_15_40">15-40 ans</label>
        <input type="radio" id="age_41_100" name="age" value="41-100" required>
        <label for="age_41_100">41-100 ans</label>
    
        <input type="submit" value="Soumettre">
       
    </form>

</body>
</html>
