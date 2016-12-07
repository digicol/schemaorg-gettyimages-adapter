<?php

namespace Digicol\SchemaOrg\GettyImages;

use Digicol\SchemaOrg\Sdk\AbstractPotentialSearchAction;
use Digicol\SchemaOrg\Sdk\PotentialSearchActionInterface;
use Digicol\SchemaOrg\Sdk\SearchActionInterface;


class GettyImagesPotentialSearchAction extends AbstractPotentialSearchAction implements PotentialSearchActionInterface
{
    /** @return array */
    public function describeInputProperties()
    {
        return [ ];
    }


    /**
     * @return SearchActionInterface
     */
    public function newSearchAction()
    {
        return new GettyImagesSearchAction($this->getAdapter(), $this, [ ]);
    }
}
