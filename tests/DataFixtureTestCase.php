<?php
namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataFixtureTestCase extends WebTestCase
{
    /** @var  Application $application */
    protected static $application;

    /** @var  Client $client */
    protected $client;

    /** @var  ContainerInterface $container */
    protected static $container;

    /** @var  EntityManager $entityManager */
    protected $entityManager;

    /**
    * {@inheritDoc}
    */
    public function setUp()
    {
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load --append --no-interaction');

        $this->client = static::createClient();
        self::$container = $this->client->getContainer();
        $this->entityManager = self::$container->get('doctrine.orm.entity_manager');

        parent::setUp();
    }

    /**
     * @param $command
     * @return int
     * @throws \Exception
     */
    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
        $client = static::createClient();

        self::$application = new Application($client->getKernel());
        self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    protected function tearDown()
    {
        self::runCommand('doctrine:database:drop --force');

        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $params
     * @return array
     */
    public function makeRequest($method, $endpoint, $params = []): array
    {
        $this->client->request($method, $endpoint, $params);

        $json = $this->client->getResponse()->getContent();
        $contentType = $this->client->getResponse()->headers->get('content-type');
        return array($json, $contentType);
    }
}