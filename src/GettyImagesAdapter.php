<?php

namespace Digicol\SchemaOrg\GettyImages;

use Digicol\SchemaOrg\Sdk\AdapterInterface;
use Digicol\SchemaOrg\Sdk\PotentialSearchActionInterface;
use Digicol\SchemaOrg\Sdk\ThingInterface;
use GettyImages\Api\GettyImages_Client;


class GettyImagesAdapter implements AdapterInterface
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
    public function getParams()
    {
        return $this->params;
    }


    /**
     * @return PotentialSearchActionInterface[]
     */
    public function getPotentialSearchActions()
    {
        $result =
            [
                'images' => new GettyImagesPotentialSearchAction
                (
                    $this,
                    [
                        'name' => 'Getty Images',
                        'description' => 'Search for both creative and editorial images',
                        'url' => 'https://api.gettyimages.com/v3/search/images'
                    ]
                )
            ];

        return $result;
    }


    /**
     * @param string $uri sameAs identifying URL
     * @return ThingInterface
     */
    public function newThing($uri)
    {
        return new GettyImagesCreativeWork($this, ['sameAs' => $uri]);
    }


    /**
     * @return GettyImages_Client
     */
    public function newGettyImages_Client()
    {
        return new GettyImages_Client
        (
            $this->params['credentials']['api_key'],
            $this->params['credentials']['api_secret']
        );
    }
}
