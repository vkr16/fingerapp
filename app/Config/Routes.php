<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Main');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('App\Controllers\Pages::errorNotFound');
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Main::login');
$routes->get('/login', 'Main::login');
$routes->post('/login', 'Main::loginProcess');
$routes->get('/register', 'Main::register');
$routes->post('/register', 'Main::registerProcess');
$routes->get('/recovery', 'Main::recovery');
$routes->get('/verify/(:any)/(:any)', 'Main::verify/$1/$2');
$routes->get('/logout', 'Main::logout');

$routes->get('/user/pockets', 'Pocket::index');
$routes->post('/user/pockets/add', 'Pocket::addPocket');
$routes->post('/user/pockets/add-balance', 'Pocket::addBalance');
$routes->get('/user/pockets/list', 'Pocket::listPocket');
$routes->get('/user/pockets/transaction-list/(:any)', 'Pocket::listTransaction/$1');
$routes->post('/user/pockets/make-transaction', 'Pocket::makeTransaction');
$routes->post('/user/pockets/transfer-balance', 'Pocket::transferBalance');
$routes->get('/user/pockets/detail/(:any)', 'Pocket::detailPocket/$1');


$routes->get('/user/budgets', 'Budget::index');
$routes->post('/user/budgets/add', 'Budget::addBudget');
$routes->post('/user/budgets/add-balance', 'Budget::addBalance');
$routes->get('/user/budgets/list', 'Budget::listBudget');
$routes->get('/user/budgets/transaction-list/(:any)', 'Budget::listTransaction/$1');
$routes->post('/user/budgets/transfer-balance', 'Budget::transferBalance');
$routes->get('/user/budgets/detail/(:any)', 'Budget::detailBudget/$1');


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
