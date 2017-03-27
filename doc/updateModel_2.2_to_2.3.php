#!/usr/bin/env php
<?php
// application.php

set_time_limit(0);

require_once __DIR__.'/../app/autoload.php';

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pumukit\SchemaBundle\Document\EmbeddedBroadcast;
use Pumukit\SchemaBundle\Document\Broadcast;

class UpgradePumukitCommand extends ContainerAwareCommand
{
    private $dm;
    private $typeLoginName = 'Private (LDAP)';

    protected function configure()
    {
        $this
            ->setName('update:model:2.2to2.3')
            ->setDescription('Update the documents (from 2.2) to match the 2.3 version.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initParameters();

        $output->writeln(' ***** Updating PuMuKIT 2.2 to PuMuKIT 2.3 ***** ');

        $output->writeln('Series and MultimediaObject collections updated (keywords)');
        $this->updateKeywords();

        $output->writeln('MultimediaObject collection updated (broadcasts)');
        $this->updateBroadcast();
    }

    private function initParameters()
    {
        $this->dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
    }

    private function updateKeywords()
    {
        try {
            $aSeries = $this->getAllSeries();
            if (count($aSeries) > 0) {
                $this->convertKeywordToKeywords($aSeries);
            }

            $aMultimedia = $this->getAllMultimediaObjects();
            if (count($aSeries) > 0) {
                $this->convertKeywordToKeywords($aMultimedia);
            }
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    private function updateBroadcast()
    {
        try {
            $aMultimediaObjects = $this->getAllMultimediaObjects();
            if (count($aMultimediaObjects) > 0) {
                $this->convertBroadcastToEmbeddedBroadcast($aMultimediaObjects);
            }
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    private function getAllSeries()
    {
        $aSeries = $this->dm->getRepository('PumukitSchemaBundle:Series')->findAll();

        return $aSeries;
    }

    private function getAllMultimediaObjects()
    {
        $aMultimediaObject = $this->dm->getRepository('PumukitSchemaBundle:MultimediaObject')->findAll();

        return $aMultimediaObject;
    }

    /**
     * @param null $aElement
     */
    private function convertKeywordToKeywords($aElement = null)
    {
        if ($aElement) {
            foreach ($aElement as $oElement) {
                foreach ($oElement->getI18nKeyword() as $sLanguage => $sKeyword) {
                    if ('' != $sKeyword) {
                        $aKeyword = explode(',', $sKeyword);
                        $oElement->setKeywords($aKeyword, $sLanguage);
                    }
                }
            }

            $this->dm->flush();
        }
    }

    /**
     * @param null $aMultimediaObjects
     */
    private function convertBroadcastToEmbeddedBroadcast($aMultimediaObjects = null)
    {
        if ($aMultimediaObjects) {
            foreach ($aMultimediaObjects as $oMultimedia) {
                $this->createEmbeddedBroadcast($oMultimedia);
            }
        }
    }

    /**
     * @param $oMultimedia
     */
    private function createEmbeddedBroadcast($oMultimedia)
    {
        if ($oBroadcast = $oMultimedia->getBroadcast()) {
            $oBroadcast = $this->dm->getRepository('PumukitSchemaBundle:Broadcast')->findOneBy(array('_id' => new \MongoId($oBroadcast)));
            switch ($oBroadcast->getBroadcastTypeId()) {
                case Broadcast::BROADCAST_TYPE_PUB:
                    $sName = EmbeddedBroadcast::NAME_PUBLIC;
                    $sType = EmbeddedBroadcast::TYPE_PUBLIC;
                    break;
                case Broadcast::BROADCAST_TYPE_PRI:
                    $sName = EmbeddedBroadcast::NAME_PASSWORD;
                    $sType = EmbeddedBroadcast::TYPE_PASSWORD;
                    break;
                case Broadcast::BROADCAST_TYPE_COR:
                    if ($this->typeLoginName === $oBroadcast->getName()) {
                        $sName = EmbeddedBroadcast::NAME_LOGIN;
                        $sType = EmbeddedBroadcast::TYPE_LOGIN;
                    } elseif ($oBroadcast->getPasswd() && !empty($oBroadcast->getPasswd())) {
                        $sName = EmbeddedBroadcast::NAME_PASSWORD;
                        $sType = EmbeddedBroadcast::TYPE_PASSWORD;
                    }
                    break;
            }
        } else {
            $sName = EmbeddedBroadcast::NAME_PUBLIC;
            $sType = EmbeddedBroadcast::TYPE_PUBLIC;
        }

        $oEmbeddedBroadcast = new EmbeddedBroadcast();

        $oEmbeddedBroadcast->setType($sType);
        $oEmbeddedBroadcast->setName($sName);
        if (EmbeddedBroadcast::TYPE_PASSWORD === $sType) {
            $oEmbeddedBroadcast->setPassword($oBroadcast->getPasswd());
        }
        $this->dm->persist($oEmbeddedBroadcast);

        $oMultimedia->setEmbeddedBroadcast($oEmbeddedBroadcast);

        $this->dm->flush();
    }
}

$input = new ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), getenv('SYMFONY_ENV') ?: 'dev');
$debug = getenv('SYMFONY_DEBUG') !== '0' && !$input->hasParameterOption(array('--no-debug', '')) && $env !== 'prod';

if ($debug) {
    Debug::enable();
}

$kernel = new AppKernel($env, $debug);
$application = new Application($kernel);
$application->add(new UpgradePumukitCommand());
$application->run();
