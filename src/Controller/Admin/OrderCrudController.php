<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use function Symfony\Component\Translation\t;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $crudUrlGenerator;
    private  $adminUrlGenerator;

    public  function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this-> adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreapation= Action::new('updatePreapation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreapation');
        $updateDelivery= Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePreapation)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }

    public function updatePreapation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:green;'><strong>La commande ".$order->getReference()." est bien<u> en cours de préparation</u></strong></span>");

        $url = $this-> adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();



        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:orange;'><strong>La commande ".$order->getReference()." est bien<u> en cours de livraison</u></strong></span>");

        $url = $this-> adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        $to_email = $order->getUser()->getEmail();
        $to_name = $order->getUser()->getFirstname();
        $subject = 'Votre commande est en cours de livraison';
        $content = 'Votre commande '.$order->getReference().' est maintenant en cours de livraison. Merci de votre confiance.';

        $mail = new Mail();
        $mail->send($to_email, $to_name, $subject, $content);

        return $this->redirect($url);
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id'=> 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passée le'),
            TextField::new('user.getFullName', 'Nom du client'),
            TextEditorField::new('delivery', 'Adresse de livraison')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    $formattedAddress = nl2br($entity->getDelivery());
                    return $formattedAddress;
                }),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de ports')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits commandés')->hideOnIndex()
        ];
    }

}
