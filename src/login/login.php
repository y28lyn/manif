<?php
include '../../includes/connexion.php';

// Fonction pour démarrer la session et rediriger l'utilisateur
function startSessionAndRedirect($role, $participantId) {
    session_start();
    $_SESSION['role'] = $role;
    $_SESSION['participantId'] = $participantId;

    switch ($role) {
        case 'admin':
            header('Location: ../admin/admin.php');
            break;
        case 'responsable':
            header('Location: ../responsable/responsable.php');
            break;
        case 'participant':
            header('Location: ../participant/participant.php');
            break;
        default:
            break;
    }

    exit();
}

// Vérification du formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Exemple basique (vérifie uniquement si l'utilisateur existe)
    $stmt = $pdo->prepare("SELECT * FROM user WHERE login = ? AND mdp = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Utilisateur valide, démarrer la session et rediriger
        startSessionAndRedirect($user['role'], $user['id_participant']);
    } else {
        // Gestion de l'échec de connexion
        echo "<script>alert('Identifiants invalides.')</script>";
    }
}
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
    <div class="flex justify-center h-screen bg-[#393646]">
        <!-- Panneau gauche -->
        <div class="hidden bg-cover lg:block lg:w-2/3" style="background-image: url(https://images.unsplash.com/photo-1465447142348-e9952c393450?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D)">
                <div class="flex items-center h-full px-20 bg-black bg-opacity-80">
                    <div>
                        <h2 class="text-4xl font-bold text-white">Renaissance urbaine</h2>
                        
                        <p class="max-w-xl mt-3 text-gray-300">Bâtisseurs de Quartiers, l'association Renaissance Urbaine s'engage avec ferveur à revitaliser nos espaces citadins, créant ainsi des communautés florissantes où l'unité, la créativité et la solidarité sont les pierres angulaires de notre projet commun.</p>
                    </div>
                </div>
            </div>  
        <!-- Panneau droit -->
        <div class="flex items-center w-full max-w-md px-6 mx-auto lg:w-2/6">
            <div class="flex-1">
                <div class="text-center">
                    <h2 class="text-4xl font-bold text-center text-white">Renaissance urbaine</h2>
                    
                    <p class="mt-3 text-gray-200">Connectez-vous pour accéder à votre compte</p>
                </div>
                <div class="mt-8">
                    <form action="" method="post">
                        <div>
                            <label for="username" class="block mb-2 text-sm text-gray-200">Login</label>
                            <input type="text" name="username" id="username" placeholder="Votre login" class="block w-full px-4 py-2 mt-2 bg-white border rounded-md placeholder-gray-600bg-gray-900 text-gray-900 border-gray-700 focus:border-[#F4EEE0] focus:ring-[#F4EEE0] focus:outline-none focus:ring focus:ring-opacity-40" />
                        </div>
                        <div class="mt-6">
                            <div class="flex justify-between mb-2">
                                <label for="password" class="text-sm text-gray-200">Mot de passe</label>
                                <a href="#" class="text-sm text-gray-400 focus:text-[#F4EEE0] hover:text-[#F4EEE0] hover:underline">Mot de passe oublié?</a>
                            </div>
                            <input type="password" name="password" id="password" placeholder="Votre mot de passe" class="block w-full px-4 py-2 mt-2 bg-white border rounded-md placeholder-gray-600bg-gray-900 text-gray-900 border-gray-700 focus:border-[#F4EEE0] focus:ring-[#F4EEE0] focus:outline-none focus:ring focus:ring-opacity-40" />
                        </div>
                        <div class="mt-6">
                            <button
                                type="submit"
                                class="w-full px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-[#6D5D6E] rounded-md hover:bg-[#4F4557] focus:outline-none focus:bg-[#4F4557] focus:ring focus:ring-[#F4EEE0] focus:ring-opacity-50">
                                Se connecter
                            </button>
                        </div>
                    </form>
                    <p class="mt-6 text-sm text-center text-gray-400">Pas encore inscrit? <a href="inscription.php" class="text-[#F4EEE0] focus:outline-none focus:underline hover:underline">Inscrivez-vous</a>.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
