<?php
session_start();
require_once 'connexion.php';
require_once 'functions.php';

header('Content-Type: application/json');

$data      = json_decode(file_get_contents('php://input'), true);
$action    = isset($data['action'])     ? $data['action']        : '';
$idProduit = isset($data['id_produit']) ? (int)$data['id_produit'] : 0;
$quantite  = isset($data['quantite'])   ? (int)$data['quantite']   : 0;

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Action : supprimer un produit
if ($action === 'remove') {
    if (isset($_SESSION['panier'][$idProduit])) {
        unset($_SESSION['panier'][$idProduit]);
        echo json_encode(['success' => true, 'message' => 'Produit supprimé.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Produit introuvable dans le panier.']);
    }
    exit();
}

// Action : modifier la quantité
if ($action === 'update') {
    if ($quantite <= 0) {
        unset($_SESSION['panier'][$idProduit]);
        echo json_encode(['success' => true, 'message' => 'Produit supprimé.']);
        exit();
    }

    $produit = retrieveProductById($pdo, $idProduit);
    if (empty($produit)) {
        echo json_encode(['success' => false, 'message' => 'Produit introuvable.']);
        exit();
    }

    if ($quantite > $produit['stock']) {
        $quantite = $produit['stock'];
    }

    $_SESSION['panier'][$idProduit]['quantite'] = $quantite;
    echo json_encode(['success' => true, 'message' => 'Quantité mise à jour.']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Action inconnue.']);
