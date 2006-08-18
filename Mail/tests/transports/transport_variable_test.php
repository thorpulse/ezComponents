<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Mail
 * @subpackage Tests
 */

/**
 * @package Mail
 * @subpackage Tests
 */
class ezcMailTransportVariableTest extends ezcTestCase
{
    public static function suite()
    {
         return new ezcTestSuite( "ezcMailTransportVariableTest" );
    }

    public function testOneLine()
    {
        $reference = "Line1";
        $set = new ezcMailVariableSet( $reference );
        $result = '';

        $line = $set->getNextLine();
        while( $line !== null )
        {
            $result .= $line;
            $line = $set->getNextLine();
        }
        $this->assertEquals( $reference, $result );
        $this->assertEquals( false, $set->nextMail() );
    }

    public function testMultiLineCRLF()
    {
        $input = "Line1\r\nLine2";
        $reference = "Line1\nLine2";
        $set = new ezcMailVariableSet( $reference );
        $result = '';

        $line = $set->getNextLine();
        while( $line !== null )
        {
            $result .= $line;
            $line = $set->getNextLine();
            if( $line !== null )
            {
                $result .= "\n";
            }
        }
        $this->assertEquals( $reference, $result );
        $this->assertEquals( false, $set->nextMail() );
    }

    public function testMultiLineLF()
    {
        $reference = "Line1\nLine2";
        $set = new ezcMailVariableSet( $reference );
        $result = '';

        $line = $set->getNextLine();
        while( $line !== null )
        {
            $result .= $line;
            $line = $set->getNextLine();

            if( $line !== null )
            {
                $result .= "\n";
            }
        }
        $this->assertEquals( $reference, $result );
        $this->assertEquals( false, $set->nextMail() );
    }

    public function testFromProcMail()
    {
        $mail_msg = file_get_contents( dirname( __FILE__ ) . '/data/test-variable' );
        $set = new ezcMailVariableSet( $mail_msg );
        $parser = new ezcMailParser();
        $mail = $parser->parseMail( $set );

        // check that we have no extra linebreaks
        $this->assertEquals( "notdisclosed@mydomain.com", $mail[0]->from->email );
    }
}
?>
