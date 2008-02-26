<?php
/**
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Search
 * @subpackage Tests
 */

/**
 * Test the handler classes.
 *
 * @package Search
 * @subpackage Tests
 */
class ezcSearchHandlerSolrTest extends ezcTestCase
{
    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcSearchHandlerSolrTest" );
    }

    function setUp()
    {
        try
        {
            $this->solr = new ezcSearchSolrHandler;
        }
        catch ( ezcSearchCanNotConnectException $e )
        {
            self::markTestSkipped( 'Solr is not running.' );
        }
        $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ),
                '<delete><query>timestamp:[* TO *]</query></delete>' );
        $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ),
                '<commit/>' );
    }

    function testUnableToConnect()
    {
        try
        {
            $s = new ezcSearchSolrHandler( 'localhost', 58983 );
            $r = $s->sendRawGetCommand( 'admin/ping' );
            self::fail( 'Expected exception not thrown.' );
        }
        catch ( ezcSearchCanNotConnectException $e )
        {
            self::assertEquals( "Could not connect to 'solr' at 'http://localhost:58983/solr'.", $e->getMessage() );
        }
    }

    function testConnectAndPing()
    {
        $r = $this->solr->sendRawGetCommand( 'admin/ping' );
        self::assertContains( "<ping", $r );
    }

    function testSearchEmptyResultsSimple()
    {
        $r = $this->solr->sendRawGetCommand( 'select', array( 'q' => 'solr', 'wt' => 'json', 'df' => 'name_s' ) );
        $r = json_decode( $r );
        self::assertEquals( 0, $r->response->numFound );
    }

    function testSearchEmptyResults()
    {
        $r = $this->solr->search( 'solr', 'name_s' );
        self::assertType( 'ezcSearchResult', $r );
        self::assertEquals( 0, $r->resultCount );
        self::assertEquals( 0, $r->start );
        self::assertEquals( 0, $r->status );
    }

    function testSimpleIndex()
    {
        $r = $this->solr->sendRawGetCommand( 'select', array( 'q' => 'solr', 'wt' => 'json', 'df' => 'name_s' ) );
        $r = json_decode( $r );
        self::assertEquals( 0, $r->response->numFound );

        $r = $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ), '<add><doc><field name="id">cfe5cc06-9b07-4e4b-930e-7e99f5202570</field><field name="name_s">solr</field></doc></add>' );
        $r = $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ), '<commit/>' );

        $r = $this->solr->sendRawGetCommand( 'select', array( 'q' => 'solr', 'wt' => 'json', 'df' => 'name_s' ) );
        $r = json_decode( $r );
        self::assertEquals( 1, $r->response->numFound );

        $r = $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ), '<delete><id>cfe5cc06-9b07-4e4b-930e-7e99f5202570</id></delete>' );
        $r = $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ), '<commit/>' );

        $r = $this->solr->sendRawGetCommand( 'select', array( 'q' => 'solr', 'wt' => 'json', 'df' => 'name_s' ) );
        $r = json_decode( $r );
        self::assertEquals( 0, $r->response->numFound );
    }

    function testSimpleIndexWithSearch()
    {
        $r = $this->solr->search( 'solr', 'name_s' );
        self::assertEquals( 0, $r->resultCount );

        $r = $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ), '<add><doc><field name="id">cfe5cc06-9b07-4e4b-930e-7e99f5202570</field><field name="name_s">solr</field></doc></add>' );
        $r = $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ), '<commit/>' );

        $r = $this->solr->search( 'solr', 'name_s', array( 'id', 'name_s' ), array( 'id', 'name_s', 'score' ) );
        self::assertEquals( 1, $r->resultCount );

        $r = $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ), '<delete><id>cfe5cc06-9b07-4e4b-930e-7e99f5202570</id></delete>' );
        $r = $this->solr->sendRawPostCommand( 'update', array( 'wt' => 'json' ), '<commit/>' );

        $r = $this->solr->search( 'solr', 'name_s' );
        self::assertEquals( 0, $r->resultCount );
    }
}

?>
