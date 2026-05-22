<?php
// Copiez ce fichier en config.php et remplissez vos identifiants
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    define('DB_HOST',     'localhost');
    define('DB_USER',     'root');
    define('DB_PASSWORD', '');
    define('DB_NAME',     'catalea');
} else {
    define('DB_HOST',     'localhost');
    define('DB_USER',     'votre_user');
    define('DB_PASSWORD', 'votre_password');
    define('DB_NAME',     'votre_db');
}
