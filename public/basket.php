<?php
session_start();
require_once 'connexion.php';
require_once 'functions.php';

$panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : [];

$produitsPanier = [];
$totalHtva      = 0;
$totalArticles  = 0;

foreach ($panier as $idProduit => $item) {
    $produit = retrieveProductById($pdo, $idProduit);
    if (!empty($produit)) {
        $quantite  = $item['quantite'];
        $prixTotal = $produit['prix_htva'] * $quantite;

        $produitsPanier[] = [
            'produit'    => $produit,
            'quantite'   => $quantite,
            'prix_total' => $prixTotal,
        ];

        $totalHtva     += $prixTotal;
        $totalArticles += $quantite;
    }
}

$totalTvac = $totalHtva * TVA;
?>

<?php include 'header.php'; ?>

<h2 class="section-title">Mon panier</h2>

<section class="basket" id="basket-section">

    <?php if (empty($produitsPanier)): ?>
        <p class="panier-vide">Votre panier est vide. <a href="products.php">Voir les produits</a></p>

    <?php else: ?>
        <table class="basket-table">
            <thead>
                <tr>
                    <th style="width:100px;">Image</th>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="basket-body">
                <?php foreach ($produitsPanier as $ligne): ?>
                    <tr id="row-<?php echo $ligne['produit']['id_produit']; ?>">
                        <td>
                            <img src="images/<?php echo htmlspecialchars($ligne['produit']['image_principale']); ?>"
                                 alt="<?php echo htmlspecialchars($ligne['produit']['nom_commercial']); ?>"
                                 style="width:80px; height:80px; object-fit:contain;">
                        </td>
                        <td style="min-width:180px;"><?php echo htmlspecialchars($ligne['produit']['nom_commercial']); ?></td>
                        <td><?php echo number_format($ligne['produit']['prix_htva'], 2, ',', ' '); ?> €</td>
                        <td>
                            <select class="qty-select" data-id="<?php echo $ligne['produit']['id_produit']; ?>">
                                <?php for ($i = 1; $i <= min(10, $ligne['produit']['stock']); $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i == $ligne['quantite'] ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </td>
                        <td class="ligne-total-<?php echo $ligne['produit']['id_produit']; ?>">
                            <?php echo number_format($ligne['prix_total'], 2, ',', ' '); ?> €
                        </td>
                        <td>
                            <button class="btn btn-supprimer"
                                    data-id="<?php echo $ligne['produit']['id_produit']; ?>"
                                    style="background:#e74c3c;">
                                🗑️ Supprimer
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="basket-totals" id="basket-totals">
            <p><strong>Nombre d'articles :</strong> <span id="total-articles"><?php echo $totalArticles; ?></span></p>
            <p><strong>Total HTVA :</strong> <span id="total-htva"><?php echo number_format($totalHtva, 2, ',', ' '); ?></span> €</p>
            <p><strong>Total TVAC :</strong> <span id="total-tvac"><?php echo number_format($totalTvac, 2, ',', ' '); ?></span> €</p>
        </div>

    <?php endif; ?>

</section>

<script>
// Modifier la quantité
document.querySelectorAll('.qty-select').forEach(function(select) {
    select.addEventListener('change', function() {
        var idProduit = this.getAttribute('data-id');
        var quantite  = parseInt(this.value);

        fetch('update_basket.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'update', id_produit: parseInt(idProduit), quantite: quantite })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                location.reload();
            }
        });
    });
});

// Supprimer un produit
document.querySelectorAll('.btn-supprimer').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var idProduit = this.getAttribute('data-id');

        fetch('update_basket.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'remove', id_produit: parseInt(idProduit) })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                location.reload();
            }
        });
    });
});
</script>

        <div style="text-align:right; margin-top:20px;">
            <a href="delivery.php" class="btn btn-commander">✅ Passer la commande</a>
        </div>

<?php include 'footer.php'; ?>
