<?php

namespace Digicol\SchemaOrg\GettyImages;


class GettyImagesSearchAction implements \Digicol\SchemaOrg\SearchActionInterface
{
    const DEFAULT_PAGESIZE = 20;

    /** @var GettyImagesAdapter */
    protected $adapter;

    /** @var array */
    protected $params = [ ];

    /** @var array */
    protected $input_properties = [ ];

    /** @var array */
    protected $response = [ ];


    /**
     * @param GettyImagesAdapter $adapter
     * @param array $params
     */
    public function __construct(GettyImagesAdapter $adapter, array $params)
    {
        $this->adapter = $adapter;
        $this->params = $params;
    }


    /**
     * Get item type
     *
     * @return string schema.org type like "ImageObject" or "Thing"
     */
    public function getType()
    {
        return 'SearchAction';
    }


    /**
     * Get identifier URI
     *
     * @return string
     */
    public function getSameAs()
    {
        return '';
    }


    /** @return array */
    public function getParams()
    {
        return $this->params;
    }


    /** @return array */
    public function describeInputProperties()
    {
        return [ ];
    }


    /**
     * Set search parameters
     *
     * Common values that should be supported:
     *   query (string)
     *   opensearch:count (int; items per page)
     *   opensearch:startPage (int; 1 for the first page)
     *
     * @param array $values
     * @return int
     */
    public function setInputProperties(array $values)
    {
        $this->input_properties = $values;

        return 1;
    }


    /**
     * Get search parameters
     *
     * @return array
     */
    public function getInputProperties()
    {
        return $this->input_properties;
    }


    /**
     * @return int
     */
    public function execute()
    {
        if (empty($this->input_properties['query']))
        {
            // TODO error handling
            return -1;
        }

        $client = $this->adapter->newGettyImages_Client();

        $query = $this->input_properties['query'];

        $items_per_page = \Digicol\SchemaOrg\Utils::getItemsPerPage($this->input_properties, self::DEFAULT_PAGESIZE);
        $page = \Digicol\SchemaOrg\Utils::getStartPage($this->input_properties);

        $response = $client
            ->Search()
            // TODO: Images() depends on url param
            ->Images()
            ->withPhrase($query)
            ->withPage($page)
            ->withPageSize($items_per_page)
            ->execute();

        $this->response = json_decode($response, true);

        return 1;
    }


    /**
     * Get all property values
     *
     * @return array
     */
    public function getProperties()
    {
        $result = \Digicol\SchemaOrg\Utils::getSearchActionSkeleton();

        if (empty($this->response['images']))
        {
            return $result;
        }

        $result[ 'query' ] = (isset($this->input_properties['query']) ? $this->input_properties['query'] : '');
        $result[ 'result' ][ 'numberOfItems' ] = (isset($this->response[ 'result_count' ]) ? intval($this->response[ 'result_count' ]) : 0);
        $result[ 'result' ][ 'opensearch:itemsPerPage' ] = \Digicol\SchemaOrg\Utils::getItemsPerPage($this->input_properties, self::DEFAULT_PAGESIZE);
        $result[ 'result' ][ 'opensearch:startIndex' ] = \Digicol\SchemaOrg\Utils::getStartIndex($this->input_properties, self::DEFAULT_PAGESIZE);

        foreach ($this->response['images'] as $i => $item)
        {
            $result[ 'result' ][ 'itemListElement' ][ ] =
                [
                    '@type' => 'ListItem',
                    'position' => ($i + 1),
                    'item' => new GettyImagesCreativeWork($this->adapter, [ 'search_response' => $item ])
                ];
        }

        return $result;
    }

}
