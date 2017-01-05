<?php

namespace Digicol\SchemaOrg\GettyImages;

use Digicol\SchemaOrg\Sdk\AbstractThing;
use Digicol\SchemaOrg\Sdk\ThingInterface;
use Digicol\SchemaOrg\Sdk\Utils;


class GettyImagesCreativeWork extends AbstractThing implements ThingInterface
{
    /** @var GettyImagesAdapter */
    protected $adapter;


    /**
     * Get identifier URI
     *
     * @return string
     */
    public function getSameAs()
    {
        if (! empty($this->params['search_response']['id'])) {
            return $this->idToUri($this->params['search_response']['id']);
        } elseif (! empty($this->params['sameAs'])) {
            return $this->params['sameAs'];
        } else {
            return '';
        }
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
        if (! empty($this->params['search_response'])) {
            $response = $this->params['search_response'];
        } else {
            $response = $this->loadDetails($this->params['sameAs']);
        }

        $result =
            [
                '@context' => Utils::getNamespaceContext(),
                '@type' => $this->getType(),
                'name' => [['@value' => $response['title']]],
                'caption' => [['@value' => $response['caption']]],
                'provider' => [['@value' => 'Getty Images']],
                'sameAs' => [['@id' => $this->idToUri($response['id'])]]
            ];

        // description

        if (mb_strlen($response['caption']) > 500) {
            $captionShort = mb_substr($response['caption'], 0, 500) . '…';
        } else {
            $captionShort = $response['caption'];
        }

        $result['description'] = [['@value' => $captionShort]];

        // dateCreated

        foreach (['date_created', 'date_submitted'] as $key) {
            if (! empty($response[$key])) {
                // Common DC-X hack - assume midnight means "just the date, no time"

                if (strpos($response[$key], 'T00:00:00') !== false) {
                    $datatype = 'Date';
                } else {
                    $datatype = 'DateTime';
                }

                $result['dateCreated'] =
                    [
                        [
                            '@value' => $response[$key],
                            '@type' => $datatype
                        ]
                    ];

                break;
            }
        }

        // image

        $result['thumbnail'] = [];

        foreach ($response['display_sizes'] as $displaySize) {
            $result['thumbnail'][] = $this->displaySizeToMediaObject($displaySize);
        }

        return $result;
    }


    /**
     * Get media object array
     *
     * @param array $displaySize
     * @return array
     */
    protected function displaySizeToMediaObject(array $displaySize)
    {
        $result =
            [
                '@type' => 'ImageObject',
                'contentUrl' => $displaySize['uri']
            ];

        if (! empty($displaySize['width'])) {
            $result['width'] = intval($displaySize['width']);
        }

        if (! empty($displaySize['height'])) {
            $result['height'] = intval($displaySize['height']);
        }

        return $result;
    }


    /**
     * @param array $properties
     * @return array
     */
    public function getReconciledProperties(array $properties)
    {
        return Utils::reconcileThingProperties
        (
            $this->getType(),
            $properties
        );
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
            ->withResponseField('detail_set')
            ->withResponseField('display_set')
            ->execute();

        $response = json_decode($response, true);

        return $response['images'][0];
    }


    protected function uriToId($uri)
    {
        // https://api.gettyimages.com/v3/image?id=abc => abc

        $qstring = parse_url($uri, PHP_URL_QUERY);

        parse_str($qstring, $qparams);

        return $qparams['id'];
    }


    protected function idToUri($id)
    {
        // TODO: Use referral_destinations instead, see http://developer.gettyimages.com/forum/read/191104/
        return 'https://api.gettyimages.com/v3/image?id=' . urlencode($id);
    }
}
