<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalea</title>
    <link rel="stylesheet" href="/public/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-flex">
            <h1 class="logo"><a href="/public/index.php" style="text-decoration:none; color:inherit;">Catalea</a></h1>
            <nav>
                <ul class="nav">
                    <li><a href="/public/index.php">Accueil</a></li>
                    <li><a href="/public/products.php">Produits</a></li>
                    <li><a href="/public/basket.php">🛒 Panier</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
