<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Form\AreaType;
use App\Models\Area;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AreaController extends Controller
{

    public function index(ResponseInterface $response, EntityManager $em): ResponseInterface
    {
        $areas = $em->getRepository(Area::class)->findAll();

        return $this->twig->render($response, 'areas/index.html.twig', [
            'title' => 'Areas',
            'areas' => $areas,
        ]);
    }

    public function view(ResponseInterface $response, EntityManager $em, $id): ResponseInterface
    {
        $area = $em->find(Area::class, $id);

        return $this->twig->render($response, 'areas/view.html.twig', [
            'title' => $area->getName(),
            'area' => $area,
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, EntityManager $em): ResponseInterface
    {
        $area = new Area();
        $form = $this->formFactory->create(AreaType::class, $area);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($area);
            $em->flush();

            return $this->redirect($this->urlFor('area:index'));
        }

        return $this->twig->render($response, 'areas/create.html.twig', [
            'title' => 'Create Area ',
            'action' => $this->urlFor('area:create'),
            'form' => $form->createView(),
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, EntityManager $em, $id): ResponseInterface
    {
        $area = $em->find(Area::class, $id);
        $form = $this->formFactory->create(AreaType::class, $area);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($area);
            $em->flush();

            return $this->redirect($this->urlFor('area:index'));
        }

        return $this->twig->render($response, 'areas/update.html.twig', [
            'title' => 'Update Area ' . $area->getName(),
            'action' => $this->urlFor('area:update', ['id' => $area->getId()]),
            'form' => $form->createView(),
        ]);
    }

}