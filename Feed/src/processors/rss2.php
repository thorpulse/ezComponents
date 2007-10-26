<?php
/**
 * File containing the ezcFeedRss2 class.
 *
 * @package Feed
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class providing parsing and generating of RSS2 feeds.
 *
 * Specifications:
 * {@link http://www.rssboard.org/rss-specification RSS2 Specifications}.
 *
 * @package Feed
 * @version //autogentag//
 */
class ezcFeedRss2 extends ezcFeedProcessor implements ezcFeedParser
{
    /**
     * Defines the feed type of this processor.
     */
    const FEED_TYPE = 'rss2';

    /**
     * Holds the RSS2 feed schema.
     *
     * @var array(string=>mixed)
     * @ignore
     */
    protected static $rss2Schema = array(
        'title'          => array( '#'          => 'string' ),

        'link'           => array( '#'          => 'string',
                                   'MULTI'      => 'links' ),

        'description'    => array( '#'          => 'string' ),

        'language'       => array( '#'          => 'string' ),
        'copyright'      => array( '#'          => 'string' ),
        'managingEditor' => array( '#'          => 'string' ),
        'webMaster'      => array( '#'          => 'string' ),
        'pubDate'        => array( '#'          => 'string' ),
        'lastBuildDate'  => array( '#'          => 'string' ),
        'category'       => array( '#'          => 'string',
                                   'ATTRIBUTES' => array( 'domain' => 'string' ),
                                   'MULTI'      => 'categories' ),

        'generator'      => array( '#'          => 'string' ),
        'docs'           => array( '#'          => 'string' ),
        'ttl'            => array( '#'          => 'string' ),
        'image'          => array( '#'          => 'string',
                                   'NODES'      => array(
                                                     'url'         => array( '#' => 'string' ),
                                                     'title'       => array( '#' => 'string' ),
                                                     'link'        => array( '#' => 'string' ),

                                                     'description' => array( '#' => 'string' ),
                                                     'width'       => array( '#' => 'string' ),
                                                     'height'      => array( '#' => 'string' ),
                                                     'REQUIRED'    => array( 'url', 'title', 'link' ),
                                                     'OPTIONAL'    => array( 'description', 'width', 'height' ),
                                                     ), ),

        'rating'         => array( '#'          => 'string' ),
        'textInput'      => array( '#'          => 'string' ),
        'skipHours'      => array( '#'          => 'string' ),
        'skipDays'       => array( '#'          => 'string' ),

        'item'           => array( '#'          => 'node',
                                   'NODES'      => array(
                                                     'title'        => array( '#' => 'string' ),
                                                     'link'         => array( '#' => 'string' ),
                                                     'description'  => array( '#' => 'string' ),

                                                     'author'       => array( '#' => 'string' ),
                                                     'category'     => array( '#' => 'string' ),
                                                     'comments'     => array( '#' => 'string' ),
                                                     'enclosure'    => array( '#' => 'string' ),
                                                     'guid'         => array( '#' => 'string',
                                                                              'ATTRIBUTES' => array( 'isPermaLink' => 'string' ) ),

                                                     'pubDate'      => array( '#' => 'string' ),
                                                     'source'       => array( '#' => 'string' ),

                                                     'AT_LEAST_ONE' => array( 'title', 'link', 'description' ),
                                                     'OPTIONAL'     => array( 'title', 'link', 'description',
                                                                              'author', 'category', 'comments',
                                                                              'enclosure', 'guid', 'pubDate',
                                                                              'source' ),
                                                     ),
                                   'MULTI'      => 'items' ),

        'REQUIRED'       => array( 'title', 'link', 'description' ),
        'OPTIONAL'       => array( 'language', 'copyright', 'managingEditor',
                                   'webMaster', 'pubDate', 'lastBuildDate',
                                   'category', 'generator', 'docs',
                                   'ttl', 'image', 'rating',
                                   'textInput', 'skipHours', 'skipDays',
                                 ), // don't include 'item' here

        'MULTI'          => array( 'links'      => 'link',
                                   'categories' => 'category',
                                   'items'      => 'item' ),

        'ELEMENTS_MAP'   => array( 'author'     => 'managingEditor',
                                   'published'  => 'pubDate',
                                   'updated'    => 'lastBuildDate' ),

        'ITEMS_MAP'      => array( 'published'  => 'pubDate' ),

        );

    /**
     * Creates a new RSS2 processor.
     */
    public function __construct()
    {
        $this->feedType = self::FEED_TYPE;
        $this->schema = new ezcFeedSchema( self::$rss2Schema );

        // set default values
        $this->set( 'published', ezcFeedTools::prepareDate( time() ) );
        $this->set( 'generator', 'eZ Components' );
        $this->set( 'docs', 'http://www.rssboard.org/rss-specification' );
    }

    /**
     * Creates a root node for the XML document being generated.
     *
     * @param string $version The RSS version for the root node
     */
    public function createRootElement( $version )
    {
        $rss = $this->xml->createElement( 'rss' );
        $rssVersionTag = $this->xml->createAttribute( 'version' );
        $rssVersionContent = $this->xml->createTextNode( $version );
        $rssVersionTag->appendChild( $rssVersionContent );
        $rss->appendChild( $rssVersionTag );
        $this->channel = $channelTag = $this->xml->createElement( 'channel' );
        $rss->appendChild( $channelTag );
        $this->root = $this->xml->appendChild( $rss );
    }

    /**
     * Sets the namespace attribute in the XML document being generated.
     *
     * @param string $prefix The prefix to use
     * @param string $namespace The namespace to use
     */
    public function generateNamespace( $prefix, $namespace )
    {
        $this->root->setAttributeNS( "http://www.w3.org/2000/xmlns/", "xmlns:{$prefix}", $namespace );
    }

    /**
     * Returns an XML string from the feed information contained in this
     * processor.
     *
     * @return string
     */
    public function generate()
    {
        $this->xml = new DOMDocument( '1.0', 'utf-8' );
        $this->xml->formatOutput = 1;
        $this->createRootElement( '2.0' );

        $this->generateRequired();
        $this->generateOptional();
        $this->generateItems();

        return $this->xml->saveXML();
    }

    /**
     * Adds the required feed elements to the XML document being generated.
     *
     * @ignore
     */
    protected function generateRequired()
    {
        foreach ( $this->schema->getRequired() as $element )
        {
            $data = $this->schema->isMulti( $element ) ? $this->get( $this->schema->getMulti( $element ) ) : $this->get( $element );
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( $element );
            }

            if ( !is_array( $data ) )
            {
                $data = array( $data );
            }

            foreach ( $data as $dataNode )
            {
                $this->generateNode( $element, $dataNode );
            }
        }
    }

    /**
     * Adds the optional feed elements to the XML document being generated.
     *
     * @ignore
     */
    protected function generateOptional()
    {
        foreach ( $this->schema->getOptional() as $element )
        {
            $normalizedAttribute = ezcFeedTools::normalizeName( $element, $this->schema->getElementsMap() );
            $data = $this->schema->isMulti( $element ) ? $this->get( $this->schema->getMulti( $element ) ) : $this->get( $element );

            if ( !is_null( $data ) )
            {
                // @todo Add hooks
                switch ( $element )
                {
                    case 'published':
                    case 'updated':
                        $this->generateMetaData( $this->channel, $normalizedAttribute, date( 'D, d M Y H:i:s O', $data ) );
                        break;

                    default:
                        if ( !is_array( $data ) )
                        {
                            $data = array( $data );
                        }

                        foreach ( $data as $dataNode )
                        {
                            $this->generateNode( $element, $dataNode );
                        }
                        break;
                }
            }
        }
    }

    /**
     * Creates an XML node in the XML document being generated.
     *
     * @param string $element The name of the node to create
     * @param array(string=>mixed) $dataNode The data for the node to create
     * @ignore
     */
    protected function generateNode( $element, $dataNode )
    {
        $attributes = array();
        foreach ( $this->schema->getAttributes( $element ) as $attribute => $type )
        {
            if ( isset( $dataNode->$attribute ) )
            {
                $attributes[$attribute] = $dataNode->$attribute;
            }
        }
        if ( count( $attributes ) >= 1 )
        {
            $this->generateMetaDataWithAttributes( $this->channel, $element, $dataNode, $attributes );
        }
        else
        {
            $this->generateMetaData( $this->channel, $element, $dataNode );
        }
    }

    /**
     * Adds the feed items to the XML document being generated.
     *
     * @ignore
     */
    protected function generateItems()
    {
        $items = $this->get( 'items' );
        if ( $items === null )
        {
            return;
        }

        foreach ( $this->get( 'items' ) as $item )
        {
            $itemTag = $this->xml->createElement( 'item' );
            $this->channel->appendChild( $itemTag );

            $atLeastOneRequiredFeedItemPresent = false;
            foreach ( $this->schema->getAtLeastOne( 'item' ) as $attribute )
            {
                $data = $this->schema->isMulti( 'item', $attribute ) ? $this->get( $this->schema->getMulti( 'item', $attribute ) ) : $item->$attribute;
                if ( !is_null( $data ) )
                {
                    $atLeastOneRequiredFeedItemPresent = true;
                    break;
                }
            }

            if ( $atLeastOneRequiredFeedItemPresent === false )
            {
                throw new ezcFeedAtLeastOneItemDataRequiredException( $this->schema->getAtLeastOne( 'item' ) );
            }

            foreach ( $this->schema->getOptional( 'item' ) as $attribute )
            {
                $normalizedAttribute = ezcFeedTools::normalizeName( $attribute, $this->schema->getItemsMap() );

                $metaData = $this->schema->isMulti( 'item', $attribute ) ? $this->get( $this->schema->getMulti( 'item', $attribute ) ) : $item->$attribute;
                if ( !is_null( $metaData ) )
                {
                    // @todo Add hooks
                    switch ( $attribute )
                    {
                        case 'guid':
                            $permalink = substr( $metaData, 0, 7 ) === 'http://' ? "true" : "false";
                            $this->generateMetaDataWithAttributes( $itemTag, $normalizedAttribute, $metaData, array( 'isPermaLink' => $permalink ) );
                            break;

                        case 'published':
                            $this->generateMetaData( $itemTag, $normalizedAttribute, date( 'D, d M Y H:i:s O', $metaData ) );
                            break;

                        default:
                            $this->generateMetaData( $itemTag, $normalizedAttribute, $metaData );
                    }
                }
            }
        }
    }

    /**
     * Returns true if the parser can parse the provided XML document object,
     * false otherwise.
     *
     * @param DOMDocument $xml The XML document object to check for parseability
     * @return bool
     */
    public static function canParse( DOMDocument $xml )
    {
        if ( $xml->documentElement->tagName !== 'rss' )
        {
            return false;
        }
        if ( !$xml->documentElement->hasAttribute( 'version' ) )
        {
            return false;
        }
        if ( $xml->documentElement->getAttribute( 'version' ) !== "2.0" )
        {
            return false;
        }
        return true;
    }

    /**
     * Parses the provided XML document object and returns an ezcFeed object
     * from it.
     *
     * @throws ezcFeedParseErrorException
     *         If an error was encountered during parsing.
     *
     * @param DOMDocument $xml The XML document object to parse
     * @return ezcFeed
     */
    public function parse( DOMDocument $xml )
    {
        $feed = new ezcFeed( self::FEED_TYPE );
        $rssChildren = $xml->documentElement->childNodes;
        $channel = null;

        // figure out modules
        $this->usedPrefixes = array();
        $xp = new DOMXpath( $xml );
        $set = $xp->query( './namespace::*', $xml->documentElement );

        foreach ( $rssChildren as $rssChild )
        {
            if ( $rssChild->nodeType === XML_ELEMENT_NODE
                 && $rssChild->tagName === 'channel' )
            {
                $channel = $rssChild;
            }
        }

        if ( $channel === null )
        {
            throw new ezcFeedParseErrorException( "No channel tag" );
        }

        foreach ( $channel->childNodes as $channelChild )
        {
            if ( $channelChild->nodeType == XML_ELEMENT_NODE )
            {
                $tagName = $channelChild->tagName;
                $tagName = ezcFeedTools::deNormalizeName( $tagName, $this->schema->getElementsMap() );

                switch ( $tagName )
                {
                    case 'title':
                    case 'description':
                    case 'language':
                    case 'copyright':
                    case 'author':
                    case 'webMaster':
                    case 'generator':
                    case 'ttl':
                    case 'docs':
                        $feed->$tagName = $channelChild->textContent;
                        break;

                    case 'link':
                    case 'category':
                        $element = $feed->add( $tagName );
                        $element->set( $channelChild->textContent );
                        break;

                    case 'published':
                    case 'updated':
                        $feed->$tagName = ezcFeedTools::prepareDate( $channelChild->textContent );
                        break;

                    case 'item':
                        $element = $feed->add( $tagName );
                        $this->parseItem( $feed, $element, $channelChild );
                        break;

                    case 'image':
                        $this->parseImage( $feed, $channelChild );
                        break;

                    default:
                        // check if it's part of a known module/namespace
                }
            }

            foreach ( ezcFeedTools::getAttributes( $channelChild ) as $key => $value )
            {
                if ( in_array( $tagName, array( 'category' ) ) )
                {
                    $element->$key = $value;
                }
                else
                {
                    $feed->$tagName->$key = $value;
                }
            }
        }
        return $feed;
    }

    /**
     * Parses the provided XML element object and stores it as a feed item in
     * the provided ezcFeed object.
     *
     * @param ezcFeed $feed The feed object in which to store the parsed XML element as a feed item
     * @param ezcFeedElement $element The feed element object that will contain the feed item
     * @param DOMElement $xml The XML element object to parse
     */
    public function parseItem( ezcFeed $feed, ezcFeedElement $element, DOMElement $xml )
    {
        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;
                $tagName = ezcFeedTools::deNormalizeName( $tagName, $this->schema->getItemsMap() );

                switch ( $tagName )
                {
                    case 'title':
                    case 'link':
                    case 'description':
                    case 'author':
                    case 'comments':
                    case 'enclosure':
                    case 'guid':
                    case 'source':
                        $element->$tagName = $itemChild->textContent;
                        break;

                    case 'published':
                        $element->$tagName = ezcFeedTools::prepareDate( $itemChild->textContent );
                        break;

                    case 'category':
                        $subElement = $element->add( $tagName );
                        $subElement->set( $itemChild->textContent );
                        break;

                    default:
                        // check if it's part of a known module/namespace
                }

                foreach ( ezcFeedTools::getAttributes( $itemChild ) as $key => $value )
                {
                    if ( in_array( $tagName, array( 'category' ) ) )
                    {
                        $subElement->$key = $value;
                    }
                    else
                    {
                        $element->$tagName->$key = $value;
                    }
                }
            }
        }
    }

    /**
     * Parses the provided XML element object and stores it as a feed image in
     * the provided ezcFeed object.
     *
     * @param ezcFeed $feed The feed object in which to store the parsed XML element as a feed image
     * @param DOMElement $xml The XML element object to parse
     */
    public function parseImage( ezcFeed $feed, DOMElement $xml )
    {
        $feedImage = $feed->newImage();
        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;

                switch ( $tagName )
                {
                    case 'title': // required in RSS2
                    case 'link': // required in RSS2
                    case 'url': // required in RSS2

                    case 'description':
                    case 'width':
                    case 'height':
                        $feedImage->$tagName = $itemChild->textContent;
                        break;
                }
            }
        }
    }
}
?>
