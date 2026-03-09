<?php

use App\Entity\Categorie;
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Load .env if DATABASE_URL is not set (when running this script directly)
if (!getenv('DATABASE_URL') && file_exists(__DIR__ . '/../.env')) {
	(new Dotenv())->bootEnv(__DIR__ . '/../.env');
}

$kernel = new Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

// Get the entity manager
$em = $container->get('doctrine')->getManager();

$categorie = new Categorie();
$categorie->setNom('Technologie');
$categorie->setDescription('Articles liés à la technologie et l\'innovation.');

$em->persist($categorie);
$em->flush();

echo "Created categorie with id: " . $categorie->getId() . "\n";
