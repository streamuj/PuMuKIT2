<?php

namespace Pumukit\BasePlayerBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Pumukit\SchemaBundle\Document\Track;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TrackUrlService
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function generateTrackFileUrl(Track $track, $reference_type = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $ext = pathinfo(parse_url($track->getUrl(), PHP_URL_PATH), PATHINFO_EXTENSION);
        if (!$ext) {
            $ext = pathinfo($track->getPath(), PATHINFO_EXTENSION);
        }

        $params = array(
            'id' => $track->getId(),
            'ext' => $ext,
        );
        $url = $this->router->generate('pumukit_trackfile_index', $params, $reference_type);

        return $url;
    }
}
