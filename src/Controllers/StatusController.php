<?php

namespace App\Controllers;

use App\Form\StatusType;
use App\Models\Area;
use App\Models\Status;
use App\Storage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class StatusController extends Controller
{

    public function index(ResponseInterface $response, Storage $db): ResponseInterface
    {
        $statuses = $db->getLatestStatuses();
        return $this->twig->render($response, 'status/index.html.twig', [
            'title' => 'Tablet Status',
            'statuses' => $statuses,
        ]);
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, Storage $db, $id = null): ResponseInterface
    {
        $status = new Status();
        if (!is_null($id)) {
            $status->setArea($db->getArea($id));
        }
        $form = $this->formFactory->create(StatusType::class, $status, ['area_id' => $id]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $status->setTimestamp(time());
            if ($db->addStatus($status)) {
                return $this->redirect($this->urlFor('status:index'));
            }
        }

        return $this->twig->render($response, 'status/add.html.twig', [
            'title' => 'Add Status Update',
            'action' => $this->urlFor('status:add', (is_null($id)) ? [] : ['id' => $id]),
            'form' => $form->createView(),
        ]);
    }

}