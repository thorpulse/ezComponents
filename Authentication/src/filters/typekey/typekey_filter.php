<?php
/**
 * File containing the ezcAuthenticationTypekeyFilter class.
 *
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version //autogen//
 */

/**
 * Filter to authenticate against TypeKey.
 *
 * The filter deals with the validation of information returned by the TypeKey
 * server in response to a login command.
 *
 * In order to access a protected page, user logs in by using a request like:
 *   https://www.typekey.com/t/typekey/login?
 *     t=391jbj25WAQANzJrKvb5&_return=http://example.com/login.php
 * (link split on two rows for clarity),
 * where:
 *   t = TypeKey token generated for each TypeKey account.
 *       It is found at https://www.typekey.com/t/typekey/prefs.
 *       This value is also used as a session key, so it must be passed to the
 *       page performing the TypeKey authentication via the _return URL.
 *   _return = the URL where to return after user logs in with his TypeKey
 *             username and password.
 *             The URL can contain query arguments, such as the value t which
 *             can be used as a session key.
 *
 * The login link can also contain these 2 optional values:
 *   v = TypeKey version to use. Default is 1.
 *   need_email = the mail address which was used to register with TypeKey.
 *
 * So the TypeKey authentication filter will run in the _return page and will
 * verify the signature and the other information in the URL.
 *
 * The application link (eg. http://example.com) must be registered in the
 * TypeKey preferences page (https://www.typekey.com/t/typekey/prefs) in one
 * of the 5 lines from "Your Weblog Preferences", otherwise TypeKey will
 * not accept the login request.
 *
 * The link returned by TypeKey after user logs in with his TypeKey username
 * and password looks like this:
 *
 * http://example.com/typekey.php?
 *   ts=1177319974&email=5098f1e87a608675ded4d933f31899cae6b4f968&
 *   name=ezc&nick=ezctest&
 *   sig=I9Dop72+oahY82bpL7ymBoxdQ+k=:Vj/t7oZVL2zMSzwHzdOWop5NG/g=
 * (link split on four rows for clarity),
 * where:
 *   ts = timestamp (in seconds) of the TypeKey server time at login.
 *        The TypeKey filter compares this timestamp with the application
 *        server's timestamp to make sure the login is in a reasonable
 *        time window (specified by the validity option). Don't use a too small
 *        value for validity, because servers are not always synchronized.
 *   email = sha1 hash of "mailto:{$mail}", where $mail is the mail address
 *           used to register with TypeKey.
 *   nick = TypeKey nickname/display name.
 *   sig = signature which must be validated by the TypeKey filter.
 *
 * For more information on the login request and the TypeKey response link see
 * {@link http://www.sixapart.com/typekey/api}.
 *
 * Example:
 * <code>
 * <?php
 * // no headers should be sent before calling $session->start()
 * $session = new ezcAuthenticationSessionFilter();
 * $session->start();
 * 
 * // $token is used as a key in the session to store the authenticated state between requests
 * $token = isset( $_GET['token'] ) ? $_GET['token'] : $session->load();
 * 
 * $credentials = new ezcAuthenticationIdCredentials( $token );
 * $authentication = new ezcAuthentication( $credentials );
 * $authentication->session = $session;
 * 
 * $filter = new ezcAuthenticationTypekeyFilter();
 * $authentication->addFilter( $filter );
 * // add other filters if needed
 *
 * if ( !$authentication->run() )
 * {
 *     echo "<b>Not logged-in</b>. ";
 *     // authentication did not succeed, so inform the user
 *     $status = $authentication->getStatus();
 *     for ( $i = 0; $i < count( $status ); $i++ )
 *     {
 *         list( $key, $value ) = each( $status[$i] );
 *         switch ( $key )
 *         {
 *             case 'ezcAuthenticationTypekeyFilter':
 *                 if ( $value === ezcAuthenticationTypekeyFilter::STATUS_SIGNATURE_INCORRECT )
 *                 {
 *                     echo "Signature returned by TypeKey is incorrect.";
 *                 }
 *                 if ( $value === ezcAuthenticationTypekeyFilter::STATUS_SIGNATURE_EXPIRED )
 *                 {
 *                     echo "Did not login in a reasonable amount of time. The application server and the TypeKey server might be desynchronized.";
 *                 }
 *                 break;
 * 
 *             case 'ezcAuthenticationSessionFilter':
 *                 if ( $value === ezcAuthenticationSessionFilter::STATUS_EXPIRED )
 *                 {
 *                     echo "Session expired.";
 *                 }
 *                 if ( $value === ezcAuthenticationSessionFilter::STATUS_EMPTY )
 *                 {
 *                     echo "Session empty.";
 *                 }
 *                 break;
 *         }
 *     }
 * ?>
 * <!-- OnSubmit hack to append the value of t to the _return value, to pass
 *      the TypeKey token after the TypeKey request -->
 * <form method="GET" action="https://www.typekey.com/t/typekey/login" onsubmit="document.getElementById('_return').value += '?token=' + document.getElementById('t').value;">
 * TypeKey token: <input type="text" name="t" id="t" />
 * <input type="hidden" name="_return" id="_return" value="http://localhost/typekey.php" />
 * <input type="submit" />
 * </form>
 * <?
 * }
 * else
 * {
 *     // authentication succeeded, so allow the user to see his content
 *     echo "<b>Logged-in</b>";
 * }
 * ?>
 * </code>
 *
 * Another method, which doesn't use JavaScript, is using an intermediary page
 * which saves the token in the session, then calls the TypeKey login page:
 *
 * - original file is modified as follows:
 * <code>
 * <form method="GET" action="save_typekey.php">
 * TypeKey token: <input type="text" name="t" id="t" />
 * <input type="hidden" name="_return" id="_return" value="http://localhost/typekey.php" />
 * <input type="submit" />
 * </form>
 * </code>
 *
 * - intermediary page:
 * <code>
 * <?php
 * // no headers should be sent before calling $session->start()
 * $session = new ezcAuthenticationSessionFilter();
 * $session->start();
 *
 * // $token is used as a key in the session to store the authenticated state between requests
 * $token = isset( $_GET['t'] ) ? $_GET['t'] : $session->load();
 * if ( $token !== null )
 * {
 *     $session->save( $token );
 * }
 * $url = isset( $_GET['_return'] ) ? $_GET['_return'] : null;
 * $url .= "?token={$token}";
 * header( "Location: https://www.typekey.com/t/typekey/login?t={$token}&_return={$url}" );
 * ?>
 * </code>
 *
 * @property ezcAuthenticationBignumLibrary $lib
 *           The wrapper for the PHP extension to use for big number operations.
 *           This will be autodetected in the constructor, but you can specify
 *           your own wrapper before calling run().
 *           
 * @package Authentication
 * @version //autogen//
 * @mainclass
 */
class ezcAuthenticationTypekeyFilter extends ezcAuthenticationFilter
{
    /**
     * The request does not contain the needed information (like $_GET['sig']).
     */
    const STATUS_SIGNATURE_MISSING = 1;

    /**
     * Signature verification was incorect.
     */
    const STATUS_SIGNATURE_INCORRECT = 2;

    /**
     * Login is outside of the timeframe.
     */
    const STATUS_SIGNATURE_EXPIRED = 3;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Creates a new object of this class.
     *
     * @throws ezcBaseExtensionNotFoundException
     *         if neither of the PHP gmp and bcmath extensions are installed
     * @param ezcAuthenticationTypekeyOptions $options Options for this class
     */
    public function __construct( ezcAuthenticationTypekeyOptions $options = null )
    {
        $this->options = ( $options === null ) ? new ezcAuthenticationTypekeyOptions() : $options;
        $this->lib = ezcAuthenticationMath::createBignumLibrary();
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'lib':
                if ( $value instanceof ezcAuthenticationBignumLibrary )
                {
                    $this->properties[$name] = $value;
                }
                else
                {
                    throw new ezcBaseValueException( $name, $value, 'instance of ezcAuthenticationBignumLibrary' );
                }
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'lib':
                return $this->properties[$name];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'lib':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Runs the filter and returns a status code when finished.
     *
     * @throws ezcAuthenticationTypekeyException
     *         if the keys from the TypeKey public keys file could not be fetched
     * @param ezcAuthenticationIdCredentials $credentials Authentication credentials
     * @return int
     */
    public function run( $credentials )
    {
        if ( isset( $_GET['name'] ) && isset( $_GET['email'] ) && isset( $_GET['nick'] ) && isset( $_GET['ts'] ) && isset( $_GET['sig'] ) )
        {
            // parse the response URL sent by the TypeKey server
            $id = isset( $_GET['name'] ) ? $_GET['name'] : null;
            $mail = isset( $_GET['email'] ) ? $_GET['email'] : null;
            $nick = isset( $_GET['nick'] ) ? $_GET['nick'] : null;
            $timestamp = isset( $_GET['ts'] ) ? $_GET['ts'] : null;
            $signature = isset( $_GET['sig'] ) ? $_GET['sig'] : null;
        }
        else
        {
            return self::STATUS_SIGNATURE_MISSING;
        }
        if ( $this->options->validity !== 0 &&
             time() - $timestamp >= $this->options->validity )
        {
            return self::STATUS_SIGNATURE_EXPIRED;
        }
        $keys = $this->fetchPublicKeys( $this->options->keysFile );
        $msg = "{$mail}::{$id}::{$nick}::{$timestamp}";
        $signature = rawurldecode( urlencode( $signature ) );
        list( $r, $s ) = explode( ':', $signature );
        if ( $this->checkSignature( $msg, $r, $s, $keys ) )
        {
            return self::STATUS_OK;
        }
        return self::STATUS_SIGNATURE_INCORRECT;
    }

    /**
     * Fetches the public keys from the specified file or URL $file.
     *
     * The file must be composed of space-separated values for p, g, q, and
     * pub_key, like this:
     *   p=<value> g=<value> q=<value> pub_key=<value>
     *
     * The format of the returned array is:
     * <code>
     *   array( 'p' => p_val, 'g' => g_val, 'q' => q_val, 'pub_key' => pub_key_val )
     * </code>
     *
     * @todo file_exist() and is_readable() tests before reading from $file
     *       Question: are this tests reliable for URLs also? accessing a broken
     *       URL results in a 404 document which exists and is readable.
     *
     * @throws ezcAuthenticationTypekeyException
     *         if the keys from the TypeKey public keys file could not be fetched
     * @return array(string=>string)
     */
    protected function fetchPublicKeys( $file )
    {
        $data = @file_get_contents( $file );
        if ( empty( $data ) )
        {
            throw new ezcAuthenticationTypekeyException( "Could not fetch public keys from '{$file}'." );
        }
        $lines = explode( ' ', trim( $data ) );
        foreach ( $lines as $line )
        {
            $val = explode( '=', $line );
            if ( count( $val ) < 2 )
            {
                throw new ezcAuthenticationTypekeyException( "The data retrieved from '{$file}' is invalid." );
            }
            $keys[$val[0]] = $val[1];
        }
        return $keys;
    }

    /**
     * Checks the information returned by the TypeKey server.
     *
     * @param string $msg Plain text signature which needs to be verified
     * @param string $r First part of the signature retrieved from TypeKey
     * @param string $s Second part of the signature retrieved from TypeKey
     * @param array(string=>string) $keys Public keys retrieved from TypeKey
     * @return bool
     */
    protected function checkSignature( $msg, $r, $s, $keys )
    {
        $lib = $this->lib;

        $r = base64_decode( $r );
        $s = base64_decode( $s );

        foreach ( $keys as $key => $value )
        {
            $keys[$key] = $lib->init( (string) $value );
        }

        $s1 = $lib->init( $lib->binToDec( $r ) );
        $s2 = $lib->init( $lib->binToDec( $s ) );

        $w = $lib->invert( $s2, $keys['q'] );

        $msg = $lib->hexToDec( sha1( $msg ) );

        $u1 = $lib->mod( $lib->mul( $msg, $w ), $keys['q'] );
        $u2 = $lib->mod( $lib->mul( $s1, $w ), $keys['q'] );

        $v = $lib->mul( $lib->powmod( $keys['g'], $u1, $keys['p'] ), $lib->powmod( $keys['pub_key'], $u2, $keys['p'] ) );
        $v = $lib->mod( $lib->mod( $v, $keys['p'] ), $keys['q'] );

        return ( $lib->cmp( $v, $s1 ) === 0 );
    }
}
?>
