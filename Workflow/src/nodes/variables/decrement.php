<?php
/**
 * File containing the ezcWorkflowNodeVariableDecrement class.
 *
 * @package Workflow
 * @version //autogen//
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This node decrements a workflow variable by one when executed.
 *
 * <code>
 * <?php
 * $dec = new ezcWorkflowNodeVariableDecrement( 'variable name' );
 * ?>
 * </code>
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * @package Workflow
 * @version //autogen//
 */
class ezcWorkflowNodeVariableDecrement extends ezcWorkflowNodeArithmeticBase
{
    /**
     * The name of the variable to be decremented.
     *
     * @var string
     */
    protected $configuration;

    /**
     * Perform variable modification.
     */
    protected function doExecute()
    {
        $this->variable--;
    }

    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @return string
     * @ignore
     */
    public static function configurationFromXML( DOMElement $element )
    {
        return $element->getAttribute( 'variable' );
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML( DOMElement $element )
    {
        $element->setAttribute( 'variable', $this->configuration );
    }

    /**
     * Returns a textual representation of this node.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return $this->configuration . '--';
    }
}
?>
