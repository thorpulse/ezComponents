<?php
/**
 * File containing the ezcTreeVisitorPlainText class.
 *
 * @package Tree
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An implementation of the ezcTreeVisitor interface that generates
 * a plain text representatation of a tree structure.
 *
 * <code>
 * <?php
 *     $visitor = new ezcTreeVisitorPlainText( ezcTreeVisitorPlainText::SYMBOL_UTF8 );
 *     $tree->accept( $visitor );
 *     echo (string) $visitor; // print the plot
 * ?>
 * </code>
 *
 * Shows (something like):
 * <code>
 * Hylobatidae
 * ├─Hylobates
 * │ ├─Lar Gibbon
 * │ ├─Agile Gibbon
 * │ ├─Müller's Bornean Gibbon
 * │ ├─Silvery Gibbon
 * │ ├─Pileated Gibbon
 * │ └─Kloss's Gibbon
 * ├─Hoolock
 * │ ├─Western Hoolock Gibbon
 * │ └─Eastern Hoolock Gibbon
 * ├─Symphalangus
 * └─Nomascus
 *   ├─Black Crested Gibbon
 *   ├─Eastern Black Crested Gibbon
 *   ├─White-cheecked Crested Gibbon
 *   └─Yellow-cheecked Gibbon
 * </code>
 *
 * @package Tree
 * @version //autogen//
 */
class ezcTreeVisitorPlainText implements ezcTreeVisitor
{
    /**
     * Represents the ASCII symbol set
     */
    const SYMBOL_ASCII = 1;

    /**
     * Represents the UTF-8 symbol set
     */
    const SYMBOL_UTF8 = 2;

    /**
     * Holds all the edges of the graph.
     *
     * @var array(string=>array(string))
     */
    protected $edges = array();

    /**
     * Holds the root ID
     *
     * @var string
     */
    protected $root = null;

    /**
     * Constructs a new ezcTreeVisitorPlainText visualizer using $symbolCharset as character set
     *
     * This class only supports 'ascii' and 'utf-8' as character sets.
     * @see SYMBOL_UTF8
     * @see SYMBOL_ASCII
     *
     * @param int $symbolCharset
     */
    public function __construct( $symbolCharset = self::SYMBOL_UTF8 )
    {
        switch( $symbolCharset )
        {
            case self::SYMBOL_ASCII:
                $symbols = array( '|', '+', '-', '+' );
                break;
            case self::SYMBOL_UTF8:
            default:
                $symbols = array( '│', '├', '─', '└' );
        }
        $this->symbolPipe   = $symbols[0];
        $this->symbolTee    = $symbols[1];
        $this->symbolLine   = $symbols[2];
        $this->symbolCorner = $symbols[3];
    }

    /**
     * Visits the node and sets the the member variables according to the node
     * type and contents.
     *
     * @param ezcTreeVisitable $visitable
     * @return boolean
     */
    public function visit( ezcTreeVisitable $visitable )
    {
        if ( $visitable instanceof ezcTree )
        {
        }

        if ( $visitable instanceof ezcTreeNode )
        {
            if ( $this->root === null )
            {
                $this->root = $visitable->id;
            }

            $parent = $visitable->fetchParent();
            if ( $parent )
            {
                $this->edges[$parent->id][] = $visitable->id;
            }
        }

        return true;
    }

    /**
     * Loops over the children of the node with ID $id.
     *
     * This methods loops over all the node's children and adds the correct
     * layout for each node depending on the state that is collected in the
     * $level and $levelLast variables.
     *
     * @param string $id
     * @param int    $level
     * @param array(int=>bool) $levelLast
     *
     * @return string
     */
    private function doChildren( $id, $level = 0, $levelLast = array() )
    {
        $text = '';

        if ( !isset( $this->edges[$id] ) )
        {
            return $text;
        }
        $children = $this->edges[$id];
        $numChildren = count( $children );

        $count = 0;
        foreach ( $children as $child )
        {
            $count++;
            for ( $i = 0; $i < $level; $i++ )
            {
                if ( isset( $levelLast[$i] ) && $levelLast[$i] )
                {
                    $text .= '  ';
                }
                else
                {
                    $text .= "{$this->symbolPipe} ";
                }
            }
            if ( $count != $numChildren )
            {
                $text .= "{$this->symbolTee}{$this->symbolLine}";
                $levelLast[$level] = false;
            }
            else
            {
                $text .= "{$this->symbolCorner}{$this->symbolLine}";
                $levelLast[$level] = true;
            }
            $text .= $child . "\n";
            $text .= $this->doChildren( $child, $level + 1, $levelLast );
        }

        return $text;
    }

    /**
     * Returns a text representatation of a tree.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        $tree = $this->root . "\n";
        $tree .= $this->doChildren( $this->root );
        return $tree;
    }
}
?>
