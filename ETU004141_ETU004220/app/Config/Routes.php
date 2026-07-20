<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->get('admin/login', 'AuthOperateur::afficherFormulaireConnexion');
$routes->post('admin/login', 'AuthOperateur::connexion');
$routes->get('admin/deconnexion', 'AuthOperateur::deconnexion');

$routes->group('admin', ['filter' => 'authOperateur'], static function ($routes) {
    $routes->get('prefixes', 'Prefixes::listerPrefixes');
    $routes->get('prefixes/nouveau', 'Prefixes::nouveauPrefixe');
    $routes->post('prefixes', 'Prefixes::creerPrefixe');
    $routes->get('prefixes/(:num)/modifier', 'Prefixes::modifierPrefixe/$1');
    $routes->post('prefixes/(:num)', 'Prefixes::enregistrerModificationPrefixe/$1');
    $routes->post('prefixes/(:num)/activer-desactiver', 'Prefixes::activerDesactiverPrefixe/$1');

    $routes->get('types-operations', 'TypesOperations::listerTypesOperation');
    $routes->get('types-operations/(:num)/modifier', 'TypesOperations::modifierTypeOperation/$1');
    $routes->post('types-operations/(:num)', 'TypesOperations::enregistrerModificationTypeOperation/$1');

    $routes->get('baremes', 'Baremes::listerBaremes');
    $routes->get('baremes/nouveau', 'Baremes::nouveauBareme');
    $routes->post('baremes', 'Baremes::creerBareme');
    $routes->post('baremes/(:num)/desactiver', 'Baremes::desactiverBareme/$1');

    $routes->get('gains', 'DashboardOperateur::afficherGains');
    $routes->get('comptes-clients', 'DashboardOperateur::afficherComptesClients');
});

