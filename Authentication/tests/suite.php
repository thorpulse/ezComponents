<?php
/**
 * File containing the ezcAuthenticationSuite class.
 *
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version //autogen//
 * @subpackage Tests
 */

/**
 * Including the tests
 */
require_once( "general/authentication_test.php" );
require_once( "filters/group/group_test.php" );
require_once( "filters/htpasswd/htpasswd_test.php" );
require_once( "filters/ldap/ldap_test.php" );
require_once( "filters/openid/openid_test.php" );
require_once( "filters/session/session_test.php" );
require_once( "filters/token/token_test.php" );
require_once( "filters/typekey/typekey_test.php" );
require_once( "math/bignum_test.php" );

/**
 * @package Authentication
 * @version //autogen//
 * @subpackage Tests
 */
class ezcAuthenticationSuite extends PHPUnit_Framework_TestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName( "Authentication" );
        
        $this->addTest( ezcAuthenticationTest::suite() );
        $this->addTest( ezcAuthenticationGroupTest::suite() );
        $this->addTest( ezcAuthenticationHtpasswdTest::suite() );
        $this->addTest( ezcAuthenticationLdapTest::suite() );
        $this->addTest( ezcAuthenticationOpenidTest::suite() );
        $this->addTest( ezcAuthenticationSessionTest::suite() );
        $this->addTest( ezcAuthenticationTokenTest::suite() );
        $this->addTest( ezcAuthenticationTypekeyTest::suite() );
        $this->addTest( ezcAuthenticationBignumTest::suite() );
    }

    public static function suite()
    {
        return new ezcAuthenticationSuite();
    }
}
?>
