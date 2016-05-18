<?php

namespace Digicol\SchemaOrg\GettyImages;


class GettyImagesCreativeWork implements \Digicol\SchemaOrg\ThingInterface
{
    /** @var GettyImagesAdapter */
    protected $adapter;

    /** @var array */
    protected $params = [ ];


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
        if (! empty($this->params[ 'search_response' ]))
        {
            $response = $this->params[ 'search_response' ];
        }
        else
        {
            $response = $this->loadDetails($this->params[ 'sameAs' ]);
        }

        $result =
            [
                'name' => $response[ 'title' ],
                'description' => $response[ 'caption' ],
                'sameAs' => 'https://api.gettyimages.com/v3/image?id=' . urlencode($response[ 'id' ])
            ];

        foreach ($response[ 'display_sizes' ] as $display_size)
        {
            if ($display_size[ 'name' ] === 'thumb')
            {
                $result[ 'image' ] = $display_size[ 'uri' ];
            }
        }

        return $result;
    }


    /**
     * @return array Response
     */
    protected function loadDetails($uri)
    {
        $client = $this->adapter->newGettyImages_Client();

        $response = $client
            ->Images()
            ->withId($this->uriToId($uri))
            //->Fields([ 'caption', 'display_sizes', 'id', 'title' ])
            ->execute();

        $response = json_decode($response, true);

        return $response[ 'images' ][ 0 ];
    }


    protected function uriToId($uri)
    {
        // https://api.gettyimages.com/v3/image?id=abc => abc

        $qstring = parse_url($uri, PHP_URL_QUERY);

        parse_str($qstring, $qparams);

        return $qparams[ 'id' ];
    }
}