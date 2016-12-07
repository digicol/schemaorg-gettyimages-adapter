<?php

namespace Digicol\SchemaOrg\GettyImages;

use Digicol\SchemaOrg\Sdk\AbstractSearchAction;
use Digicol\SchemaOrg\Sdk\ItemListInterface;
use Digicol\SchemaOrg\Sdk\SearchActionInterface;
use Digicol\SchemaOrg\Sdk\Utils;


class GettyImagesSearchAction extends AbstractSearchAction implements SearchActionInterface
{
    const DEFAULT_PAGESIZE = 20;

    /** @var GettyImagesAdapter */
    protected $adapter;


    /**
     * @return ItemListInterface
     */
    public function getResult()
    {
        if (empty($this->getQuery()))
        {
            return new GettyImagesItemList($this->getAdapter(), $this, [ 'search_response' => [] ]);
        }

        $client = $this->adapter->newGettyImages_Client();

        $query = $this->getQuery();

        $items_per_page = Utils::getItemsPerPage($this->input_properties, self::DEFAULT_PAGESIZE);
        $page = ceil(Utils::getStartIndex($this->input_properties) / $items_per_page);

        $responseJson = $client
            ->Search()
            // TODO: Images() depends on parameters
            ->Images()
            ->withPhrase($query)
            ->withPage($page)
            ->withPageSize($items_per_page)
            ->withResponseField('detail_set')
            ->execute();

        $response = json_decode($responseJson, true);
        
        return new GettyImagesItemList($this->getAdapter(), $this, [ 'search_response' => $response ]);
    }
}
