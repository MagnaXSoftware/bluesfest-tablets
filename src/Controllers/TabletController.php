<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Form\TabletType;
use App\Models\Tablet;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TabletController extends Controller
{

    public function index(ResponseInterface $response, EntityManager $em): ResponseInterface
    {
        $tablets = $em->getRepository(Tablet::class)->findAll();

        return $this->twig->render($response, 'tablets/index.html.twig', [
            'title' => 'Tablets',
            'tablets' => $tablets,
        ]);
    }

    public function view(ResponseInterface $response, EntityManager $em, $id): ResponseInterface
    {
        $tablet = $em->find(Tablet::class, $id);

        return $this->twig->render($response, 'tablets/view.html.twig', [
            'title' => $tablet->getCode(),
            'tablet' => $tablet,
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, EntityManager $em): ResponseInterface
    {
        $tablet = new Tablet();
        $form = $this->formFactory->create(TabletType::class, $tablet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tablet);
            $em->flush();

            return $this->redirect($this->urlFor('tablet:index'));
        }

        return $this->twig->render($response, 'tablets/create.html.twig', [
            'title' => 'Create Tablet',
            'action' => $this->urlFor('tablet:create'),
            'form' => $form->createView(),
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, EntityManager $em, $id): ResponseInterface
    {
        $tablet = $em->find(Tablet::class, $id);
        $form = $this->formFactory->create(TabletType::class, $tablet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tablet);
            $em->flush();

            return $this->redirect($this->urlFor('tablet:index'));
        }

        return $this->twig->render($response, 'tablets/update.html.twig', [
            'title' => 'Edit Tablet ' . $tablet->getCode(),
            'action' => $this->urlFor('tablet:update', ['id' => $tablet->getId()]),
            'form' => $form->createView(),
        ]);
    }

}