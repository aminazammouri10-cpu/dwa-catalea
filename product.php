<?php
session_start();
require_once 'connexion.php';
require_once 'functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produit = retrieveProductById($pdo, $id);

if (empty($produit)) {
    header('Location: products.php');
    exit();
}

$prix_tvac = $produit['prix_htva'] * TVA;
?>

<?php include 'header.php'; ?>

<h2 class="section-title">Fiche produit</h2>

<section class="product-detail">
    <div>
        <img src="images/<?php echo htmlspecialchars($produit['image_principale']); ?>"
             alt="<?php echo htmlspecialchars($produit['nom_commercial']); ?>">
    </div>

    <div>
        <h2><?php echo htmlspecialchars($produit['nom_commercial']); ?></h2>

        <p><strong>Description courte :</strong> <?php echo htmlspecialchars($produit['description_courte']); ?></p>
        <br>
        <p>
            <strong>Description détaillée :</strong><br>
            <?php echo htmlspecialchars($produit['description_longue']); ?>
        </p>
        <br>
        <p><strong>Prix HTVA :</strong> <?php echo number_format($produit['prix_htva'], 2, ',', ' '); ?> €</p>
        <p><strong>Prix TVAC :</strong> <?php echo number_format($prix_tvac, 2, ',', ' '); ?> €</p>

        <?php if (!$produit['disponibilite']): ?>
            <p class="stock out">⚠️ Ce produit n'est pas disponible à la vente.</p>
        <?php elseif ($produit['stock'] <= 0): ?>
            <p class="stock out">❌ Rupture de stock</p>
        <?php else: ?>
            <p class="stock">✅ En stock (<?php echo $produit['stock']; ?> disponibles)</p>

            <div class="add-to-basket">
                <label for="quantite">Quantité :</label>
                <select id="quantite" name="quantite">
                    <?php for ($i = 1; $i <= $produit['stock']; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>

                <button class="btn" id="btn-ajouter"
                        data-id="<?php echo $produit['id_produit']; ?>">
                    🛒 Ajouter au panier
                </button>
            </div>

            <p id="ajax-message" style="display:none; margin-top:10px;"></p>
        <?php endif; ?>
    </div>
</section>

<script>
document.getElementById('btn-ajouter').addEventListener('click', function () {
    const idProduit = this.dataset.id;
    const quantite  = document.getElementById('quantite').value;
    const message   = document.getElementById('ajax-message');

    fetch('add_to_basket.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_produit: parseInt(idProduit), quantite: parseInt(quantite) })
    })
    .then(response => response.json())
    .then(data => {
        message.style.display = 'block';
        if (data.success) {
            message.style.color = 'green';
            message.innerHTML = '✅ ' + data.message
                + ' — <a href="basket.php">Voir mon panier</a>';
        } else {
            message.style.color = 'red';
            message.textContent = '❌ ' + data.message;
        }
    })
    .catch(() => {
        message.style.display = 'block';
        message.style.color   = 'red';
        message.textContent   = '❌ Une erreur est survenue.';
    });
});
</script>

<?php include 'footer.php'; ?>
