<?php

namespace Pumukit\SchemaBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Pumukit\SchemaBundle\Document\Broadcast;

class BroadcastRepositoryTest extends WebTestCase
{
    private $dm;
    private $repo;

    public function setUp()
    {
        $options = array('environment' => 'test');
        $kernel = static::createKernel($options);
        $kernel->boot();
        $this->dm = $kernel->getContainer()
        ->get('doctrine_mongodb')->getManager();
        $this->repo = $this->dm
        ->getRepository('PumukitSchemaBundle:Broadcast');

        $this->dm->getDocumentCollection('PumukitSchemaBundle:MultimediaObject')
        ->remove(array());
        $this->dm->getDocumentCollection('PumukitSchemaBundle:Broadcast')
        ->remove(array());
        $this->dm->flush();
    }

    public function testRepositoryEmpty()
    {
        $this->assertEquals(0, count($this->repo->findAll()));
    }

    public function testRepository()
    {
        $broadcastPrivate = $this->createBroadcast(Broadcast::BROADCAST_TYPE_PRI);
        $this->assertEquals(1, count($this->repo->findAll()));

        $broadcastPublic = $this->createBroadcast(Broadcast::BROADCAST_TYPE_PUB);
        $this->assertEquals(2, count($this->repo->findAll()));
    }

    public function testFindDistinctIdsByBroadcastTypeId()
    {
        $private1 = $this->createBroadcast(Broadcast::BROADCAST_TYPE_PRI);
        $public1 = $this->createBroadcast(Broadcast::BROADCAST_TYPE_PUB);
        $public2 = $this->createBroadcast(Broadcast::BROADCAST_TYPE_PUB);
        $private2 = $this->createBroadcast(Broadcast::BROADCAST_TYPE_PRI);
        $corporative1 = $this->createBroadcast(Broadcast::BROADCAST_TYPE_COR);

        $privates = $this->repo->findDistinctIdsByBroadcastTypeId(Broadcast::BROADCAST_TYPE_PRI)->toArray();

        $this->assertTrue(in_array($private1->getId(), $privates));
        $this->assertTrue(in_array($private2->getId(), $privates));
        $this->assertFalse(in_array($public1->getId(), $privates));
        $this->assertFalse(in_array($public2->getId(), $privates));
        $this->assertFalse(in_array($corporative1->getId(), $privates));

        $publics = $this->repo->findDistinctIdsByBroadcastTypeId(Broadcast::BROADCAST_TYPE_PUB)->toArray();

        $this->assertFalse(in_array($private1->getId(), $publics));
        $this->assertFalse(in_array($private2->getId(), $publics));
        $this->assertTrue(in_array($public1->getId(), $publics));
        $this->assertTrue(in_array($public2->getId(), $publics));
        $this->assertFalse(in_array($corporative1->getId(), $publics));

        $corporatives = $this->repo->findDistinctIdsByBroadcastTypeId(Broadcast::BROADCAST_TYPE_COR)->toArray();

        $this->assertFalse(in_array($private1->getId(), $corporatives));
        $this->assertFalse(in_array($private2->getId(), $corporatives));
        $this->assertFalse(in_array($public1->getId(), $corporatives));
        $this->assertFalse(in_array($public2->getId(), $corporatives));
        $this->assertTrue(in_array($corporative1->getId(), $corporatives));
    }

    private function createBroadcast($broadcastTypeId)
    {
        $locale = 'en';
        $name = ucfirst($broadcastTypeId);
        $passwd = 'password';
        $defaultSel = $broadcastTypeId == Broadcast::BROADCAST_TYPE_PRI;
        $description = ucfirst($broadcastTypeId).' broadcast';

        $broadcast = new Broadcast();
        $broadcast->setLocale($locale);
        $broadcast->setName($name);
        $broadcast->setBroadcastTypeId($broadcastTypeId);
        $broadcast->setPasswd($passwd);
        $broadcast->setDefaultSel($defaultSel);
        $broadcast->setDescription($description);

        $this->dm->persist($broadcast);
        $this->dm->flush();

        return $broadcast;
    }
}
