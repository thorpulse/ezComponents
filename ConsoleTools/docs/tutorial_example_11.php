<?php

require_once 'tutorial_autoload.php';

$output = new ezcConsoleOutput();

$status = new ezcConsoleStatusindicator( $out, 7 );

$i = 0;
while( $i++ < 7 ) 
{
    usleep( mt_rand( 20000, 2000000 ) );
    $status->addEntry( 'ACTION', "Performed action #{$i}." );
}

$out->outputLine();

?>
