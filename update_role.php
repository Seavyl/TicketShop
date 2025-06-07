<?php

require __DIR__.'/vendor/autoload.php';

$kernel = new App\Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();

$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

$user = $entityManager->getRepository('App\Entity\User')->findOneBy(['email' => 'alice@example.com']);

if ($user) {
    $user->setRoles(['ROLE_ADMIN']);
    $entityManager->persist($user);
    $entityManager->flush();
    echo "Rôle mis à jour avec succès pour alice@example.com\n";
} else {
    echo "Utilisateur non trouvé\n";
} 