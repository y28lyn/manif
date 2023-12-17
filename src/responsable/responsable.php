<?php
include('../../includes/connexion.php');

// Vérifiez si le responsable est connecté et si oui, récupérez son rôle depuis la session
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'responsable') {
    // L'utilisateur est un responsable, continuez
    // Vous pouvez également récupérer d'autres informations du responsable depuis la session si nécessaire
} else {
    // Redirigez le responsable vers la page de connexion s'il n'est pas connecté ou n'est pas un responsable
    header("Location: login.php");
    exit();
}

// Traitement du formulaire pour la gestion du créneau
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creneauId'], $_POST['activiteId'])) {
    // Modification du créneau des activités
    $creneauId = $_POST['creneauId'];
    $activiteId = $_POST['activiteId'];

    // Vous devez ajuster la requête UPDATE en fonction de votre structure de base de données
    $queryUpdateCreneau = "UPDATE avoir SET id_creneau = :creneauId WHERE id_activite = :activiteId";
    $stmtUpdateCreneau = $pdo->prepare($queryUpdateCreneau);
    $stmtUpdateCreneau->bindParam(':creneauId', $creneauId);
    $stmtUpdateCreneau->bindParam(':activiteId', $activiteId);

    if ($stmtUpdateCreneau->execute()) {
        echo "<script>alert('Créneau modifié avec succès pour l\'activité avec l\'ID : {$activiteId}');</script>";
    } else {
        echo "<script>alert('Erreur lors de la modification du créneau.');</script>";
    }
}

// Traitement du formulaire de désinscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activiteId'], $_POST['participantId'])) {
    // Désinscription d'un participant d'une activité
    $activiteId = $_POST['activiteId'];
    $participantId = $_POST['participantId'];

    // Vous devez ajuster la requête DELETE en fonction de votre structure de base de données
    $queryDesinscription = "DELETE FROM participation WHERE id_activite = :activiteId AND num_participant = :participantId";
    $stmtDesinscription = $pdo->prepare($queryDesinscription);
    $stmtDesinscription->bindParam(':activiteId', $activiteId);
    $stmtDesinscription->bindParam(':participantId', $participantId);

    if ($stmtDesinscription->execute()) {
        echo "<script>alert('Désinscription réussie pour l\'activité avec l\'ID : {$activiteId}');</script>";
    } else {
        echo "<script>alert('Erreur lors de la désinscription.');</script>";
    }
}

if (isset($_POST['deconnexion'])) {
    // Détruire la session et rediriger vers la page de connexion
    session_destroy();
    header("Location: ../login/login.php");
    exit();
}

// Requête pour récupérer les activités et leur créneau
$queryActivitesResponsable = "SELECT a.*, c.heure_debut, c.heure_fin
                              FROM activité a
                              LEFT JOIN avoir av ON a.id_activité = av.id_activite
                              LEFT JOIN creneau c ON av.id_creneau = c.id_creneau";
$stmtActivitesResponsable = $pdo->query($queryActivitesResponsable);

// Requête pour récupérer les participants et les activités auxquelles ils sont inscrits
$queryParticipantsResponsable = "SELECT p.*, a.NomAct, a.id_activité
                                 FROM participant p
                                 LEFT JOIN participation part ON p.num_participant = part.num_participant
                                 LEFT JOIN activité a ON part.id_activite = a.id_activité";
$stmtParticipantsResponsable = $pdo->query($queryParticipantsResponsable);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <title>Responsable</title>
</head>
<body class="bg-gray-100">
    <!-- Liste des activités et modification de leur créneau -->
    <div class="container mx-auto px-6 pt-6">
        <h2 class="text-xl font-bold mb-2">Liste des activités :</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php
            while ($activiteResponsable = $stmtActivitesResponsable->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='bg-white border p-4 rounded shadow mb-4'>";
                echo "<div class='flex flex-col gap-2 mb-2'><p>{$activiteResponsable['NomAct']}</p> <p>Heure de début : {$activiteResponsable['heure_debut']} </p> <p>Heure de fin : {$activiteResponsable['heure_fin']}</p></div>";

                // Formulaire pour la modification du créneau
                echo "<form method='post' class='flex flex-row justify-items-end gap-2'><input type='hidden' name='activiteId' value='{$activiteResponsable['id_activité']}'>";
                
                // Vous devez ajuster la requête SELECT pour récupérer les créneaux disponibles
                $queryCreneaux = "SELECT * FROM creneau";
                $stmtCreneaux = $pdo->query($queryCreneaux);

                echo "<select name='creneauId' class='p-2 border rounded-md'>";
                while ($creneau = $stmtCreneaux->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($creneau['id_creneau'] == $activiteResponsable['id_creneau']) ? 'selected' : '';
                    echo "<option value='{$creneau['id_creneau']}' {$selected}>{$creneau['heure_debut']} - {$creneau['heure_fin']}</option>";
                }
                echo "</select>";

                echo "<button type='submit' class='bg-green-500 hover:bg-green-600 text-white p-2 rounded'>Modifier Créneau</button>";
                echo "</form></div>";
            }
            ?>
        </div>
    </div>

    <!-- Liste des participants et désinscription -->
    <div class="container mx-auto px-6">
        <h2 class="text-xl font-bold mb-2">Liste des inscriptions :</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php
            // Un tableau pour stocker les activités de chaque participant
            $participantActivities = array();

            while ($participantResponsable = $stmtParticipantsResponsable->fetch(PDO::FETCH_ASSOC)) {
                $participantKey = $participantResponsable['num_participant'];

                // Si le participant n'est pas encore dans le tableau, l'ajouter
                if (!isset($participantActivities[$participantKey])) {
                    $participantActivities[$participantKey] = array();
                }

                // Ajouter l'activité actuelle au tableau du participant
                $participantActivities[$participantKey][] = $participantResponsable;
            }

            // Parcourir le tableau des participants et afficher leurs activités
            foreach ($participantActivities as $participantKey => $activities) {
                echo "<div class='bg-white border p-4 rounded shadow mb-4'>";
                echo "<p class='mb-2'>{$activities[0]['nom']} {$activities[0]['prenom']} - {$activities[0]['mail']}</p>";

                // Liste des activités auxquelles le participant est inscrit
                echo "<ul>";

                foreach ($activities as $activity) {
                    echo "<li>{$activity['NomAct']} ";
                    echo "<form method='post' class='flex items-center'>";
                    echo "<input type='hidden' name='activiteId' value='{$activity['id_activité']}'>";
                    echo "<input type='hidden' name='participantId' value='{$activity['num_participant']}'>";
                    // Vérification si le participant est inscrit à une activité
                    if ($activity['id_activité'] !== null) {
                        // Bouton "Désinscrire" si le participant est inscrit à une activité
                        echo "<button type='submit' class='bg-red-500 hover:bg-red-600 text-white mb-2 p-2 rounded'>Désinscrire</button></form></li>";
                    } else {
                        // Message si le participant n'est pas inscrit à une activité
                        echo "<p>Le participant n'est inscrit à aucune activité</p></form></li>";
                    }
                }
                echo "</ul></div>";
            }
            ?>
        </div>
    </div>

    <form method="post" class="container mx-auto px-6">
        <button type="submit" name="deconnexion" class='bg-red-500 hover:bg-red-600 text-white p-2 rounded'>Déconnexion</button>
    </form>

</body>
</html>