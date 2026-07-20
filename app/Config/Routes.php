<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/authenticate', 'Login::authenticate');
$routes->get('/logout', 'Login::logout');
$routes->get('/client', 'Client::index');
$routes->get('/operateur', 'Operateur::index');
$routes->get('/operateur/prefixes', 'Operateur::prefixes');
$routes->get('/operateur/prefix/create', 'Operateur::prefixCreate');
$routes->post('/operateur/prefix/store', 'Operateur::prefixStore');
$routes->get('/operateur/prefix/toggle/(:num)', 'Operateur::prefixToggle/$1');
$routes->get('/operateur/prefix/delete/(:num)', 'Operateur::prefixDelete/$1');
