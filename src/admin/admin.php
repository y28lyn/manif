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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifyCreneau'])) {
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
        // Redirection après le traitement réussi
        header("refresh:0.2;url={$_SERVER['PHP_SELF']}"); // Rediriger après 1 secondes
        exit(); // Assurez-vous de terminer l'exécution du script après la redirection    
    } else {
        echo "<script>alert('Erreur lors de la modification du créneau.');</script>";
    }
}

// Traitement du formulaire pour créer une activité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createActivity'])) {
    $newActivityTitle = $_POST['newActivityTitle'];
    $newActivityDescription = $_POST['newActivityDescription'];
    $resp = $_POST['responsableId'];
    $creneau = $_POST['creneauId'];

    // Vous devez ajuster la requête INSERT en fonction de votre structure de base de données
    $queryCreateActivity = "INSERT INTO activité (NomAct, Description, num_resp) VALUES (:newActivityTitle, :newActivityDescription, :resp)";
    $stmtCreateActivity = $pdo->prepare($queryCreateActivity);
    $stmtCreateActivity->bindParam(':newActivityTitle', $newActivityTitle);
    $stmtCreateActivity->bindParam(':newActivityDescription', $newActivityDescription);
    $stmtCreateActivity->bindParam(':resp', $resp);

    if ($stmtCreateActivity->execute()) {
        // Récupérer l'ID de l'activité que vous venez de créer
        $lastActivityID = $pdo->lastInsertId();

        // Insérer le créneau lié à l'activité
        $queryInsertCreneau = "INSERT INTO avoir (id_activite, id_creneau) VALUES (:idActivite, :creneau)";
        $stmtInsertCreneau = $pdo->prepare($queryInsertCreneau);
        $stmtInsertCreneau->bindParam(':idActivite', $lastActivityID);
        $stmtInsertCreneau->bindParam(':creneau', $creneau);

        if ($stmtInsertCreneau->execute()) {
            echo "<script>alert('Activité créée avec succès : {$newActivityTitle}');</script>";
            // Redirection après le traitement réussi
            header("refresh:0.2;url={$_SERVER['PHP_SELF']}"); // Rediriger après 1 seconde
            exit(); // Assurez-vous de terminer l'exécution du script après la redirection
        } else {
            echo "<script>alert('Erreur lors de l\'insertion du créneau de l\'activité.');</script>";
        }
    } else {
        echo "<script>alert('Erreur lors de la création de l\'activité.');</script>";
    }
}

// Traitement du formulaire pour créer un créneau
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createCreneau'])) {
    $newCreneauHeureDebut = $_POST['newCreneauHeureDebut'];
    $newCreneauHeureFin = $_POST['newCreneauHeureFin'];

    // Vous devez ajuster la requête INSERT en fonction de votre structure de base de données
    $queryCreateCreneau = "INSERT INTO creneau (heure_debut, heure_fin) VALUES (:newCreneauHeureDebut, :newCreneauHeureFin)";
    $stmtCreateCreneau = $pdo->prepare($queryCreateCreneau);
    $stmtCreateCreneau->bindParam(':newCreneauHeureDebut', $newCreneauHeureDebut);
    $stmtCreateCreneau->bindParam(':newCreneauHeureFin', $newCreneauHeureFin);

    if ($stmtCreateCreneau->execute()) {
        echo "<script>alert('Créneau créé avec succès de {$newCreneauHeureDebut} à {$newCreneauHeureFin}.');</script>";
        // Redirection après le traitement réussi
        header("refresh:0.2;url={$_SERVER['PHP_SELF']}"); // Rediriger après 1 seconde
        exit(); // Assurez-vous de terminer l'exécution du script après la redirection
    } else {
        echo "<script>alert('Erreur lors de la création du créneau. Veuillez vérifier les champs saisis.');</script>";
    }
}

// Traitement du formulaire pour créer un responsable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createResponsable'])) {
    $newRespName = $_POST['newRespName'];
    $newRespFirstName = $_POST['newRespFirstName'];
    $newRespLogin = $_POST['newRespLogin'];
    $newRespPassword = $_POST['newRespPassword'];

    // Vous devez ajuster la requête INSERT en fonction de votre structure de base de données
    $queryCreateResponsable = "INSERT INTO responsable (Nom, Prenom) VALUES (:newRespName, :newRespFirstName)";
    $stmtCreateResponsable = $pdo->prepare($queryCreateResponsable);
    $stmtCreateResponsable->bindParam(':newRespName', $newRespName);
    $stmtCreateResponsable->bindParam(':newRespFirstName', $newRespFirstName);

    if ($stmtCreateResponsable->execute()) {
        // Récupérer l'ID du responsable que vous venez de créer
        $lastResponsableID = $pdo->lastInsertId();

        // Insérer le nouvel utilisateur lié au responsable
        $queryInsertUser = "INSERT INTO user (login, mdp, id_resp, role) VALUES (:newRespLogin, :newRespPassword, :lastResponsableID, 'responsable')";
        $stmtInsertUser = $pdo->prepare($queryInsertUser);
        $stmtInsertUser->bindParam(':newRespLogin', $newRespLogin);
        $stmtInsertUser->bindParam(':newRespPassword', $newRespPassword);
        $stmtInsertUser->bindParam(':lastResponsableID', $lastResponsableID);

        if ($stmtInsertUser->execute()) {
            // Succès
            echo "<script>alert('Responsable {$newRespName} {$newRespFirstName} créé avec succès.');</script>";
            // Redirection après le traitement réussi
            header("refresh:0.2;url={$_SERVER['PHP_SELF']}"); // Rediriger après 1 seconde
            exit(); // Assurez-vous de terminer l'exécution du script après la redirection
        } else {
            // Gestion de l'erreur d'insertion de l'utilisateur
            echo "Erreur lors de la création de l'utilisateur.";
        }
    } else {
        // Gestion de l'erreur d'insertion du responsable
        echo "Erreur lors de la création du responsable.";
    }
}

// Traitement du formulaire pour modifier une activité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifyActivity'])) {
    // Vérifier si 'activiteId' est défini dans $_POST
    if (isset($_POST['activiteId'])) {
        $activiteId = $_POST['activiteId'];
        $newActivityTitle = $_POST['newActivityTitle'];
        $newActivityDescription = $_POST['newActivityDescription'];
        $newResponsable = $_POST['newResponsable'];

        // Vous devez ajuster la requête UPDATE en fonction de votre structure de base de données
        $queryUpdateActivity = "UPDATE activité SET NomAct = :newActivityTitle, Description = :newActivityDescription, num_resp = :newResponsable WHERE id_activité = :activiteId";
        $stmtUpdateActivity = $pdo->prepare($queryUpdateActivity);
        $stmtUpdateActivity->bindParam(':newActivityTitle', $newActivityTitle);
        $stmtUpdateActivity->bindParam(':newActivityDescription', $newActivityDescription);
        $stmtUpdateActivity->bindParam(':newResponsable', $newResponsable);
        $stmtUpdateActivity->bindParam(':activiteId', $activiteId);

        if ($stmtUpdateActivity->execute()) {
            echo "<script>alert('Activité modifiée avec succès : {$newActivityTitle}');</script>";
            // Redirection après le traitement réussi
            header("refresh:0.2;url={$_SERVER['PHP_SELF']}"); // Rediriger après 1 seconde
            exit(); // Assurez-vous de terminer l'exécution du script après la redirection        
        } else {
            echo "<script>alert('Erreur lors de la modification de l\'activité.');</script>";
        }
    } else {
        // Gérer le cas où 'activiteId' n'est pas défini dans $_POST
        echo "<script>alert('Erreur : identifiant de l\'activité non spécifié.');</script>";
    }
}


// Traitement du formulaire de modification du participant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifyParticipant'])) {
    $participantId = $_POST['participantId'];
    $newParticipantName = $_POST['newParticipantName'];
    $newParticipantFirstName = $_POST['newParticipantFirstName'];
    $newParticipantEmail = $_POST['newParticipantEmail'];
    $newLogin = $_POST['newLogin'];
    $newPassword = $_POST['newPassword'];

    $queryUpdateParticipant = "UPDATE participant SET nom = :newParticipantName, prenom = :newParticipantFirstName, mail = :newParticipantEmail WHERE num_participant = :participantId";
    $stmtUpdateParticipant = $pdo->prepare($queryUpdateParticipant);
    $stmtUpdateParticipant->bindParam(':newParticipantName', $newParticipantName);
    $stmtUpdateParticipant->bindParam(':newParticipantFirstName', $newParticipantFirstName);
    $stmtUpdateParticipant->bindParam(':newParticipantEmail', $newParticipantEmail);
    $stmtUpdateParticipant->bindParam(':participantId', $participantId);
    
    // Exécutez la requête pour mettre à jour les informations du participant
    if ($stmtUpdateParticipant->execute()) {
        // La mise à jour de la table participant a réussi
        // Continuez avec la mise à jour de la table user
        $queryUpdateUser = "UPDATE user SET login = :newLogin, mdp = :newPassword WHERE id_participant = :participantId";
        $stmtUpdateUser = $pdo->prepare($queryUpdateUser);
        $stmtUpdateUser->bindParam(':newLogin', $newLogin);
        $stmtUpdateUser->bindParam(':newPassword', $newPassword);
        $stmtUpdateUser->bindParam(':participantId', $participantId);
    
        // Exécutez la requête pour mettre à jour les informations de l'utilisateur
        if ($stmtUpdateUser->execute()) {
            // La mise à jour de la table user a réussi
            echo "<script>alert('Participant modifié avec succès : {$newParticipantName} {$newParticipantFirstName}');</script>";
            // Redirection après le traitement réussi
            header("refresh:0.2;url={$_SERVER['PHP_SELF']}"); // Rediriger après 1 seconde
            exit(); // Assurez-vous de terminer l'exécution du script après la redirection
        } else {
            // Erreur lors de la mise à jour de la table user
            echo "<script>alert('Erreur lors de la mise à jour des informations utilisateur.');</script>";
        }
    } else {
        // Erreur lors de la mise à jour de la table participant
        echo "<script>alert('Erreur lors de la mise à jour des informations du participant.');</script>";
    }    
}

// Traitement du formulaire de désinscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteParticipation'])) {
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
        // Redirection après le traitement réussi
        header("refresh:0.2;url={$_SERVER['PHP_SELF']}"); // Rediriger après 1 secondes
        exit(); // Assurez-vous de terminer l'exécution du script après la redirection
    } else {
        echo "<script>alert('Erreur lors de la désinscription.');</script>";
    }
}

// Traitement du formulaire de suppression du participant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteParticipant'])) {
    $participantIdToDelete = $_POST['participantId'];

    // Vous devez ajuster la requête DELETE en fonction de votre structure de base de données
    $queryDeleteParticipations = "DELETE FROM participation WHERE num_participant = :participantId";
    $stmtDeleteParticipations = $pdo->prepare($queryDeleteParticipations);
    $stmtDeleteParticipations->bindParam(':participantId', $participantIdToDelete);

    // Supprimer également l'entrée correspondante dans la table 'user'
    $queryDeleteUser = "DELETE FROM user WHERE id_participant = :participantId";
    $stmtDeleteUser = $pdo->prepare($queryDeleteUser);
    $stmtDeleteUser->bindParam(':participantId', $participantIdToDelete);

    // Utilisez une transaction pour assurer la cohérence entre les trois suppressions
    try {
        $pdo->beginTransaction();

        // Supprimer d'abord les participations dans la table 'participation'
        $stmtDeleteParticipations->execute();

        // Ensuite, supprimer l'entrée dans la table 'user'
        $stmtDeleteUser->execute();

        // Puis supprimer l'entrée dans la table 'participant'
        $queryDeleteParticipant = "DELETE FROM participant WHERE num_participant = :participantId";
        $stmtDeleteParticipant = $pdo->prepare($queryDeleteParticipant);
        $stmtDeleteParticipant->bindParam(':participantId', $participantIdToDelete);
        $stmtDeleteParticipant->execute();

        // Valider la transaction
        $pdo->commit();

        // La suppression du participant a réussi
        echo "<script>alert('Participant supprimé avec succès.');</script>";
        // Redirection après le traitement réussi
        header("refresh:1;url={$_SERVER['PHP_SELF']}"); // Rediriger après 1 seconde
        exit();
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();

        // Afficher l'erreur
        echo "<script>alert('Erreur lors de la suppression du participant.');</script>";
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
$queryParticipantsResponsable = "SELECT p.*, u.*, a.NomAct, a.id_activité
                                 FROM participant p
                                 LEFT JOIN participation part ON p.num_participant = part.num_participant
                                 LEFT JOIN activité a ON part.id_activite = a.id_activité
                                 LEFT JOIN user u ON p.num_participant = u.id_participant";
$stmtParticipantsResponsable = $pdo->query($queryParticipantsResponsable);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://cdn-icons-png.flaticon.com/128/5928/5928517.png">
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
                <div class="flex flex-col justify-center p-6 text-center rounded-sm lg:max-w-md xl:max-w-lg lg:text-left">
                    <h1 class="text-4xl md:text-5xl font-bold leadi sm:text-6xl">Panneau de contrôle complet de l'administration.</h1>
                    <p class="mt-6 mb-8 text-lg sm:mb-12">Soyez le maître d'orchestre de l'action en gérant avec assurance les participants et les activités.</p>
                    <div class="flex flex-col md:flex-row gap-5 w-full">
                        <button 
                            onclick="scrollToNextSection()"
                            class="w-full md:w-1/2 px-8 py-3 text-lg font-semibold rounded bg-[#E84545] text-gray-50 transition ease-in-out delay-150 md:hover:-translate-y-1 md:hover:scale-105 duration-300"
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

    <main id="main" class="p-6 bg-[#000500] text-white">
        <!-- Liste des créations / affectations -->
        <div class="container mx-auto">
            <h1 class="mb-4 mt-12 text-3xl font-extrabold text-white md:text-5xl lg:text-6xl"><span class="text-transparent bg-clip-text bg-[#E84545]">Les créations</span> et affectations</h1>
            <p class="text-lg font-normal lg:text-xl text-gray-400">Créez des activités et créneaux puis affecter leurs des responsables.</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <!-- Formulaire pour créer une activité -->
                <form method="post" class="flex flex-col bg-[#2B2E4A] shadow-lg rounded-lg p-5 overflow-hidden w-[95%] mx-auto">
                    <div class='relative md:h-[2.5em]'>
                        <div class='text-xs font-bold uppercase text-[#FFF] tracking-widest mb-2'>Crée une activité</div>
                        <div class='h-[1px] w-[98%] bg-white my-3'></div>
                    </div>
                    <div class="flex flex-col">
                        <label for="titre" class="text-md font-semibold mb-1">Titre</label>
                        <input type="text" name="newActivityTitle" placeholder="Nouveau titre" class="p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]">
                        <label for="description" class="text-md font-semibold my-1">Description</label>
                        <textarea name="newActivityDescription" placeholder="Nouvelle description" class="p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]"></textarea>
                        <label for="responsable" class="text-md font-semibold my-1">Responsable</label>
                        <select name="responsableId" class="w-48 p-2 border border-[#FFF] rounded-md bg-[#2B2E4A]">
                            <!-- Vous devez ajuster la requête SELECT pour récupérer les responsables disponibles -->
                            <?php
                            $queryResponsables = "SELECT * FROM responsable";
                            $stmtResponsables = $pdo->query($queryResponsables);
                            while ($responsable = $stmtResponsables->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$responsable['num_resp']}'>{$responsable['Nom']} {$responsable['Prenom']}</option>";
                            }
                            ?>
                        </select>
                        <label for="creneau" class="text-md font-semibold my-1">Créneau</label>
                        <select name="creneauId" class="w-48 p-2 border border-[#FFF] rounded-md bg-[#2B2E4A]">
                            <!-- Vous devez ajuster la requête SELECT pour récupérer les créneaux disponibles -->
                            <?php
                            $queryCreneaux = "SELECT * FROM creneau";
                            $stmtCreneaux = $pdo->query($queryCreneaux);
                            while ($creneau = $stmtCreneaux->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$creneau['id_creneau']}'>{$creneau['heure_debut']} - {$creneau['heure_fin']}</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="createActivity" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded mt-2">Créer l'activité</button>
                    </div>
                </form>

                <form method="post" class="flex flex-col bg-[#2B2E4A] shadow-lg rounded-lg p-5 overflow-hidden w-[95%] mx-auto">
                    <div class='relative md:h-[2.5em]'>
                        <div class='text-xs font-bold uppercase text-[#FFF] tracking-widest mb-2'>Créer un créneau</div>
                        <div class='h-[1px] w-[98%] bg-white my-3'></div>
                    </div>
                    <div class="flex flex-col">
                        <label for="heure_debut" class="text-md font-semibold my-1">Heure de début</label>
                        <input type="time" id="heure_debut" name="newCreneauHeureDebut" class="p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]" required><br>

                        <label for="heure_fin" class="text-md font-semibold my-1">Heure de fin</label>
                        <input type="time" id="heure_fin" name="newCreneauHeureFin" class="p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]" required><br>

                        <button type="submit" name="createCreneau" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded mt-2 md:mt-28">Ajouter le créneau</button>
                    </div>
                </form>

                <!-- Formulaire pour créer un responsable -->
                <form method="post" class="flex flex-col bg-[#2B2E4A] shadow-lg rounded-lg p-5 overflow-hidden w-[95%] mx-auto">
                    <div class='relative md:h-[2.5em]'>
                        <div class='text-xs font-bold uppercase text-[#FFF] tracking-widest mb-2'>Créer un responsable</div>
                        <div class='h-[1px] w-[98%] bg-white my-3'></div>
                    </div>
                    <div class="flex flex-col">
                        <label for="newRespName" class="text-md font-semibold my-1">Nom</label>
                        <input type="text" name="newRespName" placeholder="Nouveau nom" class="p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]">

                        <label for="newRespFirstName" class="text-md font-semibold my-1">Prénom</label>
                        <input type="text" name="newRespFirstName" placeholder="Nouveau prénom" class="p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]">

                        <label for="newRespLogin" class="text-md font-semibold my-1">Login</label>
                        <input type="text" name="newRespLogin" placeholder="Nouveau login" class="p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]">

                        <label for="newRespPassword" class="text-md font-semibold my-1">Mot de passe</label>
                        <input type="password" name="newRespPassword" placeholder="Nouveau mot de passe" class="p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]">

                        <button type="submit" name="createResponsable" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded mt-5">Créer le responsable</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des activités et modification de leur créneau -->
        <div class="container mx-auto">
            <h1 class="mb-4 mt-12 text-3xl font-extrabold text-white md:text-5xl lg:text-6xl"><span class="text-transparent bg-clip-text bg-[#E84545]">Les activités</span> disponibles</h1>
            <p class="text-lg font-normal lg:text-xl text-gray-400">Modifier ou supprimer les activités.</p>
            
            <ul class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">            
                <?php
                while ($activiteResponsable = $stmtActivitesResponsable->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li class='w-[95%] mx-auto'><div class='bg-[#2B2E4A] shadow-lg rounded-lg p-5 overflow-hidden'>";
                    echo "<div class='relative md:h-[2.5em]'>
                            <div class='text-xs font-bold uppercase text-[#FFF] tracking-widest mb-2'>{$activiteResponsable['NomAct']}</div>
                            <div class='h-[1px] w-[98%] bg-white my-3'></div>
                          </div>";   

                          echo "<form method='post' class='flex flex-col gap-2'>";
                          echo "<input type='hidden' name='activiteId' value='{$activiteResponsable['id_activité']}'>";
                          echo "<input type='text' name='newActivityTitle' value='{$activiteResponsable['NomAct']}' placeholder='Nouveau titre' class='p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]'>";
                          echo "<textarea name='newActivityDescription' placeholder='Nouvelle description' class='p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]'>{$activiteResponsable['Description']}</textarea>";
                          echo "<select name='newResponsable' class='w-48 p-2 border border-[#FFF] rounded-md bg-[#2B2E4A]'>";
                          $queryResponsables = "SELECT * FROM responsable";
                          $stmtResponsables = $pdo->query($queryResponsables);
                          while ($responsable = $stmtResponsables->fetch(PDO::FETCH_ASSOC)) {
                              $selected = ($responsable['num_resp'] == $activiteResponsable['num_resp']) ? 'selected' : '';
                              echo "<option value='{$responsable['num_resp']}' $selected>{$responsable['Nom']} {$responsable['Prenom']}</option>";
                          }
                          echo "</select>";
                          echo "<button type='submit' name='modifyActivity' class='bg-green-500 hover:bg-green-600 text-white p-2 rounded'>Modifier Activité</button>";
                          echo "</form>";

                    // Formulaire pour la modification du créneau
                    echo "<div class='h-[1px] w-[98%] bg-white my-3'></div>";
                    echo "<form method='post' class='flex flex-col gap-2 mt-2'><input type='hidden' name='activiteId' value='{$activiteResponsable['id_activité']}'>";
                    echo "<p class='text-md font-extrabold text-white leading-snug mb-2'>Heure de début : {$activiteResponsable['heure_debut']}</p>
                    <p class='text-md font-extrabold text-white leading-snug'>Heure de fin : {$activiteResponsable['heure_fin']}</p>";   
                    // Vous devez ajuster la requête SELECT pour récupérer les créneaux disponibles
                    $queryCreneaux = "SELECT * FROM creneau";
                    $stmtCreneaux = $pdo->query($queryCreneaux);
                    echo "<select name='creneauId' class='w-48 p-2 border border-[#FFF] rounded-md bg-[#2B2E4A]'>";
                    while ($creneau = $stmtCreneaux->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($creneau['id_creneau'] == $activiteResponsable['id_creneau']) ? 'selected' : '';
                        echo "<option value='{$creneau['id_creneau']}' {$selected} class='bg-[#2B2E4A]'>{$creneau['heure_debut']} - {$creneau['heure_fin']}</option>";
                    }
                    echo "</select>";
                    echo "<button type='submit' name='modifyCreneau' class='bg-green-500 hover:bg-green-600 text-white p-2 rounded'>Modifier Créneau</button>";
                    echo "</form></div></li>";   
                }
                ?>
            </div>
        </div>

        <!-- Liste des participants -->
        <div class="container mx-auto">
            <h1 class="mb-4 mt-12 text-3xl font-extrabold text-white md:text-5xl lg:text-6xl"><span class="text-transparent bg-clip-text bg-[#E84545]">Les participants</span> inscrits au site</h1>
            <p class="text-lg font-normal lg:text-xl text-gray-400">Modifier les informations d'un participant, le banir d'une activité ou du site.</p>

            <ul class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
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
                    echo "<li class='w-[95%] mx-auto'><div class='bg-[#2B2E4A] shadow-lg rounded-lg p-5 overflow-hidden'>";
                    echo "<div class='relative md:h-[2.5em]'>
                            <div class='text-xs font-bold uppercase text-[#FFF] tracking-widest mb-2'>{$activities[0]['nom']} {$activities[0]['prenom']} </div>
                            <div class='text-xs font-bold uppercase text-[#FFF] tracking-widest'>{$activities[0]['mail']}</div>
                          </div>";       
                            
                    // Modification des informations du participant
                    echo "<div class='h-[1px] w-[98%] bg-white my-3'></div>";
                    echo "<form method='post' class='flex flex-col gap-2'>";
                    echo "<input type='hidden' name='participantId' value='{$activities[0]['num_participant']}'>";
                    echo "<input type='text' name='newParticipantName' value='{$activities[0]['nom']}' placeholder='Nouveau nom' class='p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]'>";
                    echo "<input type='text' name='newParticipantFirstName' value='{$activities[0]['prenom']}' placeholder='Nouveau prénom' class='p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]'>";
                    echo "<input type='text' name='newParticipantEmail' value='{$activities[0]['mail']}' placeholder='Nouveau e-mail' class='p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]'>";
                    echo "<input type='text' name='newLogin' value='{$activities[0]['login']}' placeholder='Nouveau login' class='p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]'>";
                    echo "<input type='text' name='newPassword' value='{$activities[0]['mdp']}' placeholder='Nouveau mot de passe' class='p-2 w-48 border border-[#FFF] rounded-md bg-[#2B2E4A]'>";
                    echo "<button type='submit' name='modifyParticipant' class='bg-green-500 hover:bg-green-600 text-white p-2 rounded'>Modifier Participant</button>";
                    echo "</form>";
                    echo "<div class='h-[0.5px] w-[98%] bg-white my-3'></div>";

                    foreach ($activities as $activity) {
                        echo "<form method='post' class='flex flex-col gap-1'>";
                        echo "<p class='font-semibold'>{$activity['NomAct']}</p>";
                        echo "<input type='hidden' name='activiteId' value='{$activity['id_activité']}'>";
                        echo "<input type='hidden' name='participantId' value='{$activity['num_participant']}'>";
                        // Vérification si le participant est inscrit à une activité
                        if ($activity['id_activité'] !== null) {
                            // Bouton "Désinscrire" si le participant est inscrit à une activité
                            echo "<button type='submit' name='deleteParticipation' class='bg-red-500 hover:bg-red-600 text-white p-2 rounded'>Désinscrire</button>";
                        } else {
                            // Message si le participant n'est pas inscrit à une activité
                            echo "<p class='font-semibold'>Le participant n'est inscrit à aucune activité</p>";
                        }
                    }

                    // Ajouter le formulaire pour supprimer le participant
                    echo "<div class='h-[1px] w-[98%] bg-white my-3'></div>";
                    echo "<form method='post' class='flex flex-col'>";
                    echo "<input type='hidden' name='participantId' value='{$activities[0]['num_participant']}'>";
                    echo "<button type='submit' name='deleteParticipant' class='bg-red-500 hover:bg-red-600 text-white mb-2 p-2 rounded'>Supprimer participant</button>";
                    echo "</form>";
                    echo "</div></li>";
                }
                ?>
            </ul>
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