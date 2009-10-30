<?php
/**
 * File containing the ezcDocumentOdtStyleConverter interface.
 *
 * @package Document
 * @version //autogen//
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Interface for style converters.
 *
 * A style converter handles one or more CSS properties and converts such a 
 * property to an arbitrary combination of ODF style properties on an ODF style 
 * property DOMElement.
 *
 * @package Document
 * @access private
 * @version //autogen//
 */
interface ezcDocumentOdtStyleConverter
{
    /**
     * Convert the given $styleValue and apply it to the $targetProperty.
     *
     * This method receives a $targetProperty DOMElement and converts the given 
     * style with $styleName and $styleValue to attributes on this 
     * $targetProperty.
     * 
     * @param DOMElement $targetProperty 
     * @param string $styleName 
     * @param ezcDocumentPcssStyleValue $styleValue 
     */
    public function convert( DOMElement $targetProperty, $styleName, ezcDocumentPcssStyleValue $styleValue );
}

?>
