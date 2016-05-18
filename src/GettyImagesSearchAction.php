<?php

namespace Digicol\SchemaOrg\GettyImages;


class GettyImagesSearchAction implements \Digicol\SchemaOrg\SearchActionInterface
{
    const DEFAULT_PAGESIZE = 20;
    protected $params = [ ];
    protected $input_properties = [ ];
    protected $response = [ ];


    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
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

        $client = new \GettyImages\Api\GettyImages_Client
        (
            $this->params[ 'credentials' ][ 'api_key' ],
            $this->params[ 'credentials' ][ 'api_secret' ]
        );

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
            $result[ ] = new GettyImagesCreativeWork([ 'response' => $item ]);
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
                'opensearch:totalResults' => intval($this->response[ 'result_count' ]),
                'opensearch:startIndex' => \Digicol\SchemaOrg\Utils::getStartIndex($this->input_properties, self::DEFAULT_PAGESIZE),
                'opensearch:itemsPerPage' => \Digicol\SchemaOrg\Utils::getItemsPerPage($this->input_properties, self::DEFAULT_PAGESIZE)
            ];
    }

}