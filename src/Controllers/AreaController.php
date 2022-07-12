<?php

namespace App\Controllers;

use App\Form\AreaType;
use App\Models\Area;
use App\Storage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AreaController extends Controller
{

    public function index(ResponseInterface $response, Storage $db): ResponseInterface
    {
        $areas = $db->getAreas();

        return $this->twig->render($response, 'areas/index.html.twig', [
            'title' => 'Areas',
            'areas' => $areas,
        ]);
    }

    public function view(ResponseInterface $response, Storage $db, $id): ResponseInterface
    {
        $area = $db->getAreaWithStatuses($id);

        return $this->twig->render($response, 'areas/view.html.twig', [
            'title' => $area->getName(),
            'area' => $area,
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, Storage $db): ResponseInterface
    {
        $area = new Area();
        $form = $this->formFactory->create(AreaType::class, $area);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($db->addArea($area)) {
                return $this->redirect($this->urlFor('area:index'));
            }
        }

        return $this->twig->render($response, 'areas/create.html.twig', [
            'title' => 'Create Area ',
            'action' => $this->urlFor('area:create'),
            'form' => $form->createView(),
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, Storage $db, $id): ResponseInterface
    {
        $area = $db->getArea($id);
        $form = $this->formFactory->create(AreaType::class, $area);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($db->updateArea($area)) {
                return $this->redirect($this->urlFor('area:index'));
            }
        }

        return $this->twig->render($response, 'areas/update.html.twig', [
            'title' => 'Update Area ' . $area->getName(),
            'action' => $this->urlFor('area:update', ['id' => $area->getId()]),
            'form' => $form->createView(),
        ]);
    }

}