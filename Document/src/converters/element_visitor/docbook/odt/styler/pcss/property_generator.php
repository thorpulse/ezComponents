<?php
/**
 * File containing the abstract ezcDocumentOdtStylePropertyGenerator base class.
 *
 * @package Document
 * @version //autogen//
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Base class for property generators.
 *
 * A property generator creates a certain style property and is capable of 
 * applying the style information handled by this property type Base class for 
 * property generators.
 *
 * @package Document
 * @access private
 * @version //autogen//
 */
abstract class ezcDocumentOdtStylePropertyGenerator
{
    /**
     * List of CSS style names to apply to the property. 
     * 
     * @var array(string)
     */
    protected $styleAttributes = array();

    /**
     * Style converters to be used. 
     * 
     * @var ezcDocumentOdtPcssConverterManager
     */
    protected $styleConverters;

    /**
     * Creates a new property generator.
     *
     * Must be overwritten by the actual implementation to fill the list of 
     * $styles to be applied to the property.
     */
    public function __construct( ezcDocumentOdtPcssConverterManager $styleConverters, array $styleAttributes )
    {
        $this->styleConverters = $styleConverters;
        $this->styleAttributes = $styleAttributes;
    }

    /**
     * Creates the style property from the attributes in $style.
     *
     * Creates the property generated by the specific style generator and 
     * renders all suitable styling attributes in $style into this property. 
     * The method {@link applyStyleAttributes()} can be used for easy 
     * application of all styles registered in the $styleAttributes property.
     * 
     * @param DOMElement $parent 
     * @param array $styles 
     * @return DOMElement The created property
     */
    public abstract function createProperty( DOMElement $parent, array $styles );

    /**
     * Applies corresponding style attributes to the given property.
     * 
     * @param DOMElement $property 
     */
    protected function applyStyleAttributes( DOMElement $property, array $styles )
    {
        foreach ( $this->styleAttributes as $handledStyleName )
        {
            if ( isset( $styles[$handledStyleName] ) && isset( $this->styleConverters[$handledStyleName] ) )
            {
                $this->styleConverters[$handledStyleName]->convert(
                    $property,
                    $handledStyleName,
                    $styles[$handledStyleName]
                );
            }
        }
    }
}

?>
