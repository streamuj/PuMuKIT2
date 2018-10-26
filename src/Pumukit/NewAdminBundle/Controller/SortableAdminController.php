<?php

namespace Pumukit\NewAdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class SortableAdminController extends AdminController implements NewAdminController
{
    public function upAction(Request $request)
    {
        $resource = $this->findOr404($request);

        $new_rank = $resource->getRank() + 1;
        $resource->setRank($new_rank);
        $this->domainManager->update($resource);

        return $this->redirectToRoute(
            $this->getRedirectRoute('index'),
            $this->getRedirectParameters()
        );
    }

    public function downAction(Request $request)
    {
        $resource = $this->findOr404($request);

        $new_rank = $resource->getRank() - 1;
        $resource->setRank($new_rank);
        $this->domainManager->update($resource);

        return $this->redirectToRoute(
            $this->getRedirectRoute('index'),
            $this->getRedirectParameters()
        );
    }

    public function topAction(Request $request)
    {
        $resource = $this->findOr404($request);

        $new_rank = -1;
        $resource->setRank($new_rank);
        $this->domainManager->update($resource);

        return $this->redirectToRoute(
            $this->getRedirectRoute('index'),
            $this->getRedirectParameters()
        );
    }

    public function bottomAction(Request $request)
    {
        $resource = $this->findOr404($request);

        $new_rank = 0;
        $resource->setRank($new_rank);
        $this->domainManager->update($resource);

        return $this->redirectToRoute(
            $this->getRedirectRoute('index'),
            $this->getRedirectParameters()
        );
    }
}
