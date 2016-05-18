<?php

namespace Digicol\SchemaOrg\GettyImages;


class GettyImagesAdapter implements \Digicol\SchemaOrg\AdapterInterface
{
    protected $params;


    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }


    /** @return array */
    public function describeSearchActions()
    {
        $result =
            [
                'images' =>
                    [
                        'name' => 'Images',
                        'description' => 'Search for both creative and editorial images',
                        'url' => 'https://api.gettyimages.com/v3/search/images'
                    ]
            ];

        return $result;
    }


    /**
     * @param array $search_params
     * @return \Digicol\SchemaOrg\SearchActionInterface
     */
    public function newSearchAction(array $search_params)
    {
        return new GettyImagesSearchAction($this, $search_params);
    }


    /**
     * @param string $uri sameAs identifying URL
     * @return \Digicol\SchemaOrg\ThingInterface
     */
    public function newThing($uri)
    {
        return new GettyImagesCreativeWork($this, [ 'sameAs' => $uri ]);
    }


    /**
     * @return \GettyImages\Api\GettyImages_Client
     */
    public function newGettyImages_Client()
    {
        return new \GettyImages\Api\GettyImages_Client
        (
            $this->params[ 'credentials' ][ 'api_key' ],
            $this->params[ 'credentials' ][ 'api_secret' ]
        );
    }
}