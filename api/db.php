<?php
// Connexion a la base de données

// Si pas de connexion on lance l'installateur
if (!(isset($GLOBALS['db_server']) and $GLOBALS['db_user'] and $GLOBALS['db'])) {
    include_once("install.php");
    return;
}

// Connexion
$GLOBALS['connect'] = new mysqli($GLOBALS['db_server'], $GLOBALS['db_user'], $GLOBALS['db_pwd'], $GLOBALS['db']);

// Si pas de connexion on affiche pour google une indisponibilité
if ($GLOBALS['connect']->connect_errno) {
    header($_SERVER['SERVER_PROTOCOL']." 503 Service Unavailable");
    exit($GLOBALS['connect']->connect_error);
}

// Pour un bon encodage dans les sorties de la page
if ($GLOBALS['db_charset']) {
    $GLOBALS['connect']->query("SET NAMES '".$GLOBALS['db_charset']."'");
}
