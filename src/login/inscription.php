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
                echo '<script>alert("Inscription réussie ! Redirection vers la page de connexion...")</script>';
                header("refresh:1;url=login.php"); // Rediriger après 1 secondes
            } catch (Exception $e) {
                // En cas d'échec, annuler la transaction
                $pdo->rollBack();
                echo '<script>alert("Une erreur est survenue lors de l\'inscription.")</script>';
            }
        }
    ?>
    <div class="flex justify-center h-screen bg-[#393646]">
        <!-- Panneau gauche -->
        <div class="hidden bg-cover lg:block lg:w-2/3" style="background-image: url(https://images.unsplash.com/photo-1532635241-17e820acc59f?q=80&w=2015&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D)">
            <div class="flex items-center h-full px-20 bg-black bg-opacity-80">
                <div>
                    <h2 class="text-4xl font-bold text-white">Renaissance urbaine</h2>
                    
                    <p class="max-w-xl mt-3 text-gray-300">Devenez un maillon actif de la Renaissance Urbaine ! Inscrivez-vous dès maintenant sur notre site pour contribuer à la construction de quartiers dynamiques et solidaires. Ensemble, façonnons un avenir urbain où l'unité et la créativité prospèrent. Rejoignez-nous dans cette belle aventure dès aujourd'hui !</p>
                </div>
            </div>
        </div>  
        <!-- Panneau droit -->
        <div class="flex items-center w-full max-w-md px-6 mx-auto lg:w-2/6">
            <div class="flex-1">
                <div class="text-center">
                    <h2 class="text-4xl font-bold text-center text-white">Renaissance urbaine</h2>
                    
                    <p class="mt-3 text-gray-200">Inscrivez-vous pour accéder à notre site</p>
                </div>
                <div class="mt-8">
                    <form action="" method="post">
                        <div class="flex flex-row gap-5">
                            <div>
                                <div>
                                    <label for="nom" class="block mb-2 text-sm text-gray-200 mt-2">Nom</label>
                                    <input type="text" name="nom"  placeholder="Votre nom" class="block w-full px-4 py-2 mt-2 bg-white border rounded-md placeholder-gray-600bg-gray-900 text-gray-900 border-gray-700 focus:border-[#F4EEE0] focus:ring-[#F4EEE0] focus:outline-none focus:ring focus:ring-opacity-40" />
                                </div>
                                <div>
                                    <label for="prenom" class="block mb-2 text-sm text-gray-200 mt-2">Prenom</label>
                                    <input type="text" name="prenom" placeholder="Votre prenom" class="block w-full px-4 py-2 mt-2 bg-white border rounded-md placeholder-gray-600bg-gray-900 text-gray-900 border-gray-700 focus:border-[#F4EEE0] focus:ring-[#F4EEE0] focus:outline-none focus:ring focus:ring-opacity-40" />
                                </div>
                                <div>
                                    <label for="mail" class="block mb-2 text-sm text-gray-200 mt-2">E-mail</label>
                                    <input type="email" name="mail" placeholder="Votre e-mail" class="block w-full px-4 py-2 mt-2 bg-white border rounded-md placeholder-gray-600bg-gray-900 text-gray-900 border-gray-700 focus:border-[#F4EEE0] focus:ring-[#F4EEE0] focus:outline-none focus:ring focus:ring-opacity-40" />
                                </div>
                            </div>
                            <div>
                                <div>
                                    <label for="login" class="block mb-2 text-sm text-gray-200 mt-2">Login</label>
                                    <input type="text" name="login" placeholder="Votre login" class="block w-full px-4 py-2 mt-2 bg-white border rounded-md placeholder-gray-600bg-gray-900 text-gray-900 border-gray-700 focus:border-[#F4EEE0] focus:ring-[#F4EEE0] focus:outline-none focus:ring focus:ring-opacity-40" />
                                </div>
                                <div>
                                    <label for="mdp" class="block mb-2 text-sm text-gray-200 mt-2">Mot de passe</label>
                                    <input type="password" name="mdp" placeholder="Votre mot de passe" class="block w-full px-4 py-2 mt-2 bg-white border rounded-md placeholder-gray-600bg-gray-900 text-gray-900 border-gray-700 focus:border-[#F4EEE0] focus:ring-[#F4EEE0] focus:outline-none focus:ring focus:ring-opacity-40" />
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex flex-row gap-5">
                            <button
                                type="submit"
                                class="w-1/2 px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-[#6D5D6E] rounded-md hover:bg-[#4F4557] focus:outline-none focus:bg-[#4F4557] focus:ring focus:ring-[#F4EEE0] focus:ring-opacity-50">
                                S'inscrire
                            </button>
                            <a 
                                href="login.php" 
                                name="deconnexion" 
                                class="w-1/2 px-4 py-2 tracking-wide text-center text-white transition-colors duration-200 transform bg-red-500 rounded-md hover:bg-red-600 focus:outline-none focus:bg-red-600 focus:ring focus:ring-[#F4EEE0] focus:ring-opacity-50">
                            Annuler
                        </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
