<?php

namespace Pumukit\NewAdminBundle\Controller;

use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Pumukit\NewAdminBundle\Form\Type\UNESCOBasicType;
use Pumukit\SchemaBundle\Document\EmbeddedBroadcast;
use Pumukit\SchemaBundle\Document\MultimediaObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;

/**
 * @Route("/unesco")
 * @Security("is_granted('ROLE_ACCESS_MULTIMEDIA_SERIES')")
 */
class UNESCOController extends Controller implements NewAdminController
{
    public static $unescoTags = array(
        'Ciencias de la vida y la salud' => array(
            'U310000',
            'U240000',
            'U320000',
            'U610000',
        ),
        'Tecnologías' => array(
            'U330000',
        ),
        'Ciencias' => array(
            'U210000',
            'U250000',
            'U220000',
            'U120000',
            'U230000',
        ),
        'Jurídicas' => array(
            'U530000',
            'U560000',
            'U590000',
            'U520000',
            'U580000',
            'U630000',
        ),
        'Humanidades' => array(
            'U510000',
            'U620000',
            'U710000',
            'U720000',
            'U540000',
            'U550000',
            'U570000',
            'U110000',
        ),
    );

    /**
     * @Route("/", name="pumukitnewadmin_unesco_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/tags", name="pumukitnewadmin_unesco_menu_tags")
     * @Template()
     */
    public function menuTagsAction()
    {
        $dm = $this->container->get('doctrine_mongodb')->getManager();
        $translator = $this->get('translator');

        $tagUNESCO = array();
        foreach (static::$unescoTags as $key => $tag) {
            foreach ($tag as $cod) {
                $tagUNESCO[$translator->trans($key)][] = $dm->getRepository('PumukitSchemaBundle:Tag')->findOneBy(
                    array('cod' => $cod)
                );
            }
        }

        $countMultimediaObjects = $dm->getRepository('PumukitSchemaBundle:MultimediaObject')->count();

        $unescoTag = $dm->getRepository('PumukitSchemaBundle:Tag')->findOneBy(array('cod' => 'UNESCO'));

        $countMultimediaObjectsWithoutTag = $dm->getRepository('PumukitSchemaBundle:MultimediaObject')->findWithoutTag($unescoTag);
        $defaultTagOptions = array(
            array('key' => 2,'title' => $translator->trans('All'), 'count' => $countMultimediaObjects),
            array('key' => 1,'title' => $translator->trans('Without category'), 'count' => count($countMultimediaObjectsWithoutTag)),
        );

        return array('tags' => $tagUNESCO, 'defaultTagOptions' => $defaultTagOptions);
    }

    /**
     * @Route("/list/{tag}", name="pumukitnewadmin_unesco_list")
     * @Template("PumukitNewAdminBundle:MultimediaObject:listAll.html.twig")
     *
     * @param Request $request
     * @param string $tag
     *
     * @return array
     */
    public function listAction(Request $request, $tag = null)
    {
        dump($request);
        $session = $this->get('session');
        $page = $session->get('admin/unesco/page', 1);
        $maxPerPage = $session->get('admin/unesco/paginate', 10);

        if(isset($tag) or $session->has('admin/unesco/tag')) {
            $tag = (isset($tag)? $tag : $session->get('admin/unesco/tag'));
            $multimediaObjects = $this->searchMultimediaObjects($request, $tag);
        } else {
            $dm = $this->container->get('doctrine_mongodb')->getManager();
            $multimediaObjects = $dm->getRepository('PumukitSchemaBundle:MultimediaObject')->createStandardQueryBuilder();
        }

        $adapter = new DoctrineODMMongoDBAdapter($multimediaObjects);
        $adapter = new Pagerfanta($adapter);

        $adapter->setMaxPerPage($maxPerPage)->setNormalizeOutOfRangePages(true);

        if (($adapter->getNbResults() / $maxPerPage) > $page) {
            $page = $adapter->getNbPages();
            $session->set('admin/unesco/page', $page);
        }

        $adapter->setCurrentPage($page);

        return array(
            'mms' => $adapter,
            'disable_pudenew' => !$this->container->getParameter('show_latest_with_pudenew'),
        );
    }

    /**
     * @param $request
     * @param $tag
     *
     * @return mixed
     */
    private function searchMultimediaObjects($request, $tag)
    {
        $dm = $this->container->get('doctrine_mongodb')->getManager();
        $session = $this->get('session');
        $session->set('admin/unesco/tag', $tag);

        $caseUNESCO = false;
        if(isset($tag) and "-1" != $tag) {
            $caseUNESCO = (strtoupper(substr($tag,0,1)));
        }

        switch ($tag) {
            case "1":
                $unescoTag = $dm->getRepository('PumukitSchemaBundle:Tag')->findOneBy(array('cod' => 'UNESCO'));
                $multimediaObjects = $dm->getRepository('PumukitSchemaBundle:MultimediaObject')->createStandardQueryBuilder()->field('tags._id')->notEqual(new \MongoId($unescoTag->getId()));
                break;
            case ($caseUNESCO === 'U'):
                $unescoTag = $dm->getRepository('PumukitSchemaBundle:Tag')->findOneBy(array('cod' => $tag));
                $multimediaObjects = $dm->getRepository('PumukitSchemaBundle:MultimediaObject')->createStandardQueryBuilder()->field('tags._id')->equals(new \MongoId($unescoTag->getId()));
                break;
            case "2":
                $multimediaObjects = $dm->getRepository('PumukitSchemaBundle:MultimediaObject')->createStandardQueryBuilder();
                break;
            default:
                break;
        }

        return $multimediaObjects;
    }

    /**
     * @Route("/advance/search/form", name="pumukitnewadmin_unesco_advance_search_form")
     * @Template("PumukitNewAdminBundle:UNESCO:search_view.html.twig")
     *
     * @return array
     */
    public function advanceSearchFormAction(Request $request)
    {
        $dm = $this->container->get('doctrine_mongodb')->getManager();

        $translator = $this->get('translator');
        $locale = $request->getLocale();

        $form = $this->createForm(new UNESCOBasicType($translator, $locale));

        $roles = $dm->getRepository('PumukitSchemaBundle:Role')->findAll();

        $statusPub = array(
            MultimediaObject::STATUS_PUBLISHED => $translator->trans('Published'),
            MultimediaObject::STATUS_BLOQ => $translator->trans('Blocked'),
            MultimediaObject::STATUS_HIDE => $translator->trans('Hidden'),
        );

        $broadcasts = array(
            EmbeddedBroadcast::TYPE_PUBLIC => $translator->trans('Public'),
            EmbeddedBroadcast::TYPE_LOGIN => $translator->trans('Login'),
            EmbeddedBroadcast::TYPE_PASSWORD => $translator->trans('Password'),
            EmbeddedBroadcast::TYPE_GROUPS => $translator->trans('Groups'),
        );

        return array(
            'form' => $form->createView(),
            'roles' => $roles,
            'statusPub' => $statusPub,
            'broadcasts' => $broadcasts,
        );
    }
}
