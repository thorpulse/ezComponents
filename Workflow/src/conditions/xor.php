<?php
/**
 * File containing the ezcWorkflowConditionXor class.
 *
 * @package Workflow
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Boolean XOR.
 *
 * An object of the ezcWorkflowConditionXor class represents a boolean XOR expression. It
 * can hold an arbitrary number of ezcWorkflowCondition objects.
 *
 * <code>
 *  $xor = new ezcWorkflowConditionXor ( array ( $condition , ... ) ) ;
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class ezcWorkflowConditionXor extends ezcWorkflowConditionBooleanSet
{
    /**
     * @var string
     */
    protected $concatenation = 'XOR';

    /**
     * Evaluates this condition with $value and returns true if the condition holds and false otherwise.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        $result = false;

        foreach ( $this->conditions as $condition )
        {
            if ( $condition->evaluate( $value ) )
            {
                if ( $result )
                {
                    return false;
                }

                $result = true;
            }
        }

        return $result;
    }
}
?>
