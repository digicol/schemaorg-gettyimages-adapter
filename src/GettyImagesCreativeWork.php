<?php

namespace Digicol\SchemaOrg\GettyImages;


class GettyImagesCreativeWork implements \Digicol\SchemaOrg\ThingInterface
{
    protected $params = [ ];
    protected $response = [ ];


    /**
     * ThingInterface constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;

        unset($this->params[ 'response' ]);

        $this->response = $params[ 'response' ];
    }


    /**
     * Get item type
     *
     * @return string schema.org type like "ImageObject" or "Thing"
     */
    public function getType()
    {
        // TODO: Might be a video instead. Read from $this->params[ 'response' ]
        return 'ImageObject';
    }


    /**
     * Get all property values
     *
     * @return array
     */
    public function getProperties()
    {
        $result =
            [
                'name' => $this->response[ 'title' ],
                'description' => $this->response[ 'caption' ]
            ];

        foreach ($this->response[ 'display_sizes' ] as $display_size)
        {
            if ($display_size[ 'name' ] === 'thumb')
            {
                $result[ 'image' ] = $display_size[ 'uri' ];
            }
        }

        return $result;
    }

}