<?php
/**
 * File containing the abstract ezcGraphChartElementNumericAxis class
 *
 * @package Graph
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class to represent a axe as a chart element
 *
 * @package Graph
 */
class ezcGraphChartElementNumericAxis extends ezcGraphChartElement
{
    
    /**
     * Minimum value of displayed scale on axis
     * 
     * @var float
     */
    protected $min = false;

    /**
     * Maximum value of displayed scale on axis
     * 
     * @var float
     */
    protected $max = false;

    /**
     * Labeled major steps displayed on the axis 
     * 
     * @var float
     */
    protected $majorStep = false;

    /**
     * Non labeled minor steps on the axis
     * 
     * @var mixed
     * @access protected
     */
    protected $minorStep = false;

    /**
     * Constant used for calculation of automatic definition of major scaling 
     * steps
     */
    const MIN_MAJOR_COUNT = 5;

    /**
     * Constant used for automatic calculation of minor steps from given major 
     * steps 
     */
    const MIN_MINOR_COUNT = 8;

    /**
     * __set 
     * 
     * @param mixed $propertyName 
     * @param mixed $propertyValue 
     * @throws ezcBaseValueException
     *          If a submitted parameter was out of range or type.
     * @throws ezcBasePropertyNotFoundException
     *          If a the value for the property options is not an instance of
     * @return void
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'min':
                $this->min = (float) $propertyValue;
                break;
            case 'max':
                $this->max = (float) $propertyValue;
                break;
            case 'majorStep':
                if ( $propertyValue <= 0 )
                {
                    throw new ezcBaseValueException( 'majorStep', $propertyValue, 'float > 0' );
                }
                $this->majorStep = (float) $propertyValue;
                break;
            case 'minorStep':
                if ( $propertyValue <= 0 )
                {
                    throw new ezcBaseValueException( 'minorStep', $propertyValue, 'float > 0' );
                }
                $this->minorStep = (float) $propertyValue;
                break;
            default:
                parent::__set( $propertyName, $propertyValue );
                break;
        }
    }

    /**
     * Returns a "nice" number for a given floating point number.
     *
     * Nice numbers are steps on a scale which are easily recognized by humans
     * like 0.5, 25, 1000 etc.
     * 
     * @param float $float Number to be altered
     * @return float Nice number
     */
    protected function getNiceNumber( $float )
    {
        // Get absolute value and save sign
        $abs = abs( $float );
        $sign = $float / $abs;

        // Normalize number to a range between 1 and 10
        $log = (int) round( log10( $abs ), 0);
        $abs /= pow( 10, $log );


        // find next nice number
        if ( $abs > 5 ) {
            $abs = 10.;
        }
        elseif ( $abs > 2.5 )
        {
            $abs = 5.;
        }
        elseif ( $abs > 1 )
        {
            $abs = 2.5;
        }
        else
        {
            $abs = 1;
        }

        // unnormalize number to original values
        return $abs * pow( 10, $log ) * $sign;
    }

    /**
     * Calculate minimum value for displayed axe basing on real minimum and
     * major step size
     * 
     * @param float $min Real data minimum 
     * @param float $max Real data maximum
     * @return void
     */
    protected function calculateMinimum( $min, $max )
    {
        $this->min = floor( $min / $this->majorStep ) * $this->majorStep;
    }

    /**
     * Calculate maximum value for displayed axe basing on real maximum and
     * major step size
     * 
     * @param float $min Real data minimum 
     * @param float $max Real data maximum
     * @return void
     */
    protected function calculateMaximum( $min, $max )
    {
        $this->max = ceil( $max / $this->majorStep ) * $this->majorStep;
    }

    /**
     * Calculate size of minor steps based on the size of the major step size
     *
     * @param float $min Real data minimum 
     * @param float $max Real data maximum
     * @return void
     */
    protected function calculateMinorStep( $min, $max )
    {
        $stepSize = $this->majorStep / self::MIN_MINOR_COUNT;
        $this->minorStep = $this->getNiceNumber( $stepSize );
    }

    /**
     * Calculate size of major step based on the span to be displayed and the
     * defined MIN_MAJOR_COUNT constant.
     *
     * @param float $min Real data minimum 
     * @param float $max Real data maximum
     * @return void
     */
    protected function calculateMajorStep( $min, $max )
    {
        $span = $max - $min;
        $stepSize = $span / self::MIN_MAJOR_COUNT;
        $this->majorStep = $this->getNiceNumber( $stepSize );
    }

    /**
     * Calculate steps, min and max values from given datasets, if not set 
     * manually before. receives an array of array( ezcGraphDataset )
     * 
     * @param array $datasets 
     * @return void
     */
    public function calculateFromDataset(array $datasets)
    {
        $min = false;
        $max = false;

        // Determine minimum and maximum values
        foreach ( $datasets as $dataset )
        {
            foreach ( $dataset as $value )
            {
                if ( $min === false ||
                     $value < $min )
                {
                    $min = $value;
                }

                if ( $max === false ||
                     $value > $max )
                {
                    $max = $value;
                }
            }
        }

        // Calculate "nice" values for scaling parameters
        if ( $this->majorStep === false )
        {
            $this->calculateMajorStep( $min, $max );
        }

        if ( $this->minorStep === false )
        {
            $this->calculateMinorStep( $min, $max );
        }

        if ( $this->min === false )
        {
            $this->calculateMinimum( $min, $max );
        }

        if ( $this->max === false )
        {
            $this->calculateMaximum( $min, $max );
        }
    }
    
    /**
     * Render an axe
     * 
     * @param ezcGraphRenderer $renderer 
     * @access public
     * @return void
     */
    public function render( ezcGraphRenderer $renderer, ezcGraphBoundings $boundings )
    {
        return $boundings;
    }

}
