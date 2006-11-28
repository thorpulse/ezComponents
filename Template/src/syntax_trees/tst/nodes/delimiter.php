<?php
/**
 * File containing the ezcTemplateDelimiterTstNode class
 *
 * @package Template
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Control structure: foreach.
 *
 * @package Template
 * @version //autogen//
 * @access private
 */
class ezcTemplateDelimiterTstNode extends ezcTemplateBlockTstNode
{
    public $modulo;
    public $rest;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->modulo = null;
        $this->rest = null;
        $this->name = 'delimiter';
    }

    public function getTreeProperties()
    {
        return array( 'name'             => $this->name,
                      'isClosingBlock'   => $this->isClosingBlock,
                      'isNestingBlock'   => $this->isNestingBlock,
                      'modulo'           => $this->modulo,
                      'rest'             => $this->rest );
    }

    public function handleElement( ezcTemplateTstNode $element )
    {
        parent::handleElement( $element );
    }

    public function canAttachToParent( $parentElement )
    {
        // Process the lot.
        // Must at least have one parent with foreach or while.

        $p = $parentElement;

        while( !$p instanceof ezcTemplateProgramTstNode )
        {
            if( $p instanceof ezcTemplateForeachLoopTstNode || $p instanceof ezcTemplateWhileLoopTstNode )
            {
                return; // Perfect, we are inside a loop.
            }

            $p = $p->parentBlock;
        }


        throw new ezcTemplateParserException( $this->source, $this->startCursor, $this->startCursor, 
            "{" . $this->name . "} can only be a child of an {foreach} or a {while} block." );
    }
}
?>
