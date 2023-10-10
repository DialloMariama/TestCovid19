<?php
session_start();

date_default_timezone_set('Africa/Dakar');
$date = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $saisie = array(
        "Nom" => $_POST['nom'] ?? "",
        "Prénom" => $_POST['prenom'] ?? "",
        "Température (°C)" => $_POST['temperature'] ?? "",
        "Poids (kg)" => $_POST['poids'] ?? "",
        "Date" => $date,
        "Maux de Tête" => $_POST['maux_tete'] ?? "",
        "Diarrhée" => $_POST['diarrhee'] ?? "",
        "Âge" => $_POST['age'] ?? "",
        "Perte odorat" => $_POST['perte_odorat'] ?? "",
        "Score" => 0,
        "Résultat" => ""
    );

    $_SESSION['historique'][] = $saisie;
}

$saisies_par_jour = array();

if (isset($_POST['supprimer_resultat'])) {
    $resultat_id = $_POST['resultat_id'];

    if (isset($_SESSION['historique'][$resultat_id])) {
        unset($_SESSION['historique'][$resultat_id]);
    }
}

foreach ($_SESSION['historique'] as $resultat_id => $saisie) {
    $date_saisie = date('Y-m-d', strtotime($saisie['Date']));

    if (!isset($saisies_par_jour[$date_saisie])) {
        $saisies_par_jour[$date_saisie] = array();
    }

    $saisies_par_jour[$date_saisie][] = array(
        "resultat_id" => $resultat_id,
        "saisie" => $saisie
    );
}

foreach ($saisies_par_jour as $date_jour => $saisies_jour) {
    echo "<h2>Saisies du $date_jour :</h2>";

    foreach ($saisies_jour as $entry) {
        $resultat_id = $entry["resultat_id"];
        $saisie = $entry["saisie"];

        if (empty($saisie['Nom']) && empty($saisie['Prénom'])) {
            // Ignorer les entrées vides
            continue;
        }

        echo "<p>Résultats de {$saisie['Nom']} {$saisie['Prénom']} :</p>";
        echo "<ul>";

        foreach ($saisie as $label => $valeur) {
            if ($label === "Date") continue;
            echo "<li>$label : $valeur</li>";
        }

        echo "</ul>";

        echo "<form method='post'>";
        echo "<input type='hidden' name='resultat_id' value='$resultat_id'>";
        echo "<input type='submit' name='supprimer_resultat' value='Supprimer'>";
        echo "</form>";
    }
}
?>
<button onclick="window.location.href = 'http://localhost/covid19/';">Retour au Formulaire</button>
