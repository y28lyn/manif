<?php
include('../../includes/connexion.php');

// Vérifiez si le responsable est connecté et si oui, récupérez son rôle depuis la session
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
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
    <link rel="stylesheet" href="../../public/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Administation</title>
</head>
<body>
    <header>      
        <div class="bg-cover bg-center min-h-screen flex items-center overflow-hidden relative">
            <div class="absolute inset-0">
                <img
                loading="lazy"
                class="w-full h-full object-cover object-center"
                src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt=""
                role="presentation"
                aria-hidden="true"
                />
            </div>
            <div class="absolute inset-0 bg-black/80 block from-black to-transparent"></div>
            <div class="relative text-white container flex flex-col justify-center p-6 mx-auto sm:py-12 lg:py-24 lg:flex-row">
                <div class="flex items-center justify-center lg:mt-0 h-72 sm:h-80 lg:h-96 xl:h-112 2xl:h-128">
                    <img src="https://doodleipsum.com/800x600/flat?sat=-100&i=043f407e725ebf28cb451445da864159" alt="" class="object-contain h-72 sm:h-80 lg:h-96 xl:h-112 2xl:h-128">
                </div>
                <div class="flex flex-col justify-center p-6 text-center rounded-sm lg:max-w-md xl:max-w-lg lg:text-left">
                    <h1 class="text-4xl md:text-5xl font-bold leadi sm:text-6xl">Panneau de contrôle complet de l'administration.</h1>
                    <p class="mt-6 mb-8 text-lg sm:mb-12">Soyez le maître d'orchestre de l'action en gérant avec assurance les participants et les activités.</p>
                    <div class="flex flex-col md:flex-row gap-5 w-full">
                        <button 
                            onclick="scrollToNextSection()"
                            class="w-full md:w-1/2 px-8 py-3 text-lg font-semibold rounded bg-[#393646] text-gray-50 transition ease-in-out delay-150 md:hover:-translate-y-1 md:hover:scale-105 duration-300"
                        >
                            <span>Lire plus</span>
                        </button>
                        <form method="post" class="md:w-1/2">
                            <button type="submit" name="deconnexion" class="w-full px-8 py-3 text-lg font-semibold rounded border border-slate-200 transition ease-in-out delay-150 md:hover:-translate-y-1 md:hover:scale-105 duration-300">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main id="main" class="p-6 py-12 bg-[#000500] text-white">
        <!-- Liste des activités et modification de leur créneau -->
        <div class="container mx-auto px-6 pt-6">
            <h1 class="mb-4 text-3xl font-extrabold text-white md:text-5xl lg:text-6xl"><span class="text-transparent bg-clip-text bg-[#6D5D6E]">Les activités</span> disponibles</h1>
            <p class="text-lg font-normal lg:text-xl text-gray-400">Parcourez notre vaste éventail d'activités captivantes, soigneusement sélectionnées pour offrir une diversité d'expériences.</p>
            <ul class="flex flex-col md:flex-row gap-6 mt-4">
                <?php
                // Traitement du formulaire pour modifier une activité
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activityId'], $_POST['newActivityName'])) {
                    $activityId = $_POST['activityId'];
                    $newActivityName = $_POST['newActivityName'];
                    $queryUpdateActivity = "UPDATE activité SET NomAct = :newActivityName WHERE id_activité = :activityId";
                    $stmtUpdateActivity = $pdo->prepare($queryUpdateActivity);
                    $stmtUpdateActivity->bindParam(':newActivityName', $newActivityName);
                    $stmtUpdateActivity->bindParam(':activityId', $activityId);
                    if ($stmtUpdateActivity->execute()) {
                        echo "<script>alert('Activité modifiée avec succès : {$newActivityName}');</script>";
                    } else {
                        echo "<script>alert('Erreur lors de la modification de l\'activité.');</script>";
                    }
                }

                while ($activiteResponsable = $stmtActivitesResponsable->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li class='md:w-1/3 w-[95%] mx-auto'> <div class='bg-[#6D5D6E] shadow-lg rounded-lg p-5 overflow-hidden'>";
                    echo "<div class='relative md:h-[6em] pb-2 md:pb-14'>
                            <div class='text-xs font-bold uppercase text-[#F4EEE0] tracking-widest mb-2'>{$activiteResponsable['NomAct']}</div>
                            <div class='h-[1px] w-[98%] bg-white my-3'></div>
                            <p class='text-md md:text-xl font-extrabold text-white leading-snug mb-2'>Heure de début : {$activiteResponsable['heure_debut']}</p>
                            <p class='text-md md:text-xl font-extrabold text-white leading-snug mb-2'>Heure de fin : {$activiteResponsable['heure_fin']}</p>
                          </div>";      
                    // Formulaire pour la modification du créneau
                    echo "<form method='post' class='flex flex-col md:flex-row justify-items-end gap-2 mt-0 md:mt-6'><input type='hidden' name='activiteId' value='{$activiteResponsable['id_activité']}'>";
                    
                    // Vous devez ajuster la requête SELECT pour récupérer les créneaux disponibles
                    $queryCreneaux = "SELECT * FROM creneau";
                    $stmtCreneaux = $pdo->query($queryCreneaux);
    
                    echo "<select name='creneauId' class='w-48 p-2 border border-[#F4EEE0] rounded-md bg-[#6D5D6E]'>";
                    while ($creneau = $stmtCreneaux->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($creneau['id_creneau'] == $activiteResponsable['id_creneau']) ? 'selected' : '';
                        echo "<option value='{$creneau['id_creneau']}' {$selected} class='bg-[#6D5D6E]'>{$creneau['heure_debut']} - {$creneau['heure_fin']}</option>";
                    }
                    echo "</select>";
    
                    echo "<button type='submit' class='bg-green-500 hover:bg-green-600 text-white p-2 rounded'>Modifier Créneau</button>";
                    echo "</form>";   

                    echo "<form method='post' class='flex flex-col md:flex-row justify-items-end gap-2 mt-0 md:mt-6'>";
                    echo "<input type='text' name='newActivityName' value='{$activiteResponsable['NomAct']}' class='p-2 w-48 border border-[#F4EEE0] rounded-md bg-[#6D5D6E]'>";
                    echo "<button type='submit' class='bg-green-500 hover:bg-green-600 text-white rounded p-2'>Modifier Activité</button>";
                    echo "</form></div>";
                }
                ?>
            </div>
        </div>
    
        <!-- Liste des participants et désinscription -->
        <div class="container mx-auto px-6">
            <h1 class="mb-4 mt-12 text-3xl font-extrabold text-white md:text-5xl lg:text-6xl"><span class="text-transparent bg-clip-text bg-[#4F4557]">Les participants</span> inscrits au site</h1>
            <p class="text-lg font-normal lg:text-xl text-gray-400">Explorez la communauté dynamique de participants inscrits sur notre site.</p>
            <ul class="flex flex-col md:flex-row gap-6 mt-4">
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
                    echo "<div class='md:w-1/3 w-[95%] mx-auto bg-[#6D5D6E] shadow-lg rounded-lg p-5 overflow-hidden'>";
                    echo "<div class='relative md:h-[4em]'>
                            <div class='text-xs font-bold uppercase text-[#F4EEE0] tracking-widest mb-2'>{$activities[0]['nom']} {$activities[0]['prenom']} </div>
                            <div class='text-xs font-bold uppercase text-[#F4EEE0] tracking-widest mb-2'>{$activities[0]['mail']}</div>
                            <div class='h-[1px] w-[98%] bg-white my-3'></div>
                          </div>";                   
    
                    // Liste des activités auxquelles le participant est inscrit
                    echo "<ul>";
    
                    foreach ($activities as $activity) {
                        echo "<form method='post' class='flex flex-col'>";
                        echo "<p class='font-semibold'>{$activity['NomAct']}</p>";
                        echo "<input type='hidden' name='activiteId' value='{$activity['id_activité']}'>";
                        echo "<input type='hidden' name='participantId' value='{$activity['num_participant']}'>";
                        // Vérification si le participant est inscrit à une activité
                        if ($activity['id_activité'] !== null) {
                            // Bouton "Désinscrire" si le participant est inscrit à une activité
                            echo "<button type='submit' class='bg-red-500 hover:bg-red-600 text-white mb-2 p-2 rounded'>Désinscrire</button></form></li>";
                        } else {
                            // Message si le participant n'est pas inscrit à une activité
                            echo "<p class='font-semibold'>Le participant n'est inscrit à aucune activité</p></form></li>";
                        }
                    }
                    echo "</ul></div>";
                }
                ?>
            </div>
        </div>
    </main>

    <script>
    const scrollToNextSection = () => {
        const nextSection = document.getElementById("main");
        if (nextSection) {
            nextSection.scrollIntoView({ behavior: "smooth" });
        }
    };
    </script>
</body>
</html>