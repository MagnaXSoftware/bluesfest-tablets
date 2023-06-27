<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Form\UpdateType;
use App\Models\Area;
use App\Models\Update;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
use App\Auth\Attributes\HasRole;

#[HasRole('ROLE_UPDATE')]
class StatusController extends Controller
{

    #[HasRole('ROLE_ANON')]
    public function index(ResponseInterface $response, EntityManager $em): ResponseInterface
    {
        $areas = $em->getRepository(Area::class)->findAll();
        return $this->twig->render($response, 'status/index.html.twig', [
            'title' => 'Area Status',
            'areas' => $areas,
        ]);
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, EntityManager $em, $id = null): ResponseInterface
    {
        $update = new Update();
        $update->setArea($id ? $em->find(Area::class, $id) : null);
        $form = $this->formFactory->create(UpdateType::class, $update, ['area_id' => $id]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $update->setCreatedAt(new DateTimeImmutable());

            $em->persist($update);
            $em->flush();

            return $this->redirect($this->urlFor('status:index'));
        }

        return $this->twig->render($response, 'status/add.html.twig', [
            'title' => 'Update Tablet Status',
            'action' => $this->urlFor('status:update', (is_null($id)) ? [] : ['id' => $id]),
            'form' => $form->createView(),
        ]);
    }

    public function add_partial_tablets(ServerRequestInterface $request, ResponseInterface $response, EntityManager $em): ResponseInterface
    {
        $params = $request->getQueryParams();
        if (!array_key_exists('id', $params)) {
            throw new HttpException($request, 'Query parameter "id" is required', 400);
        }
        $id = $params['id'];

        $update = new Update();
        $update->setArea($id ? $em->find(Area::class, $id) : null);
        $form = $this->formFactory->create(UpdateType::class, $update, ['area_id' => $id]);

        return $this->twig->render($response, 'status/add_partial_tablet.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}