<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Login::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/authenticate', 'Login::authenticate');
$routes->get('/logout', 'Login::logout');
$routes->get('/client', 'Client::index');
$routes->get('/client/depot', 'Client::depot');
$routes->post('/client/depot/store', 'Client::depotStore');
$routes->get('/client/retrait', 'Client::retrait');
$routes->post('/client/retrait/store', 'Client::retraitStore');
$routes->get('/client/transfert', 'Client::transfert');
$routes->post('/client/transfert/store', 'Client::transfertStore');
$routes->get('/client/historique', 'Client::historique');
$routes->get('/operateur', 'Operateur::index');
$routes->get('/operateur/prefixes', 'Operateur::prefixes');
$routes->get('/operateur/prefix/create', 'Operateur::prefixCreate');
$routes->post('/operateur/prefix/store', 'Operateur::prefixStore');
$routes->get('/operateur/prefix/toggle/(:num)', 'Operateur::prefixToggle/$1');
$routes->get('/operateur/prefix/delete/(:num)', 'Operateur::prefixDelete/$1');
$routes->get('/operateur/types', 'Operateur::types');
$routes->get('/operateur/type/create', 'Operateur::typeCreate');
$routes->post('/operateur/type/store', 'Operateur::typeStore');
$routes->get('/operateur/type/delete/(:num)', 'Operateur::typeDelete/$1');
$routes->get('/operateur/bareme/create/(:num)', 'Operateur::baremeCreate/$1');
$routes->post('/operateur/bareme/store', 'Operateur::baremeStore');
$routes->get('/operateur/bareme/delete/(:num)', 'Operateur::baremeDelete/$1');
$routes->get('/operateur/situation', 'Operateur::situation');
$routes->get('/operateur/transactions', 'Operateur::transactions');
$routes->get('/operateur/comptes', 'Operateur::comptes');
