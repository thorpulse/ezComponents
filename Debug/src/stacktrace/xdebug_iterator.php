<?php
/**
 * File containing the ezcDebugXdebugStacktraceIterator class.
 *
 * @package Debug
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Iterator class to wrap around debug_backtrace() stack traces.
 *
 * This iterator class receives a stack trace generated by debug_backtrace()
 * and unifies it as described in the {@link ezcDebugStacktraceIterator}
 * interface.
 * 
 * @package Debug
 * @version //autogen//
 */
class ezcDebugXdebugStacktraceIterator extends ezcDebugStacktraceIterator
{
    /**
     * Prepares the stack trace for being stored in the iterator instance.
     *
     * This method reverses the received $stackTrace, which was created by the
     * Xdebug ({@link http://xdebug.org}) debugging extension for PHP, to unify
     * the stacktrace with the one created by {@link
     * ezcDebugPhpStacktraceIterator}. Number $removeElements are removed from
     * the top of the $stackTrace.
     * 
     * @param array $stackTrace 
     * @param int $removeElements
     * @return array The stack trace to store.
     */
    protected function prepare( $stackTrace, $removeElements )
    {
        return parent::prepare(
            array_reverse( $stackTrace ),
            $removeElements
        );
    }

    /**
     * Unifies a stack element for being returned to the formatter.
     *
     * This method ensures that an element of the stack trace conforms to the
     * format expected by a {@link ezcDebugOutputFormatter}. The format is
     * defined as follows:
     *
     * <code>
     * array(
     *      'file'      => '<fullpathtofile>',
     *      'line'      => <lineno>,
     *      'function'  => '<functionname>',
     *      'class'     => '<classname>',
     *      'params'    => array(
     *          <param_no> => '<paramvalueinfo>',
     *          <param_no> => '<paramvalueinfo>',
     *          <param_no> => '<paramvalueinfo>',
     *          ...
     *      )
     * )
     * </code>
     * 
     * @param mixed $stackElement 
     * @return array As described above.
     */
    protected function unifyStackElement( $stackElement )
    {
        $newParams = array();
        foreach ( $stackElement['params'] as $param )
        {
            $newParams[] = $param;
        }
        $stackElement['params'] = $newParams;
        
        return $stackElement;
    }
}

?>
