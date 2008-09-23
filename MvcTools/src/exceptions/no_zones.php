<?php
/**
 * File containing the ezcMvcToolsNoZonesException class.
 *
 * @package MvcTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when the createZones() method does not return any zones.
 *
 * @package MvcTools
 * @version //autogentag//
 */
class ezcMvcNoZonesException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcNoZonesException
     */
    public function __construct()
    {
        parent::__construct( "No zones are defined in the view." );
    }
}
?>