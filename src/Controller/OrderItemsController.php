<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderItemsController extends AbstractController
{
    #[Route('/order/items', name: 'app_order_items')]
    public function index(): Response
    {
        return $this->render('order_items/index.html.twig', [
            'controller_name' => 'OrderItemsController',
        ]);
    }
}
