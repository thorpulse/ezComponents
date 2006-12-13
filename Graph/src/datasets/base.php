<?php
/**
 * File containing the abstract ezcGraphDataSet class
 *
 * @package Graph
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Basic class to contain the charts data
 *
 * @package Graph
 * @access private
 */
abstract class ezcGraphDataSet implements ArrayAccess, Iterator, Countable
{

    /**
     * labels for datapoint and datapoint elements
     * 
     * @var ezcGraphDataSetStringProperty
     */
    protected $label;

    /**
     * Colors for datapoint elements
     * 
     * @var ezcGraphDataSetColorProperty
     */
    protected $color;

    /**
     * Symbols for datapoint elements
     * 
     * @var ezcGraphDataSetIntProperty
     */
    protected $symbol;

    /**
     * Status if datapoint element is hilighted
     * 
     * @var ezcGraphDataSetBooleanProperty
     * @access protected
     */
    protected $highlight;

    /**
     * Display type of chart data.
     * 
     * @var integer
     */
    protected $displayType;

    /**
     * Array which contains the data of the datapoint
     * 
     * @var array
     */
    protected $data;

    /**
     * Current datapoint element
     * needed for iteration over datapoint with ArrayAccess
     * 
     * @var mixed
     */
    protected $current;

    /**
     * Color palette used for datapoint colorization
     * 
     * @var ezcGraphPalette
     */
    protected $pallet;

    /**
     * Constructor
     * 
     * @param array $options Default option array
     * @return void
     * @ignore
     */
    public function __construct()
    {
        $this->label = new ezcGraphDataSetStringProperty( $this );
        $this->color = new ezcGraphDataSetColorProperty( $this );
        $this->symbol = new ezcGraphDataSetIntProperty( $this );
        $this->highlight = new ezcGraphDataSetBooleanProperty( $this );
        $this->displayType = new ezcGraphDataSetIntProperty( $this );

        $this->highlight->default = false;
    }

    /**
     * Options write access
     * 
     * @throws ezcBasePropertyNotFoundException
     *          If Option could not be found
     * @throws ezcBaseValueException
     *          If value is out of range
     * @param mixed $propertyName   Option name
     * @param mixed $propertyValue  Option value;
     * @return void
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'label':
                $this->label->default = $propertyValue;
                break;
            case 'color':
                $this->color->default = $propertyValue;
                break;
            case 'symbol':
                $this->symbol->default = $propertyValue;
                break;
            case 'highlight':
            case 'hilight':
                $this->highlight->default = $propertyValue;
                break;
            case 'displayType':
                $this->displayType->default = $propertyValue;
                break;
            case 'palette':
                $this->palette = $propertyValue;
                $this->color->default = $this->palette->dataSetColor;
                $this->symbol->default = $this->palette->dataSetSymbol;
                break;
        }
    }

    /**
     * Property get access.
     * Simply returns a given option.
     * 
     * @param string $propertyName The name of the option to get.
     * @return mixed The option value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property options is not an instance of
     */
    public function __get( $propertyName )
    {
        if ( isset( $this->$propertyName ) )
        {
            return $this->$propertyName;
        }
        else 
        {
            throw new ezcBasePropertyNotFoundException( $propertyName );
        }
    }
    
    /**
     * Returns true if the given datapoint exists
     * Allows isset() using ArrayAccess.
     * 
     * @param string $key The key of the datapoint to get.
     * @return bool Wether the key exists.
     */
    public function offsetExists( $key )
    {
        return isset( $this->data[$key] );
    }

    /**
     * Returns the value for the given datapoint
     * Get an datapoint value by ArrayAccess.
     * 
     * @param string $key The key of the datapoint to get.
     * @return float The datapoint value.
     */
    public function offsetGet( $key )
    {
        return $this->data[$key];
    }

    /**
     * Sets the value for a datapoint.
     * Sets an datapoint using ArrayAccess.
     * 
     * @param string $key The kex of a datapoint to set.
     * @param float $value The value for the datapoint.
     * @return void
     */
    public function offsetSet( $key, $value )
    {
        $this->data[$key] = (float) $value;
    }

    /**
     * Unset an option.
     * Unsets an option using ArrayAccess.
     * 
     * @param string $key The options to unset.
     * @return void
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property options is not an instance of
     * @throws ezcBaseValueException
     *         If a the value for a property is out of range.
     */
    public function offsetUnset( $key )
    {
        unset( $this->data[$key] );
    }

    /**
     * Returns the currently selected datapoint.
     *
     * This method is part of the Iterator interface to allow access to the 
     * datapoints of this row by iterating over it like an array (e.g. using
     * foreach).
     * 
     * @return string The currently selected datapoint.
     */
    public function current()
    {
        $keys = array_keys( $this->data );
        if ( !isset( $this->current ) )
        {
            $this->current = 0;
        }

        return $this->data[$keys[$this->current]];
    }

    /**
     * Returns the next datapoint and selects it or false on the last datapoint.
     *
     * This method is part of the Iterator interface to allow access to the 
     * datapoints of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @return float datapoint if it exists, or false.
     */
    public function next()
    {
        $keys = array_keys( $this->data );
        if ( ++$this->current >= count( $keys ) )
        {
            return false;
        }
        else 
        {
            return $this->data[$keys[$this->current]];
        }
    }

    /**
     * Returns the key of the currently selected datapoint.
     *
     * This method is part of the Iterator interface to allow access to the 
     * datapoints of this row by iterating over it like an array (e.g. using
     * foreach).
     * 
     * @return string The key of the currently selected datapoint.
     */
    public function key()
    {
        $keys = array_keys( $this->data );
        return $keys[$this->current];
    }

    /**
     * Returns if the current datapoint is valid.
     *
     * This method is part of the Iterator interface to allow access to the 
     * datapoints of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @return bool If the current datapoint is valid
     */
    public function valid()
    {
        $keys = array_keys( $this->data );
        return isset( $keys[$this->current] );
    }

    /**
     * Selects the very first datapoint and returns it.
     * This method is part of the Iterator interface to allow access to the 
     * datapoints of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @return float The very first datapoint.
     */
    public function rewind()
    {
        $this->current = 0;
    }
}

?>
