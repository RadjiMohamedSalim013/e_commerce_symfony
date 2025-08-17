<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/order')]
class OrderController extends AbstractController
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, SecurityBundleSecurity $security)
    {
        $this->em = $em;
        $this->security = $security;
    }
#[Route('/checkout', name: 'order_checkout')]
public function checkout(Request $request, ProductRepository $productRepository): Response
{
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('login');
    }

    // Récupération du panier depuis la session
    $cart = $request->getSession()->get('cart', []);

    if (empty($cart)) {
        $this->addFlash('warning', 'Votre panier est vide.');
        return $this->redirectToRoute('product_list');
    }

    // Création de la commande
    $order = new Order();
    $order->setUser($user);
    $order->setStatus('en attente');
    $order->setCreatedAt(new \DateTimeImmutable());

    $total = 0;

    foreach ($cart as $productId => $quantity) {
        $product = $productRepository->find($productId);
        if (!$product) continue;

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity($quantity);
        $orderItem->setPrice($product->getPrice());
        $orderItem->setOrderRef($order);

        $order->addOrderItem($orderItem);
        $total += $product->getPrice() * $quantity;

        $this->em->persist($orderItem);
    }

    // ⚠️ IMPORTANT : assigner le montant total à la commande
    $order->setTotalAmount($total);

    $this->em->persist($order);
    $this->em->flush();

    // Vider le panier
    $request->getSession()->set('cart', []);

    $this->addFlash('success', "Commande passée avec succès ! Total : {$total} €");

    return $this->redirectToRoute('order_success', ['id' => $order->getId()]);
}


    #[Route('/success/{id}', name: 'order_success')]
    public function success(Order $order): Response
    {
        return $this->render('order/success.html.twig', [
            'order' => $order,
        ]);
    }
}
