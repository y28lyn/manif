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
    <link rel="stylesheet" href="../../public/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <title>Connexion</title>
</head>
<body>
    <header>      
        <form method="post" class="absolute w-full z-50 top-0 left-0 mt-6 px-6">
                <button type="submit" name="deconnexion" class="bg-gray-950/90 hover:bg-black/90 text-white p-2 rounded duration-300">Déconnexion</button>
        </form>
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
            <div class="container mx-auto text-center relative">
                <h1 class="text-3xl md:text-4xl font-extrabold mb-4 px-6 md:px-0 text-white">
                    Participant
                </h1>
                <p class="text-xl text-gray-200 mb-8 px-6 md:px-64">Participez activement à notre manifestation lucrative pour soutenir une cause qui compte ! Chaque contribution compte, chaque action fait la différence.</p>
                <div class="space-x-4">
                    <button 
                        onclick="scrollToNextSection()"
                        class="text-white w-25 outline p-1 rounded transition ease-in-out delay-150 md:hover:-translate-y-1 md:hover:scale-105 duration-300"
                    >
                        <span class="p-5">Lire plus</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main id="main" class="container h-screen p-6 bg-[#000302] text-white">
        <!-- Liste des activités disponibles -->
        <h2 class="text-xl md:text-3xl text-center font-bold mb-2">Inscrivez-vous à nos activités pour soutenir notre cause !</h2>
        <ul class="flex flex-row gap-2">
            <?php
            while ($activite = $stmtActivites->fetch(PDO::FETCH_ASSOC)) {
                $activiteId = $activite['id_activité'];
                echo "<li class='p-6 w-1/3 h-[30vh] shadow-lg rounded bg-[#eef] text-[#000302]'>";
                echo "<p>{$activite['NomAct']} - {$activite['Description']}</p>";

                // Vérifier si l'utilisateur est déjà inscrit à cette activité
                $dejaInscrit = in_array($activiteId, $activitesInscrites);

                // Déplacer le formulaire à la fin du <li>
                echo "<form method='post'><input type='hidden' name='activiteId' value='{$activiteId}'>";
                if (!$dejaInscrit) {
                    // Afficher le bouton "S'inscrire" seulement si l'utilisateur n'est pas déjà inscrit
                    echo "<button type='submit' class='bg-blue-500 hover:bg-blue-600 text-white mt-1 mb-3 p-2 rounded'>S'inscrire</button>";
                } else {
                    // L'utilisateur est déjà inscrit, afficher un message sans le bouton
                    echo "<button class='bg-slate-500 text-white mt-1 mb-3 p-2 rounded cursor-default items-end justify-end' disabled>Déjà inscrit</button>";
                }
                echo "</form>";

                echo "</li>";
            }
            ?>
        </ul>

        <!-- Liste des activités inscrites -->
        <h2 class="text-xl md:text-3xl text-center font-bold mb-2 mt-6">Voici les activités auxquelles vous vous êtes inscrit</h2>
        <ul class="flex flex-row gap-2">
            <?php
            $queryInscrit = "SELECT a.* FROM activité a 
                            INNER JOIN participation p ON a.id_activité = p.id_activite 
                            WHERE p.num_participant = :participantId";
            $stmtInscrit = $pdo->prepare($queryInscrit);
            $stmtInscrit->bindParam(':participantId', $participantId);
            $stmtInscrit->execute();

            while ($activiteInscrite = $stmtInscrit->fetch(PDO::FETCH_ASSOC)) {
                $activiteIdInscrite = $activiteInscrite['id_activité'];
                echo "<li class='p-6 w-1/3 h-[30vh] shadow-lg rounded bg-[#eef] text-[#000302]'><p>{$activiteInscrite['NomAct']} - {$activiteInscrite['Description']}</p>";
                echo "<form method='post'><input type='hidden' name='desinscriptionId' value='{$activiteIdInscrite}'><button type='submit' class='bg-red-500 hover:bg-red-600 text-white mt-1 mb-3 p-2 rounded'>Se désinscrire</button></form></li>";
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
