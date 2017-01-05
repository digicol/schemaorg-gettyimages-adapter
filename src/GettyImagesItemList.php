<?php

namespace Digicol\SchemaOrg\GettyImages;

use Digicol\SchemaOrg\Sdk\AbstractItemList;
use Digicol\SchemaOrg\Sdk\AdapterInterface;
use Digicol\SchemaOrg\Sdk\ItemListInterface;
use Digicol\SchemaOrg\Sdk\SearchActionInterface;
use Digicol\SchemaOrg\Sdk\Utils;


class GettyImagesItemList extends AbstractItemList implements ItemListInterface
{
    /** @var GettyImagesAdapter */
    protected $adapter;


    /**
     * @param array $params
     */
    public function __construct(AdapterInterface $adapter, SearchActionInterface $searchAction, array $params)
    {
        parent::__construct($adapter, $searchAction, $params);

        $this->prepareItems();
    }


    protected function prepareItems()
    {
        $this->items = [];
        $this->outputProperties['numberOfItems'] = 0;

        $response = $this->params['search_response'];

        if ((! is_array($response)) || empty($response['images'])) {
            return;
        }

        $inputProperties = $this->getSearchAction()->getInputProperties();

        $this->outputProperties['numberOfItems'] = (isset($response['result_count']) ? intval($response['result_count']) : 0);
        $this->outputProperties['opensearch:startIndex'] = Utils::getStartIndex($inputProperties);
        
        $this->outputProperties['opensearch:itemsPerPage'] = Utils::getItemsPerPage($inputProperties,
            GettyImagesSearchAction::DEFAULT_PAGESIZE);
        
        foreach ($response['images'] as $i => $item) {
            $this->items[] = new GettyImagesCreativeWork
            (
                $this->adapter,
                ['search_response' => $item]
            );
        }
    }
}
