<?php

namespace Pumukit\WebTVBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerTest extends WebTestCase
{
    private $dm;
    private $router;
    private $factory;
    private $skipTests = false;
/*
    public function __construct()
    {
        $this->client = static::createClient();
        $options = array('environment'=>'test');
        $kernel = static::createKernel($options);
        $kernel->boot();
        $container = $kernel->getContainer();
        $this->dm = $container->get('doctrine_mongodb.odm.document_manager');
        $this->router = $container->get('router');
        $this->factory = $container->get('pumukitschema.factory');
        if (!array_key_exists("PumukitPodcastBundle", $container->getParameter('kernel.bundles'))) {
            $this->skipTests = true;
        }
    }

    public function setUp()
    {
        $this->dm->getDocumentCollection('PumukitSchemaBundle:MultimediaObject')
            ->remove(array());
        $this->dm->getDocumentCollection('PumukitSchemaBundle:Series')
            ->remove(array());
    }
*/
    public function testIndex()
    {
	$client = static::createClient();
        $crawler = $client->request('GET', '/');

	// Asegura que el código de estado es exactamente 200
	$this->assertEquals(Response::HTTP_OK,$client->getResponse()->getStatusCode());

    }


}
