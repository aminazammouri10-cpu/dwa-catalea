<?php
session_start();
require_once 'connexion.php';
require_once 'functions.php';

header('Content-Type: application/json');

// Récupère les données envoyées en AJAX
$data = json_decode(file_get_contents('php://input'), true);

$id_produit = isset($data['id_produit']) ? (int)$data['id_produit'] : 0;
$quantite   = isset($data['quantite'])   ? (int)$data['quantite']   : 0;

// Validation de base
if ($id_produit <= 0 || $quantite <= 0) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit();
}

// Vérifie que le produit existe et est disponible
$produit = retrieveProductById($pdo, $id_produit);

if (empty($produit)) {
    echo json_encode(['success' => false, 'message' => 'Produit introuvable.']);
    exit();
}

if (!$produit['disponibilite']) {
    echo json_encode(['success' => false, 'message' => 'Ce produit n\'est pas disponible à la vente.']);
    exit();
}

if ($quantite > $produit['stock']) {
    echo json_encode(['success' => false, 'message' => 'Stock insuffisant. Stock disponible : ' . $produit['stock']]);
    exit();
}

// Initialise le panier en session si nécessaire
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Ajoute ou met à jour la quantité dans le panier
if (isset($_SESSION['panier'][$id_produit])) {
    $nouvelleQuantite = $_SESSION['panier'][$id_produit] + $quantite;
    // Vérifie que la nouvelle quantité ne dépasse pas le stock
    if ($nouvelleQuantite > $produit['stock']) {
        $nouvelleQuantite = $produit['stock'];
    }
    $_SESSION['panier'][$id_produit] = $nouvelleQuantite;
} else {
    $_SESSION['panier'][$id_produit] = $quantite;
}

echo json_encode([
    'success' => true,
    'message' => 'Produit ajouté au panier !',
    'panier'  => $_SESSION['panier']
]);
