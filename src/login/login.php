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
        echo "Identifiants invalides.";
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
    <div class="flex h-screen">
        <!-- Panneau gauche -->
        <div class="hidden md:flex items-center justify-center flex-1 bg-slate-700 text-black">
            <div class="max-w-md text-center">
                <img src="https://doodleipsum.com/600/flat?i=b802b74727eb07df4171a1b436cc89bb" alt="" class="w-full">
            </div>
        </div>
        <!-- Panneau droit -->
        <div class="w-full bg-slate-800 lg:w-1/2 flex items-center justify-center">
            <div class="max-w-md w-full p-6">
                <h1 class="text-3xl font-semibold mb-1 md:mb-3 text-white text-center">Connexion</h1>
                <h1 class="text-sm font-semibold mb-6 px-5 md:p-0 text-gray-200 text-center">Participez aux activités de cette manifestation pour aider à son financement !</h1>
                <form action="" method="post" class="">
                    <!-- Champs de formulaire pour le nom d'utilisateur et le mot de passe -->
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-300">Nom d'utilisateur</label>
                        <input type="text" name="username" id="username" class="mt-1 p-2 w-full border rounded-md transition-colors duration-300">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-300">Mot de passe</label>
                    <input type="password" name="password" id="password" class="mt-1 p-2 w-full border rounded-md transition-colors duration-300">
                    </div>
                    <!-- Bouton de connexion -->
                    <button type="submit" class="w-full bg-slate-200 text-black p-2 rounded-md hover:bg-slate-400 focus:bg-slate-400 transition-colors duration-300">Se connecter</button>
                    <!-- Bouton pour aller vers la page d'inscription -->
                    <p class="mt-4 text-sm text-white text-center">
                        Pas encore inscrit ? <a href="inscription.php" class="text-sky-200 hover:underline">S'inscrire</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
