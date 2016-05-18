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
        foreach ([ 'api_key', 'api_secret' ] as $key)
        {
            $search_params[ $key ] = $this->params[ $key ];
        }

        return new GettyImagesSearchAction($search_params);
    }
}