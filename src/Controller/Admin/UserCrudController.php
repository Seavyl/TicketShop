<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            EmailField::new('email'),
            TextField::new('address'),
            ChoiceField::new('role','choice user or admin')
                ->setChoices([
                    'user'=> 'user',
                    'admin'=> 'admin'
                ])
        ];

    

        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            // Ajoutez les champs conditionnels au tableau $fields
            $fields[] = TextField::new('plainPassword', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->setHelp('Laissez vide pour ne pas changer.')
                ->onlyOnForms();

            

        return $fields; // Retournez le tableau complet des champs
    }
}
}