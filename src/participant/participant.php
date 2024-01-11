<?php
include('../../includes/connexion.php');

// Vérifiez si l'utilisateur est connecté et si oui, récupérez son ID depuis la session
session_start();
if (isset($_SESSION['participantId'])) {
    $participantId = $_SESSION['participantId'];
} else {
    // Redirigez l'utilisateur vers la page de connexion s'il n'est pas connecté
    header("Location: login.php");
    exit();
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['activiteId'])) {
        $activiteId = $_POST['activiteId'];

        // Vérifier si l'utilisateur est déjà inscrit à cette activité
        $queryInscriptionExistante = "SELECT COUNT(*) FROM participation WHERE id_activite = :activiteId AND num_participant = :participantId";
        $stmtInscriptionExistante = $pdo->prepare($queryInscriptionExistante);
        $stmtInscriptionExistante->bindParam(':activiteId', $activiteId);
        $stmtInscriptionExistante->bindParam(':participantId', $participantId);
        $stmtInscriptionExistante->execute();
        $inscriptionExistante = $stmtInscriptionExistante->fetchColumn();

        if ($inscriptionExistante == 0) {
            // L'utilisateur n'est pas encore inscrit, procéder à l'inscription
            $queryInscription = "INSERT INTO participation (id_activite, num_participant, id_creneau) 
                                VALUES (:activiteId, :participantId, 1)"; // Vous devez ajuster l'ID du créneau
            $stmtInscription = $pdo->prepare($queryInscription);
            $stmtInscription->bindParam(':activiteId', $activiteId);
            $stmtInscription->bindParam(':participantId', $participantId);

            if ($stmtInscription->execute()) {
                echo "<script>alert('Inscription réussie pour l\'activité avec l\'ID : {$activiteId}');</script>";
                // Vous pouvez également mettre à jour la liste des activités inscrites sans recharger la page
            } else {
                echo "<script>alert('Erreur lors de l\'inscription.');</script>";
            }
        } else {
            // L'utilisateur est déjà inscrit à cette activité
            echo "<script>alert('Vous êtes déjà inscrit à cette activité.');</script>";
        }

        // Redirection pour éviter le rechargement du formulaire
        header("Location: participant.php");
        exit();
    } elseif (isset($_POST['desinscriptionId'])) {
        // Gérer la désinscription lorsque le formulaire est soumis
        $activiteId = $_POST['desinscriptionId'];

        // Supprimer l'inscription de la table participation
        $queryDesinscription = "DELETE FROM participation WHERE id_activite = :activiteId AND num_participant = :participantId";
        $stmtDesinscription = $pdo->prepare($queryDesinscription);
        $stmtDesinscription->bindParam(':activiteId', $activiteId);
        $stmtDesinscription->bindParam(':participantId', $participantId);

        if ($stmtDesinscription->execute()) {
            echo "<script>alert('Désinscription réussie pour l\'activité avec l\'ID : {$activiteId}');</script>";
            // Vous pouvez également mettre à jour la liste des activités inscrites sans recharger la page
        } else {
            echo "<script>alert('Erreur lors de la désinscription.');</script>";
        }

        // Redirection pour éviter le rechargement du formulaire
        header("Location: participant.php");
        exit();
    } elseif (isset($_POST['deconnexion'])) {
        // Détruire la session et rediriger vers la page de connexion
        session_destroy();
        header("Location: ../login/login.php");
        exit();
    }
}

// Requête pour récupérer les activités disponibles
$queryActivites = "SELECT * FROM activité";
$stmtActivites = $pdo->query($queryActivites);

// Requête pour récupérer les activités inscrites par l'utilisateur
$queryInscrit = "SELECT id_activite FROM participation WHERE num_participant = :participantId";
$stmtInscrit = $pdo->prepare($queryInscrit);
$stmtInscrit->bindParam(':participantId', $participantId);
$stmtInscrit->execute();
$activitesInscrites = $stmtInscrit->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://cdn-icons-png.flaticon.com/128/5928/5928517.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Participant</title>
</head>
<body>
    <header>      
        <div class="bg-cover bg-center min-h-screen flex items-center overflow-hidden relative">
            <div class="absolute inset-0">
                <img
                loading="lazy"
                class="w-full h-full object-cover object-center"
                src="../../public/participant.jpg"
                alt=""
                role="presentation"
                aria-hidden="true"
                />
            </div>
            <div class="absolute inset-0 bg-black/80 block from-black to-transparent"></div>
            <div class="relative text-white container flex flex-col justify-center p-6 mx-auto sm:py-12 lg:py-24 lg:flex-row">
                <div class="flex flex-col justify-center p-6 text-center rounded-sm lg:max-w-md xl:max-w-lg lg:text-left">
                    <h1 class="text-4xl md:text-5xl font-bold leadi sm:text-6xl">Participez à nos activités !</h1>
                    <p class="mt-6 mb-8 text-lg sm:mb-12">Plongez dans l'action ! Participez à nos activités dès maintenant.</p>
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
        <!-- Liste des activités disponibles -->
        <h1 class="mb-4 text-3xl font-extrabold text-white md:text-5xl lg:text-6xl"><span class="text-transparent bg-clip-text bg-[#E84545]">Les activités</span> disponibles</h1>
        <p class="text-lg font-normal lg:text-xl text-gray-400">Explorez les nombreuses activités disponibles et rejoignez-nous dans notre engagement commun pour un changement positif.</p>
        <ul class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-4">
            <?php
            while ($activite = $stmtActivites->fetch(PDO::FETCH_ASSOC)) {
                $activiteId = $activite['id_activité'];
                echo "<li class='w-[95%] mx-auto'> <div class='bg-[#2B2E4A] shadow-lg rounded-lg p-5 overflow-hidden'>";
                echo "<div class='relative h-fit md:h-[10em]'>
                        <div class='text-xs font-bold uppercase text-[#FFF] tracking-widest mb-2'>{$activite['NomAct']}</div>
                        <div class='h-[1px] w-[98%] bg-white my-3'></div>
                        <h3 class='text-2xl font-extrabold text-white leading-snug mb-2'>{$activite['Description']}</h3>
                      </div>";
                // Vérifier si l'utilisateur est déjà inscrit à cette activité
                $dejaInscrit = in_array($activiteId, $activitesInscrites);

                // Déplacer le formulaire à la fin du <li>
                echo "<form method='post' class='relative text-right'><input type='hidden' name='activiteId' value='{$activiteId}'>";
                if (!$dejaInscrit) {
                    // Afficher le bouton "S'inscrire" seulement si l'utilisateur n'est pas déjà inscrit
                    echo "<button type='submit' class='mt-2 inline-flex justify-center items-center bg-green-500 hover:bg-green-600 text-white p-2 rounded transition duration-150'>S'inscrire</button>";
                } else {
                    // L'utilisateur est déjà inscrit, afficher un message sans le bouton
                    echo "<button type='button' class='mt-2 inline-flex justify-center items-center bg-gray-700 text-gray-300 p-2 rounded cursor-default'>Déjà inscrit</button>";
                }
                echo "</form>";

                echo "</div></div></li>";
            }
            ?>
        </ul>

        <!-- Liste des activités inscrites -->
        <h1 class="mb-4 mt-12 text-3xl font-extrabold text-white md:text-5xl lg:text-6xl"><span class="text-transparent bg-clip-text bg-[#E84545]">Les activités</span> auxquelles vous êtes inscrits</h1>
        <p class="text-lg font-normal lg:text-xl text-gray-400">Explorez les activités auxquelles vous êtes inscrits, contribuant ainsi à notre cause commune. Plongez-vous dans une journée d'enthousiasme et d'engagement.</p>
        <ul class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-4">
            <?php
            $queryInscrit = "SELECT a.* FROM activité a 
                            INNER JOIN participation p ON a.id_activité = p.id_activite 
                            WHERE p.num_participant = :participantId";
            $stmtInscrit = $pdo->prepare($queryInscrit);
            $stmtInscrit->bindParam(':participantId', $participantId);
            $stmtInscrit->execute();

            $activitesInscrites = $stmtInscrit->fetchAll(PDO::FETCH_ASSOC);

            if (empty($activitesInscrites)) {
                echo "<p class='text-red-400'>Vous n'êtes inscrit à aucune activité pour le moment.</p>";
            } else {
                foreach ($activitesInscrites as $activiteInscrite) {
                    $activiteIdInscrite = $activiteInscrite['id_activité'];
                    echo "<li class='w-[95%] mx-auto'><div class='bg-[#2B2E4A] shadow-lg rounded-lg p-5 overflow-hidden'>";
                    echo "<div class='relative h-fit md:h-[10em]'>
                            <div class='text-xs font-bold uppercase text-[#FFF] tracking-widest mb-2'>{$activiteInscrite['NomAct']}</div>
                            <div class='h-[1px] w-[98%] bg-white my-3'></div>
                            <h3 class='text-2xl font-extrabold text-white leading-snug mb-2'>{$activiteInscrite['Description']}</h3>
                        </div>";                
                    echo "<form method='post' class='relative text-right'><input type='hidden' name='desinscriptionId' value='{$activiteIdInscrite}'><button type='submit' class='mt-2 inline-flex justify-center items-center bg-red-500 hover:bg-red-600 text-white p-2 rounded transition duration-150'>Se désinscrire</button></form></li>";
                }
            }
            ?>
        </ul>
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
