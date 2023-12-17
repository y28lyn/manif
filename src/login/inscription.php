<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Inscription</title>
</head>
<body>
    <?php
        // Inclure le fichier de connexion à la base de données
        include('../../includes/connexion.php');
        if (isset($_POST['deconnexion'])) {
            // Détruire la session et rediriger vers la page de connexion
            session_destroy();
            header("Location: login.php");
            exit();
        }
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $mail = $_POST['mail'];
            $login = $_POST['login'];
            $mdp = $_POST['mdp'];
            // Commencer la transaction
            $pdo->beginTransaction();
            try {
                // Insérer les données dans la table participant
                $queryParticipant = "INSERT INTO participant (nom, prenom, mail) VALUES (:nom, :prenom, :mail)";
                $stmtParticipant = $pdo->prepare($queryParticipant);
                $stmtParticipant->bindParam(':nom', $nom);
                $stmtParticipant->bindParam(':prenom', $prenom);
                $stmtParticipant->bindParam(':mail', $mail);
                $stmtParticipant->execute();
                // Récupérer l'ID du participant inséré
                $idParticipant = $pdo->lastInsertId();
                // Insérer les données dans la table user
                $queryUser = "INSERT INTO user (login, mdp, id_participant, role) VALUES (:login, :mdp, :id_participant, 'participant')";
                $stmtUser = $pdo->prepare($queryUser);
                $stmtUser->bindParam(':login', $login);
                $stmtUser->bindParam(':mdp', $mdp);
                $stmtUser->bindParam(':id_participant', $idParticipant);
                $stmtUser->execute();
                // Valider la transaction
                $pdo->commit();
                echo '<p class="text-green-500">Inscription réussie ! Redirection vers la page de connexion...</p>';
                header("refresh:3;url=login.php"); // Rediriger après 3 secondes
            } catch (Exception $e) {
                // En cas d'échec, annuler la transaction
                $pdo->rollBack();
                echo '<p class="text-red-500">Une erreur est survenue lors de l\'inscription.</p>';
            }
        }
    ?>
    <div class="flex h-screen">
        <!-- Panneau gauche -->
        <div class="hidden md:flex items-center justify-center flex-1 bg-slate-700 text-black">
            <div class="max-w-md text-center">
                <img src="https://doodleipsum.com/600/flat?i=79f653fe0d821dcf0641932480645c43" alt="" class="w-full">
            </div>
        </div>
        <!-- Panneau droit -->
        <div class="w-full bg-slate-800 lg:w-1/2 flex items-center justify-center">
            <div class="max-w-md w-full p-6">
                <h1 class="text-3xl font-semibold mb-1 md:mb-3 text-white text-center">Inscription</h1>
                <form action="" method="post" class="">
                    <div class="mb-2">
                        <label for="nom" class="block text-sm font-medium text-gray-300">Nom</label>
                        <input type="text" name="nom" required class="mt-1 p-2 w-full border rounded-md transition-colors duration-300">
                    </div>
                    <div class="mb-2">
                        <label for="prenom" class="block text-sm font-medium text-gray-300">Prénom</label>
                        <input type="text" name="prenom" required class="mt-1 p-2 w-full border rounded-md transition-colors duration-300">
                    </div>
                    <div class="mb-2">
                        <label for="mail" class="block text-sm font-medium text-gray-300">Email</label>
                        <input type="email" name="mail" required class="mt-1 p-2 w-full border rounded-md transition-colors duration-300">
                    </div>
                    <div class="mb-2">
                        <label for="login" class="block text-sm font-medium text-gray-300">Login</label>
                        <input type="text" name="login" required class="mt-1 p-2 w-full border rounded-md transition-colors duration-300">
                    </div>
                    <div class="mb-2">
                        <label for="mdp" class="block text-sm font-medium text-gray-300">Mot de passe</label>
                        <input type="password" name="mdp" required class="mt-1 p-2 w-full border rounded-md transition-colors duration-300">
                    </div>
                    <div class="flex flex-row gap-2">
                        <button type="submit" class="w-1/2 bg-slate-200 text-black p-2 rounded-md hover:bg-slate-400 focus:bg-slate-400 transition-colors duration-300">S'inscrire</button>
                        <button href="login.php" name="deconnexion" class="w-1/2 bg-red-500 hover:bg-red-600 text-white p-2 rounded duration-300" onclick="window.location.href='login.php';">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>    
</body>
</html>
