<?php
/**
 * ezcDocumentRstParserTests
 * 
 * @package Document
 * @version //autogen//
 * @subpackage Tests
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

require_once 'rst_dummy_directives.php';

/**
 * Test suite for class.
 * 
 * @package Document
 * @subpackage Tests
 */
class ezcDocumentRstXhtmlVisitorTests extends ezcTestCase
{
    protected static $testDocuments = null;

    public static function suite()
    {
        return new PHPUnit_Framework_TestSuite( __CLASS__ );
    }

    public static function getTestDocuments()
    {
        if ( self::$testDocuments === null )
        {
            // Get a list of all test files from the respektive folder
            $testFiles = glob( dirname( __FILE__ ) . '/files/rst/xhtml/s_*.txt' );

            // Create array with the test file and the expected result file
            foreach ( $testFiles as $file )
            {
                self::$testDocuments[] = array(
                    $file,
                    substr( $file, 0, -3 ) . 'html'
                );
            }
        }

        return self::$testDocuments;
        return array_slice( self::$testDocuments, 12, 1 );
    }

    public function testDifferentDoctypeXml()
    {
        $from = dirname( __FILE__ ) . '/files/rst/xhtml/s_003_simple_text.txt';
        $to   = dirname( __FILE__ ) . '/files/rst/xhtml/s_003_simple_text_no_xml.html';

        $document = new ezcDocumentRst();
        $document->options->errorReporting = E_PARSE | E_ERROR | E_WARNING;

        $document->registerDirective( 'my_custom_directive', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'user', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'book', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'function', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'replace', 'ezcDocumentTestDummyXhtmlDirective' );

        $document->loadFile( $from );

        $html = $document->getAsXhtml();
        $html->options->xmlHeader = true;
        $xml = $html->save();

        // Store test file, to have something to compare on failure
        $tempDir = $this->createTempDir( 'html_' ) . '/';
        file_put_contents( $tempDir . basename( $to ), $xml );

        $this->assertTrue(
            ( $errors = $html->validateString( $xml ) ) === true,
            ( is_array( $errors ) ? implode( PHP_EOL, $errors ) : 'Expected true' )
        );

        $this->assertEquals(
            file_get_contents( $to ),
            $xml,
            'Document not visited as expected.'
        );

        // Remove tempdir, when nothing failed.
        $this->removeTempDir();
    }

    public function testDocumentWithStylesheets()
    {
        $from = dirname( __FILE__ ) . '/files/rst/xhtml/s_003_simple_text.txt';
        $to   = dirname( __FILE__ ) . '/files/rst/xhtml/s_003_simple_text_stylesheets.html';

        $document = new ezcDocumentRst();
        $document->options->errorReporting = E_PARSE | E_ERROR | E_WARNING;
        $document->options->xhtmlVisitorOptions->styleSheets = array(
            'foo.css',
            'http://example.org/bar.css',
        );

        $document->registerDirective( 'my_custom_directive', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'user', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'book', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'function', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'replace', 'ezcDocumentTestDummyXhtmlDirective' );

        $document->loadFile( $from );

        $html = $document->getAsXhtml();
        $html->options->xmlHeader = true;
        $xml = $html->save();

        // Store test file, to have something to compare on failure
        $tempDir = $this->createTempDir( 'html_' ) . '/';
        file_put_contents( $tempDir . basename( $to ), $xml );

        $this->assertTrue(
            ( $errors = $html->validateString( $xml ) ) === true,
            ( is_array( $errors ) ? implode( PHP_EOL, $errors ) : 'Expected true' )
        );

        $this->assertEquals(
            file_get_contents( $to ),
            $xml,
            'Document not visited as expected.'
        );

        // Remove tempdir, when nothing failed.
        $this->removeTempDir();
    }

    /**
     * @dataProvider getTestDocuments
     */
    public function testParseRstFile( $from, $to )
    {
        if ( !is_file( $to ) )
        {
            $this->markTestSkipped( "Comparision file '$to' not yet defined." );
        }

        $document = new ezcDocumentRst();
        $document->options->errorReporting = E_PARSE | E_ERROR | E_WARNING;

        $document->registerDirective( 'my_custom_directive', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'user', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'book', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'function', 'ezcDocumentTestDummyXhtmlDirective' );
        $document->registerDirective( 'replace', 'ezcDocumentTestDummyXhtmlDirective' );

        $document->loadFile( $from );

        $html = $document->getAsXhtml();
        $xml = $html->save();

        // Store test file, to have something to compare on failure
        $tempDir = $this->createTempDir( 'html_' ) . '/';
        file_put_contents( $tempDir . basename( $to ), $xml );

        $this->assertTrue(
            ( $errors = $html->validateString( $xml ) ) === true,
            ( is_array( $errors ) ? implode( PHP_EOL, $errors ) : 'Expected true' )
        );

        $this->assertEquals(
            file_get_contents( $to ),
            $xml,
            'Document not visited as expected.'
        );

        // Remove tempdir, when nothing failed.
        $this->removeTempDir();
    }
}

?>
