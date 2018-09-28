<?php

namespace Pumukit\WebTVBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ChannelController extends Controller implements WebTVController
{
    /**
     * @Route("/series/channel/{channelNumber}.html", name="pumukit_webtv_channel_series")
     * @Template("PumukitWebTVBundle:Channel:index.html.twig")
     */
    public function seriesAction(Request $request, $channelNumber)
    {
        $numberCols = $this->container->getParameter('columns_objs_bytag');
        $limit = $this->container->getParameter('limit_objs_bytag');

        $repoSeries = $this->get('doctrine_mongodb')->getRepository('PumukitSchemaBundle:Series');
        $repoMmobj = $this->get('doctrine_mongodb')->getRepository('PumukitSchemaBundle:MultimediaObject');

        $channelService = $this->get('pumukit_web_tv.channels');
        $channelTitle = $channelService->getChannelTitle($channelNumber);
        $channelTags = $channelService->getTagsForChannel($channelNumber);

        $results = $channelService->getChannelSeriesByTags($channelTags);

        $this->updateBreadcrumbs($channelTitle, 'pumukit_webtv_channel_series', array('channelNumber' => $channelNumber));

        return array(
            'title' => $channelTitle,
            'results' => $results,
            'number_cols' => $numberCols,
        );
    }

    private function updateBreadcrumbs($title, $routeName, array $routeParameters = array())
    {
        $breadcrumbs = $this->get('pumukit_web_tv.breadcrumbs');
        $breadcrumbs->add($title, $routeName, $routeParameters);
    }
}
