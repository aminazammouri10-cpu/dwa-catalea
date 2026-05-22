<?php

define('TVA', 1.21);

function retrieveCategories($pdo) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT c.id_categorie, c.nom_cat
        FROM categorie c
        INNER JOIN categorie_produit cp ON c.id_categorie = cp.id_categorie
        INNER JOIN produits p ON cp.id_produit = p.id_produit
        WHERE p.disponibilite = 1
        ORDER BY c.nom_cat ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function retrieveBuyableProducts($pdo, $categories = [], $order = '') {
    $sql = "SELECT DISTINCT p.* FROM produits p";
    if (!empty($categories)) {
        $placeholders = implode(',', array_fill(0, count($categories), '?'));
        $sql .= " INNER JOIN categorie_produit cp ON p.id_produit = cp.id_produit WHERE p.disponibilite = 1 AND cp.id_categorie IN ($placeholders)";
    } else {
        $sql .= " WHERE p.disponibilite = 1";
    }
    if ($order === 'price_asc') $sql .= " ORDER BY p.prix_htva ASC";
    elseif ($order === 'price_desc') $sql .= " ORDER BY p.prix_htva DESC";
    else $sql .= " ORDER BY p.priorite_vente ASC";
    $stmt = $pdo->prepare($sql);
    if (!empty($categories)) $stmt->execute(array_map('intval', $categories));
    else $stmt->execute();
    return $stmt->fetchAll();
}

function retrieveProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id_produit = :id");
    $stmt->execute([':id' => (int)$id]);
    $produit = $stmt->fetch();
    return $produit ? $produit : [];
}

function isProduitDisponible($produit, $quantite) {
    if (empty($produit)) return false;
    if (!$produit['disponibilite']) return false;
    if ($quantite <= 0) return false;
    if ($quantite > $produit['stock']) return false;
    return true;
}
