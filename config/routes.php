<?php
/**
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {

    $routes->setRouteClass(DashedRoute::class);

$routes->scope('/', function (RouteBuilder $builder): void {

    // Home
    $builder->connect('/', ['controller' => 'Users', 'action' => 'login']);

    $builder->connect('/register', ['controller' => 'Users', 'action' => 'register']);

    $builder->connect('/dashboard', ['controller' => 'Dashboard', 'action' => 'index']);

    $builder->connect('/pendaftaran', ['controller' => 'Pendaftarans', 'action' => 'form']);

    $builder->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);

    $builder->connect('/reset-password/*', ['controller' => 'Users', 'action' => 'resetPassword']);

    $builder->connect('/tes-online', ['controller' => 'OnlineTests', 'action' => 'index']);

    $builder->connect('/daftar-ulang', ['controller' => 'DaftarUlangs', 'action' => 'form']);

    $builder->connect('/daftar-ulang/draft/{type}', ['controller' => 'DaftarUlangs', 'action' => 'downloadDraft'])->setPass(['type']);

    $builder->connect('/onboarding', ['controller' => 'Onboarding', 'action' => 'index']);


    // Biarkan lainnya pakai fallback standar
    $builder->fallbacks();
});

$routes->prefix('Admin', ['path' => '/rx78gpo1p6'], function (RouteBuilder $builder): void {

    $builder->connect('/login', ['controller' => 'Auth', 'action' => 'login']);
    $builder->connect('/logout', ['controller' => 'Auth', 'action' => 'logout']);

    $builder->connect('/dashboard', ['controller' => 'Dashboard', 'action' => 'index']);
    $builder->connect('/', ['controller' => 'Dashboard', 'action' => 'index']);

    // Pendaftaran (CRUD)
    $builder->connect('/pendaftarans', ['controller' => 'Pendaftarans', 'action' => 'index']);

    // Pengumuman (CRUD)
    $builder->connect('/pengumuman', ['controller' => 'Pengumuman', 'action' => 'index']);
    $builder->connect('/pengumuman/add', ['controller' => 'Pengumuman', 'action' => 'add']);
    $builder->connect('/pengumuman/edit/{id}', ['controller' => 'Pengumuman', 'action' => 'edit'])->setPass(['id']);
    $builder->connect('/pengumuman/delete/{id}', ['controller' => 'Pengumuman', 'action' => 'delete'])->setPass(['id']);

    // Onboarding Settings
    $builder->connect('/onboarding-settings', ['controller' => 'OnboardingSettings', 'action' => 'index']);

    $builder->connect('/mailing', ['controller' => 'Mailing', 'action' => 'index']);
    $builder->connect('/mailing/add', ['controller' => 'Mailing', 'action' => 'add']);
    $builder->connect('/mailing/view/*', ['controller' => 'Mailing', 'action' => 'view']);
    $builder->connect('/mailing/preview-user', ['controller' => 'Mailing', 'action' => 'previewUser']);

    // Tes Online (list user test + edit)
    $builder->connect('/online-tests', ['controller' => 'OnlineTests', 'action' => 'index']);
    $builder->connect('/online-tests/edit/{id}', ['controller' => 'OnlineTests', 'action' => 'edit'])->setPass(['id']);
    $builder->connect('/users', ['controller' => 'Users', 'action' => 'index']);
    $builder->connect('/users/view/{id}', ['controller' => 'Users', 'action' => 'view'])->setPass(['id']);
    $builder->connect('/users/toggle/{id}', ['controller' => 'Users', 'action' => 'toggle'])->setPass(['id']);
    $builder->connect('/users/delete/{id}', ['controller' => 'Users', 'action' => 'delete'])->setPass(['id']);
    $builder->connect('/draft-daftar-ulang', [
  'controller' => 'AdminFiles',
  'action' => 'daftarUlangDrafts',
]);
$builder->connect('/draft-daftar-ulang/download/*', [
  'controller' => 'AdminFiles',
  'action' => 'download',
]);


    $builder->fallbacks();
});

    /*
     * If you need a different set of middleware or none at all,
     * open new scope and define routes there.
     *
     * ```
     * $routes->scope('/api', function (RouteBuilder $builder): void {
     *     // No $builder->applyMiddleware() here.
     *
     *     // Parse specified extensions from URLs
     *     // $builder->setExtensions(['json', 'xml']);
     *
     *     // Connect API actions here.
     * });
     * ```
     */
};
