<?php
session_start();
require_once 'connexion.php';
require_once 'functions.php';

$idCommande = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idCommande <= 0) {
    header('Location: index.php');
    exit();
}

// Récupère la commande
$stmt = $pdo->prepare("
    SELECT c.*, cl.nom, cl.prenom
    FROM commande c
    INNER JOIN client cl ON c.id_client = cl.id_client
    WHERE c.id_commande = :id
");
$stmt->execute([':id' => $idCommande]);
$commande = $stmt->fetch();

if (empty($commande)) {
    header('Location: index.php');
    exit();
}

// Récupère les lignes de commande
$stmt = $pdo->prepare("
    SELECT lc.*, p.nom_commercial, p.image_principale
    FROM ligne_commande lc
    INNER JOIN produits p ON lc.id_produit = p.id_produit
    WHERE lc.id_commande = :id
");
$stmt->execute([':id' => $idCommande]);
$lignes = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

<h2 class="section-title">Confirmation de commande</h2>

<section class="confirmation">
    <div class="confirmation-header">
        <p>🎉 Merci pour votre commande !</p>
        <p>Votre commande n°<strong><?php echo $idCommande; ?></strong> a bien été enregistrée.</p>
    </div>

    <div class="confirmation-details">
        <h3>Détails de la commande</h3>
        <table class="basket-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ligne['nom_commercial']); ?></td>
                        <td style="text-align:center;"><?php echo $ligne['quantite']; ?></td>
                        <td><?php echo number_format($ligne['prix_unitaire_htva'], 2, ',', ' '); ?> €</td>
                        <td><?php echo number_format($ligne['prix_unitaire_htva'] * $ligne['quantite'], 2, ',', ' '); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="basket-totals">
            <p><strong>Total HTVA :</strong> <?php echo number_format($commande['total_htva'], 2, ',', ' '); ?> €</p>
            <p><strong>Total TVAC :</strong> <?php echo number_format($commande['total_ttc'], 2, ',', ' '); ?> €</p>
        </div>
    </div>

    <div class="confirmation-adresse">
        <h3>Adresse de livraison</h3>
        <p><?php echo htmlspecialchars($commande['nom'] . ' ' . $commande['prenom']); ?></p>
        <p><?php echo htmlspecialchars($commande['rue']); ?></p>
        <p><?php echo htmlspecialchars($commande['code_postal'] . ' ' . $commande['ville']); ?></p>
        <p><?php echo htmlspecialchars($commande['pays']); ?></p>
        <p><?php echo htmlspecialchars($commande['email_contact']); ?></p>
    </div>

    <a href="products.php" class="btn" style="margin-top:20px;">Continuer mes achats</a>
</section>

<?php include 'footer.php'; ?>
