<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
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