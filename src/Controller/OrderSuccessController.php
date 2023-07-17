<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;


class OrderSuccessController extends AbstractController
{
    private $entityManager;
    private Pdf $pdfGenerator;

    public function __construct(EntityManagerInterface $entityManager, Pdf $pdfGenerator)
    {
        $this->entityManager = $entityManager;
        $this->pdfGenerator= $pdfGenerator;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $cart->remove();

        if ($order->getState() == 0) {
            // Modifier le statut isPaid de notre commande en mettant 1
            $order->setState(1);
            $this->entityManager->flush();

            // Gérer les quantités de produits et envoyer un email d'avertissement pour les produits en rupture de stock
            $productsOutOfStock = [];
            foreach ($order->getOrderDetails() as $orderDetail) {
                $product = $orderDetail->getProduit();
                $quantity = $orderDetail->getQuantite();

                if ($product instanceof Product) {
                    $newQuantity = $product->getQuantite() - $quantity;

                    if ($newQuantity < 0) {
                        $newQuantity = 0;
                        $productsOutOfStock[] = $product;
                    }

                    $product->setQuantite($newQuantity);
                    $this->entityManager->persist($product);
                }
            }


            if (!empty($productsOutOfStock)) {
                // Envoyer un email d'avertissement pour les produits en rupture de stock
                $mail = new Mail();
                $content = "Attention, certains produits de votre commande sont en rupture de stock :<br><br>";
                foreach ($productsOutOfStock as $product) {
                    $content .= "- " . $product->getNom() . "<br>";
                }
                $content .= "<br>Veuillez nous contacter pour plus d'informations.";
                $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Produits en rupture de stock dans votre commande', $content);
            }

            // Envoyer un email de confirmation de commande
            $mail = new Mail();
            $content = "Bonjour, " . $order->getUser()->getFirstname() . "<br/>Merci pour votre commande " . $stripeSessionId . ".<br><br> Voici la liste des produits commandés :<br>";
            foreach ($order->getOrderDetails() as $orderDetail) {
                $produit = $orderDetail->getProduit();
                $content .= "- " . $produit . " (x" . $orderDetail->getQuantite() . ")<br> ";
                $content .= '<img src="' . $orderDetail->getIllustration() . '" alt="' . $orderDetail->getProduit() . '"><br>';
            }
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande chez Inforiel a bien été validée !', $content);
        }

        // Afficher quelques informations sur la commande de l'utilisateur
        $cartTotal = $cart->getTotal();
        $cartProducts = $cart->getProducts();

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
            'cartTotal' => $cartTotal,
            'cartProducts' => $cartProducts,
            'cart' => $cart->getFull(),
        ]);
    }

    #[Route('/invoice/{orderId}', name: 'app_factures')]
    public function generateInvoiceAction($orderId): Response
    {
        $entityManager = $this->entityManager;

        // Récupérez les détails de la commande en fonction de l'ID de la commande
        $orderDetails = $entityManager->getRepository(Order::class)->find($orderId);

        // Rendez le template de facture avec les détails de la commande
        $html = $this->renderView('invoice.html.twig', [
            'orderDetails' => $orderDetails,
        ]);

        // Générez le PDF à l'aide de KnpSnappyBundle
        $pdf = $this->pdfGenerator->getOutputFromHtml($html, [
            'enable-local-file-access' => true,
        ]);

        // Retournez la réponse PDF
        $response = new \Symfony\Component\HttpFoundation\Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="invoice.pdf"');

        return $response;
    }

    #[Route('/generate-invoice/{orderId}', name: 'app_generate_invoice')]

    public function generateInvoiceRoute($orderId): Response
    {
        return $this->generateInvoiceAction($orderId);
    }

}
