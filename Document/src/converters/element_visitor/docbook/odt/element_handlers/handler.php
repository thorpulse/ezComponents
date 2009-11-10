<?php
/**
 * File containing the ezcDocumentDocbookToOdtBaseHandler class.
 *
 * @package Document
 * @version //autogen//
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Base class for ODT visitor handlers.
 *
 * @package Document
 * @version //autogen//
 * @access private
 */
abstract class ezcDocumentDocbookToOdtBaseHandler extends ezcDocumentElementVisitorHandler
{
    /**
     * ODT styler. 
     * 
     * @var ezcDocumentOdtStyler
     */
    protected $styler;

    /**
     * Creates a new handler which utilizes the given $styler. 
     * 
     * @param ezcDocumentOdtStyler $styler 
     */
    public function __construct( ezcDocumentOdtStyler $styler )
    {
        $this->styler = $styler;
    }
}

?>
