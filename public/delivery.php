<?php
session_start();
require_once 'connexion.php';
require_once 'functions.php';

// Si le panier est vide, rediriger vers le panier
$panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : [];
if (empty($panier)) {
    header('Location: basket.php');
    exit();
}

// Calcul des totaux
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

// Erreurs de validation
$erreurs = [];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom        = trim($_POST['nom']        ?? '');
    $prenom     = trim($_POST['prenom']     ?? '');
    $email      = trim($_POST['email']      ?? '');
    $rue        = trim($_POST['rue']        ?? '');
    $codePostal = trim($_POST['code_postal'] ?? '');
    $ville      = trim($_POST['ville']      ?? '');
    $pays       = trim($_POST['pays']       ?? '');

    // Validation
    if (empty($nom))        $erreurs[] = 'Le nom est obligatoire.';
    if (empty($prenom))     $erreurs[] = 'Le prénom est obligatoire.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
                            $erreurs[] = 'L\'email est invalide.';
    if (empty($rue))        $erreurs[] = 'La rue est obligatoire.';
    if (empty($codePostal)) $erreurs[] = 'Le code postal est obligatoire.';
    if (empty($ville))      $erreurs[] = 'La ville est obligatoire.';
    if (empty($pays))       $erreurs[] = 'Le pays est obligatoire.';

    if (empty($erreurs)) {
        try {
            $pdo->beginTransaction();

            // Créer ou récupérer le client
            $stmt = $pdo->prepare("SELECT id_client FROM client WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $client = $stmt->fetch();

            if ($client) {
                $idClient = $client['id_client'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO client (email, nom, prenom) VALUES (:email, :nom, :prenom)");
                $stmt->execute([':email' => $email, ':nom' => $nom, ':prenom' => $prenom]);
                $idClient = $pdo->lastInsertId();
            }

            // Créer la commande
            $stmt = $pdo->prepare("
                INSERT INTO commande (id_client, total_htva, total_ttc, rue, code_postal, ville, pays, email_contact)
                VALUES (:id_client, :total_htva, :total_ttc, :rue, :code_postal, :ville, :pays, :email_contact)
            ");
            $stmt->execute([
                ':id_client'     => $idClient,
                ':total_htva'    => $totalHtva,
                ':total_ttc'     => $totalTvac,
                ':rue'           => $rue,
                ':code_postal'   => $codePostal,
                ':ville'         => $ville,
                ':pays'          => $pays,
                ':email_contact' => $email,
            ]);
            $idCommande = $pdo->lastInsertId();

            // Insérer les lignes de commande
            foreach ($produitsPanier as $ligne) {
                $stmt = $pdo->prepare("
                    INSERT INTO ligne_commande (id_commande, id_produit, quantite, prix_unitaire_htva)
                    VALUES (:id_commande, :id_produit, :quantite, :prix_unitaire_htva)
                ");
                $stmt->execute([
                    ':id_commande'       => $idCommande,
                    ':id_produit'        => $ligne['produit']['id_produit'],
                    ':quantite'          => $ligne['quantite'],
                    ':prix_unitaire_htva' => $ligne['produit']['prix_htva'],
                ]);
            }

            $pdo->commit();

            // Vider le panier
            $_SESSION['panier'] = [];

            // Rediriger vers la confirmation
            header('Location: confirmation.php?id=' . $idCommande);
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            $erreurs[] = 'Une erreur est survenue lors de la commande. Veuillez réessayer.';
        }
    }
}
?>

<?php include 'header.php'; ?>

<h2 class="section-title">Informations de livraison</h2>

<!-- Récapitulatif du panier -->
<section class="basket">
    <h3 style="margin-bottom:15px; color:#2f6f7a;">Récapitulatif de votre commande</h3>
    <table class="basket-table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produitsPanier as $ligne): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ligne['produit']['nom_commercial']); ?></td>
                    <td style="text-align:center;"><?php echo $ligne['quantite']; ?></td>
                    <td><?php echo number_format($ligne['prix_total'], 2, ',', ' '); ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="basket-totals">
        <p><strong>Total HTVA :</strong> <?php echo number_format($totalHtva, 2, ',', ' '); ?> €</p>
        <p><strong>Total TVAC :</strong> <?php echo number_format($totalTvac, 2, ',', ' '); ?> €</p>
    </div>
</section>

<!-- Formulaire de livraison -->
<section class="delivery-form">
    <h3 style="margin-bottom:20px; color:#2f6f7a;">Adresse de livraison</h3>

    <?php if (!empty($erreurs)): ?>
        <div class="erreurs">
            <?php foreach ($erreurs as $erreur): ?>
                <p>❌ <?php echo htmlspecialchars($erreur); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="delivery.php">
        <div class="form-row">
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="rue">Rue *</label>
            <input type="text" id="rue" name="rue" value="<?php echo htmlspecialchars($_POST['rue'] ?? ''); ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="code_postal">Code postal *</label>
                <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($_POST['code_postal'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="ville">Ville *</label>
                <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($_POST['ville'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="pays">Pays *</label>
            <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($_POST['pays'] ?? 'Belgique'); ?>" required>
        </div>

        <button type="submit" class="btn btn-commander">✅ Confirmer la commande</button>
    </form>
</section>

<?php include 'footer.php'; ?>
