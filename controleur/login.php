<?php
session_start();
require_once "../modele/connexion.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$identifiants['username'] = isset($_POST['username']) ? $_POST['username'] : null;
$identifiants['pwd'] = isset($_POST['pwd']) ? $_POST['pwd'] : null;
$message = "";

// Nettoie la session et les cookies :
function clear()
{
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_unset();
    session_destroy();
}

$connexion = new Connexion();

if (isset($_POST['signing-in'])) {
    if ($connexion->existeUtilisateur($identifiants) && $connexion->verifMdp($identifiants)) {
        // Si la connexion réussie alors (dans la variable de session se trouvent les infos user) :
        header("location: ./index.php");
    } else {
        $message = "Identification incorrecte. Essayez de nouveau.";
    }
} else if (isset($_POST['signing-up'])) {
    header("location: ./signup.php");
}

// Ce bloc de code sera réutilisé pour chaque déconnexion avec des formulaires redirigés sur ce fichier :
if (isset($_POST['disconnect'])) {
    clear();
    header("location: ./login.php");
}

if (isset($_POST["dissolve"])) {
    $connexion->deleteByUsername($_SESSION["username"]);
    clear();
    header("location: ./login.php");
}

require_once "../vue/login.view.php";
