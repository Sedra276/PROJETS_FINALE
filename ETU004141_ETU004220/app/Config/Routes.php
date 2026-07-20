<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'client/connexion');


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

$routes->get('client/connexion', 'AuthClient::formulaireConnexion');
$routes->post('client/connexion', 'AuthClient::connexion');
$routes->get('client/deconnexion', 'AuthClient::deconnexion');

$routes->group('client', ['filter' => 'clientAuth'], function ($routes) {
    $routes->get('solde', 'Solde::voirSolde');
    $routes->get('depot', 'Depot::formulaireDepot');
    $routes->post('depot', 'Depot::effectuerDepot');
    $routes->get('retrait', 'Retrait::formulaireRetrait');
    $routes->post('retrait', 'Retrait::effectuerRetrait');
    $routes->get('transfert', 'Transfert::formulaireTransfert');
    $routes->post('transfert', 'Transfert::effectuerTransfert');
    $routes->get('historique', 'Historique::voirHistorique');
});
