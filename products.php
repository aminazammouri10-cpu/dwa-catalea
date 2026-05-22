<?php
require_once __DIR__ . '/../connexion.php';
require_once __DIR__ . '/../src/functions.php';

$categories = retrieveCategories($pdo);
$selectedCategories = isset($_GET['categories']) && is_array($_GET['categories']) ? $_GET['categories'] : [];
$selectedOrder = isset($_GET['tri']) ? $_GET['tri'] : '';
$produits = retrieveBuyableProducts($pdo, $selectedCategories, $selectedOrder);
?>
<?php include __DIR__ . '/../templates/header.php'; ?>
<h2 class="section-title">Nos produits</h2>
<section class="filter-box">
    <form method="GET" action="/public/products.php">
        <div class="filter-row">
            <div class="filter-group">
                <label>Catégories :</label>
                <?php foreach ($categories as $categorie): ?>
                    <input type="checkbox" id="cat_<?php echo $categorie['id_categorie']; ?>" name="categories[]" value="<?php echo $categorie['id_categorie']; ?>" <?php echo in_array($categorie['id_categorie'], $selectedCategories) ? 'checked' : ''; ?>>
                    <label for="cat_<?php echo $categorie['id_categorie']; ?>"><?php echo htmlspecialchars($categorie['nom_cat']); ?></label>
                <?php endforeach; ?>
            </div>
            <div class="filter-group">
                <label for="tri">Trier par :</label>
                <select id="tri" name="tri">
                    <option value="">Par défaut</option>
                    <option value="price_asc" <?php echo $selectedOrder === 'price_asc' ? 'selected' : ''; ?>>Prix croissant</option>
                    <option value="price_desc" <?php echo $selectedOrder === 'price_desc' ? 'selected' : ''; ?>>Prix décroissant</option>
                </select>
            </div>
            <button type="submit" class="btn">Appliquer</button>
        </div>
    </form>
</section>
<section class="product-grid">
    <?php if (empty($produits)): ?>
        <p>Aucun produit trouvé.</p>
    <?php else: ?>
        <?php foreach ($produits as $produit): ?>
            <?php $prix_tvac = $produit['prix_htva'] * TVA; ?>
            <article class="product-card">
                <img src="/public/images/<?php echo htmlspecialchars($produit['image_principale']); ?>" alt="<?php echo htmlspecialchars($produit['nom_commercial']); ?>">
                <h3><?php echo htmlspecialchars($produit['nom_commercial']); ?></h3>
                <p><?php echo htmlspecialchars($produit['description_courte']); ?></p>
                <p class="price">Prix TVAC : <?php echo number_format($prix_tvac, 2, ',', ' '); ?> €</p>
                <a href="/public/product.php?id=<?php echo $produit['id_produit']; ?>" class="btn">Voir le produit</a>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<?php include __DIR__ . '/../templates/footer.php'; ?>
