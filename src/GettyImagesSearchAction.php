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
     * @return int
     */
    public function execute()
    {
        if (empty($this->input_properties['q']))
        {
            // TODO error handling
            return -1;
        }

        $client = $this->adapter->newGettyImages_Client();

        $query = $this->input_properties['q'];

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
     * Get search results
     *
     * @return array Array of objects implementing ThingInterface
     */
    public function getResult()
    {
        $result = [ ];

        if (empty($this->response['images']))
        {
            return $result;
        }

        foreach ($this->response['images'] as $item)
        {
            $result[ ] = new GettyImagesCreativeWork($this->adapter, [ 'search_response' => $item ]);
        }

        return $result;
    }


    /**
     * Get search result metadata
     *
     * The array should contain at least these three values:
     *
     *   opensearch:totalResults (int)
     *   opensearch:startIndex (int; 1 for the first document)
     *   opensearch:itemsPerPage (int)
     *
     * @return array
     */
    public function getResultMeta()
    {
        return
            [
                'opensearch:totalResults' => (isset($this->response[ 'result_count' ]) ? intval($this->response[ 'result_count' ]) : 0),
                'opensearch:startIndex' => \Digicol\SchemaOrg\Utils::getStartIndex($this->input_properties, self::DEFAULT_PAGESIZE),
                'opensearch:itemsPerPage' => \Digicol\SchemaOrg\Utils::getItemsPerPage($this->input_properties, self::DEFAULT_PAGESIZE)
            ];
    }

}