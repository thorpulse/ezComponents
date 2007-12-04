<?php
/**
 * File containing the ezcWebdavGetResourceResponse class.
 *
 * @package Webdav
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class generated by the backend to respond to GET requests on a resource.
 *
 * If a {@link ezcWebdavBackend} receives an instance of {@link
 * ezcWebdavGetRequest} it might react with an instance of {@link
 * ezcWebdavGetResourceResponse} for non-collection resources or {@link
 * ezcWebdavGetCollectionResponse} for collection resources or by producing an
 * error.
 *
 * @property string $resource
 *           The path of the requested resource.
 *
 * @version //autogentag//
 * @package Webdav
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
class ezcWebdavGetResourceResponse extends ezcWebdavResponse
{
    /**
     * Creates a new response object.
     *
     * Creates a new repsonse for the given $resource.
     * 
     * @param ezcWebdavResource $resource 
     * @return void
     */
    public function __construct( ezcWebdavResource $resource )
    {
        parent::__construct( ezcWebdavResponse::STATUS_200 );

        $this->resource = $resource;
    }

    /**
     * Sets a property.
     *
     * This method is called when an property is to be set.
     * 
     * @param string $propertyName The name of the property to set.
     * @param mixed $propertyValue The property value.
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'resource':
                if ( ! $propertyValue instanceof ezcWebdavResource )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavResource' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                parent::__set( $propertyName, $propertyValue );
        }
    }
}

?>
