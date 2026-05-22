-- =============================================
-- BASE DE DONNÉES : Cataleya
-- Boutique de cosmétiques pour bébés
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------
-- Table : categorie
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `categorie` (
    `id_categorie`  INT(11)      NOT NULL AUTO_INCREMENT,
    `nom_cat`       VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- Table : produits
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `produits` (
    `id_produit`          INT(11)        NOT NULL AUTO_INCREMENT,
    `nom_commercial`      VARCHAR(100)   NOT NULL,
    `description_courte`  VARCHAR(255)   NOT NULL,
    `description_longue`  TEXT           NOT NULL,
    `prix_htva`           DECIMAL(10,2)  NOT NULL,
    `date_enregistrement` TIMESTAMP      NOT NULL DEFAULT current_timestamp(),
    `disponibilite`       TINYINT(1)     NOT NULL DEFAULT 1,
    `stock`               INT(11)        NOT NULL DEFAULT 0,
    `priorite_vente`      INT(11)        NOT NULL DEFAULT 0,
    `image_principale`    VARCHAR(255)   NOT NULL,
    PRIMARY KEY (`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- Table : categorie_produit (liaison)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `categorie_produit` (
    `id_produit`   INT(11) NOT NULL,
    `id_categorie` INT(11) NOT NULL,
    PRIMARY KEY (`id_produit`, `id_categorie`),
    FOREIGN KEY (`id_produit`)   REFERENCES `produits`(`id_produit`),
    FOREIGN KEY (`id_categorie`) REFERENCES `categorie`(`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- Table : client
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `client` (
    `id_client`    INT(11)      NOT NULL AUTO_INCREMENT,
    `email`        VARCHAR(255) NOT NULL UNIQUE,
    `nom`          VARCHAR(100) NOT NULL,
    `prenom`       VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- Table : commande
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `commande` (
    `id_commande`      INT(11)       NOT NULL AUTO_INCREMENT,
    `id_client`        INT(11)       NOT NULL,
    `date_commande`    TIMESTAMP     NOT NULL DEFAULT current_timestamp(),
    `total_htva`       DECIMAL(10,2) NOT NULL,
    `total_ttc`        DECIMAL(10,2) NOT NULL,
    `rue`              VARCHAR(255)  NOT NULL,
    `code_postal`      VARCHAR(20)   NOT NULL,
    `ville`            VARCHAR(100)  NOT NULL,
    `pays`             VARCHAR(100)  NOT NULL,
    `email_contact`    VARCHAR(255)  NOT NULL,
    PRIMARY KEY (`id_commande`),
    FOREIGN KEY (`id_client`) REFERENCES `client`(`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- Table : ligne_commande
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `ligne_commande` (
    `id_ligne_commande` INT(11)       NOT NULL AUTO_INCREMENT,
    `id_commande`       INT(11)       NOT NULL,
    `id_produit`        INT(11)       NOT NULL,
    `quantite`          INT(11)       NOT NULL,
    `prix_unitaire_htva` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`id_ligne_commande`),
    FOREIGN KEY (`id_commande`) REFERENCES `commande`(`id_commande`),
    FOREIGN KEY (`id_produit`)  REFERENCES `produits`(`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- DONNÉES : catégories
-- =============================================
INSERT INTO `categorie` (`nom_cat`) VALUES
('Bain'),
('Hydratation'),
('Protection');

-- =============================================
-- DONNÉES : produits
-- =============================================
INSERT INTO `produits` (`nom_commercial`, `description_courte`, `description_longue`, `prix_htva`, `disponibilite`, `stock`, `priorite_vente`, `image_principale`) VALUES
('Gel lavant doux bébé',       'Nettoie la peau sensible des bébés sans savon.',       'Gel lavant doux pour le corps et les cheveux des bébés. Sa formule délicate respecte la peau fragile et aide à préserver l\'hydratation naturelle.',                              7.00,  1, 10, 1, 'gel_lavant.jpg'),
('Shampooing démêlant enfant', 'Démêle les cheveux sans tirer.',                       'Fini les pleurs à la sortie du bain ! Ce shampooing doux démêle parfaitement les cheveux des enfants sans piquer les yeux.',                                                        7.50,  1, 15, 2, 'shampooing_bebe.jpg'),
('Crème hydratante bébé',      'Hydrate et protège la peau fragile.',                  'Une crème douce et nourrissante pour protéger la peau de votre bébé au quotidien contre le dessèchement.',                                                                           8.90,  1,  8, 3, 'creme_bebe.jpg'),
('Liniment oléo-calcaire bébé','Nettoie et protège la peau lors du change.',           'Soin traditionnel pour le change. Il nettoie en douceur, prévient les rougeurs et apaise l\'épiderme fessier.',                                                                     5.90,  1, 20, 4, 'liniment_bebe.jpg'),
('Baume lèvres enfant',        'Protège les lèvres du froid et du vent.',              'Un baume ultra-nourrissant pour réparer et protéger les petites lèvres gercées par les intempéries.',                                                                                3.90,  1, 25, 5, 'baume_levres.jpg'),
('Crème solaire bébé SPF50',   'Protection solaire très haute pour bébé.',             'Protection optimale contre les UVA et UVB, spécialement formulée et testée pour la peau ultra-sensible des tout-petits.',                                                            12.50, 1,  5, 6, 'creme_solaire_bebe.jpg');

-- =============================================
-- DONNÉES : liaisons produits / catégories
-- =============================================
INSERT INTO `categorie_produit` (`id_produit`, `id_categorie`) VALUES
(1, 1), -- Gel lavant       -> Bain
(2, 1), -- Shampooing        -> Bain
(3, 2), -- Crème hydratante -> Hydratation
(4, 2), -- Liniment          -> Hydratation
(5, 3), -- Baume lèvres     -> Protection
(6, 3); -- Crème solaire    -> Protection

SET FOREIGN_KEY_CHECKS = 1;
