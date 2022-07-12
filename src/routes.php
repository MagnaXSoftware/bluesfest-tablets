<?php

/** @var \Slim\App $app
 * @noinspection PhpRedundantVariableDocTypeInspection
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */

use App\Controllers\AreaController;
use App\Controllers\FAQController;
use App\Controllers\StatusController;

$app->get('/', [StatusController::class, 'index'])->setName('status:index');
$app->map(['GET', 'POST'], '/update[/{id}]', [StatusController::class, 'add'])->setName('status:add');

$app->get('/areas', [AreaController::class, 'index'])->setName('area:index');
$app->map(['GET', 'POST'], '/area/create', [AreaController::class, 'create'])->setName('area:create');
$app->get('/area/{id}', [AreaController::class, 'view'])->setName('area:view');
$app->map(['GET', 'POST'], '/area/{id}/update', [AreaController::class, 'update'])->setName('area:update');

$app->get('/faq', [FAQController::class, 'faq'])->setName('faq');