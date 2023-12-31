<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../modele/classes/ProduitDAO.class.php";
$produitDAO = new ProduitDAO();

if (!isset($_SESSION['username'])) {
    header("location: ./login.php");
}

// On donne un accès direct au panel à tout le monde sauf les utilisateurs :
$panel = $_SESSION['roleId'] != 1 ? '<li><a href="panel.php">Panel</a></li>':"";

// On utilise ce fichier comme un réceptionniste d'ajout au panier :
    // Un ajout au panier est détecté.
if(isset($_POST['addCart'])) {
    $idProduit = $_POST["addCart"];
    // On ajoute cette id de produit au panier de l'utilisateur :
    $_SESSION['cart'][] = $idProduit;
}

$cartList = "";
if(count($_SESSION['cart']) == 0) {
    $cartList = "<p> Vide ..?</p>";
}
foreach ($_SESSION['cart'] as $idProduit) {
    $produit = $produitDAO->getById($idProduit);
    $cartList .= '<p>- '.$produit->getName().' : '.$produit->getPrice().'$</p>'; 
}

// Génération dynamique du catalogue :
// Explication du code : Nous parcourons le tableau des produits sur la base de données. Les attributs de chaque élément sont alors assignés dynamiquement à des balises HTML.

$produits = $produitDAO->getAll();
$dynamicHTML = '<form class="produits" action="../controleur/index.php" method="post"> ';
foreach ($produits as $produit) {
    $dynamicHTML .= '<div class="carte">';
        $dynamicHTML .= '<div class="img"><img src="'.$produit->getImgUrl().'"></div>';
        $dynamicHTML .= '<div class="desc">'.$produit->getDescription().'</div>';
        $dynamicHTML .= '<div class="titre">'.$produit->getName().'</div>';
        $dynamicHTML .= '<div class="box">';
            $dynamicHTML .= '<div class="prix">'.$produit->getPrice().'$</div>';
            $dynamicHTML .= '<div class="achat">';
            $dynamicHTML .= '<label for="'.$produit->getId().'">Ajouter au panier</label>';
            $dynamicHTML .= '<input type="submit" name="addCart" value="'.$produit->getId().'" id="'.$produit->getId().'" />';
            $dynamicHTML .= '</div>';

    $dynamicHTML .= '</div></div>';
}
$dynamicHTML .= '</form>';

require_once "../vue/index.view.php";

?>