<?php

/** @var \Slim\App $app
 * @noinspection PhpRedundantVariableDocTypeInspection
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */

use App\Controllers\AreaController;
use App\Controllers\FAQController;
use App\Controllers\StatusController;
use \App\Controllers\TabletController;

$app->get('/', [StatusController::class, 'index'])->setName('status:index');
$app->map(['GET', 'POST'], '/update[/{id}]', [StatusController::class, 'add'])->setName('status:update');
$app->get('/api/update/partial/tablets', [StatusController::class, 'add_partial_tablets'])->setName('status:partial_tablets');

$app->get('/areas', [AreaController::class, 'index'])->setName('area:index');
$app->map(['GET', 'POST'], '/area/create', [AreaController::class, 'create'])->setName('area:create');
$app->get('/area/{id}', [AreaController::class, 'view'])->setName('area:view');
$app->map(['GET', 'POST'], '/area/{id}/update', [AreaController::class, 'update'])->setName('area:update');

$app->get('/tablets', [TabletController::class, 'index'])->setName('tablet:index');
$app->map(['GET', 'POST'], '/tablet/create', [TabletController::class, 'create'])->setName('tablet:create');
$app->get('/tablet/{id}', [TabletController::class, 'view'])->setName('tablet:view');
$app->map(['GET', 'POST'], '/tablet/{id}/update', [TabletController::class, 'update'])->setName('tablet:update');

$app->get('/faq', [FAQController::class, 'faq'])->setName('faq');