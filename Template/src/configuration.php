<?php
/**
 * File containing the ezcTemplateConfiguration class
 *
 * @package Template
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Contains the common configuration options for template managers.
 *
 * Whenever a template source or compiled code is accessed it will use the
 * $templatePath and $compilePath respectively as the base path. The full path
 * is generated by using the value of theses variables and then appending a slash
 * (/) and the subpath, this means it is possible to  have the templates in the
 * root of the filesystem by setting an empty string or a string starting with a
 * slash. 
 
 * The next example demonstrates how the templatePath and compilePath can be set: 
 * <code>
 * // accessing templates in /usr/share and compile them in /var/cache
 * $conf->templatePath = "/usr/share/eztemplate";
 * $conf->compilePath = "/var/cache/eztemplate";
 * </code>
 *
 * Accessing templates from the applications directory is done with a single dot
 * (.), these are also the default values.
 * <code>
 * // uses current directory for accessing templates and compiling them
 * $conf->templatePath = ".";
 * $conf->compilePath = ".";
 * </code>
 *
 * The $context property is by default assigned to an ezcTemplateXhtmlContext object.
 *
 * @property ezcTemplateOutputContext                   $context
 *           Contains the template context.
 * @property string                                     $templatePath
 *           Base path where the source templates are stored.
 * @property string                                     $compilePath
 *           Base path where the compiled templates are stored.
 * @property bool                                       $checkModifiedTemplates
 *           Set to true, to recompile outdated compiled templates.
 * @property bool                                       $executeTemplate
 *           False to only compile the template without executing it.
 * @property string                                     $cachedTemplatesPath
 *           Relative path from the compilePath.
 * @property string                                     $compiledTemplatesPath
 *           Relative path from the compilePath.
 * @property array(ezcTemplateCustomBlockDefinition)    $customBlock
 *           The currently registered custom blocks.
 * @property array(ezcTemplateCustomFunctionDefinition) $customFunctions
 *           The currently registered custom functions.
 * @property ezcTemplateCacheManager                    $cacheManager
 *           The object to use for caching of compile templates.
 * @property ezcTemplateLocator                         $locator
 *           Set the locator object to dynamically translate the template file.
 * @property bool                                       $disableCache
 *           Disable caching for development purposes.
 * @property string                                     $sourceCharset
 *           Charset of the literals in the original template file.
 * @property string                                     $targetCharset
 *           Desired charset of literals in the compiled.
 * @package Template
 * @version //autogen//
 * @mainclass
 */
class ezcTemplateConfiguration
{
    /**
     * List of global instances, looked up using the identifier string.
     *
     * @var array(ezcTemplateConfiguration)
     */
    static private $instanceList = array();

    /**
     * An array containing the properties of this object.
     *
     * templatePath: The base path for all the source templates. e.g. 'design' or 'templates'.
     * compilepath : The base path for all the compiled templates. e.g. 'var/template/compiled'.
     *
     * @var array(string=>mixed)
     */
     private $properties = array( 'context' => false,
                                  'cacheManager' => null,
                                  'disableCache' => false,
                                  'locator' => null,
                                  'templatePath' => ".",
                                  'compilePath' => ".",
                                  'cachedTemplatesPath' => null,
                                  'compiledTemplatesPath' => null,
                                  'checkModifiedTemplates' => true,
                                  'executeTemplate' => true,
                                  'customBlocks' => array(),
                                  'customFunctions' => array(),

                                  'sourceCharset' => "UTF-8",
                                  'targetCharset' => "UTF-8",
                              );
    /**
     * Returns the value of the property $name.
     *
     * @param string $name
     * @param string $name
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'context': 
            case 'cacheManager':
            case 'locator':
            case 'disableCache':
            case 'templatePath': 
            case 'compilePath': 
            case 'cachedTemplatesPath':       // Relative path to the compilePath
            case 'compiledTemplatesPath':     // Relative path to the compilePath
            case 'sourceCharset':
            case 'targetCharset':
            case 'checkModifiedTemplates':
            case 'executeTemplate':
                return $this->properties[$name];
            case 'customBlocks':
            case 'customFunctions':
                return (array) $this->properties[$name];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Sets the property $name to the value $value
     *
     * The properties that can be set are:
     * 
     * - ezcTemplateOutputCollection context    : Contains the template context.
     * - string templatePath                    : Base path where the source templates are stored.
     * - string compilePath                     : Base path where the compiled templates are stored.
     * - bool checkModifiedTemplates            : Set to true, to recompile outdated compiled templates.
     *
     * @param string $name
     * @param mixed $value
     * @param string $name  
     * @param string $value
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @return void
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'context': 
                if ( !$value instanceof ezcTemplateOutputContext )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcTemplateContext' );
                }
                $this->properties[$name] = $value;
                break;

            case 'cacheManager': 
                if ( !($value instanceof ezcTemplateCacheManager) && $value !== null )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcTemplateCacheManager' );
                }

                $this->properties[$name] = $value;
                break;

            case 'locator': 
                if ( !($value instanceof ezcTemplateLocator) && $value !== null )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcTemplateLocator' );
                }

                $this->properties[$name] = $value;
                break;


            case 'templatePath': 
            case 'compilePath': 
            case 'cachedTemplatesPath':
            case 'compiledTemplatesPath':
            case 'sourceCharset':
            case 'targetCharset':
            // case 'cacheSystem':
            case 'checkModifiedTemplates': 
            case 'executeTemplate':
            case 'customBlocks': 
            case 'customFunctions': 
            case 'disableCache':
                $this->properties[$name] = $value;
                break;

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
            case 'context': 
            case 'disableCache': 
            case 'sourceCharset':
            case 'targetCharset':
                return true;

            case 'cacheManager': 
            case 'locator': 
            case 'templatePath': 
            case 'compilePath':
            case 'cachedTemplatesPath':
            case 'compiledTemplatesPath':
            case 'checkModifiedTemplates':
            case 'executeTemplate':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Initialises the configuration with default template, compiled path, and context.
     *
     * All requested templates are search from the defined $templatePath. 
     * Use an empty string to fetch templates from the root of the filesystem.
     * 
     * All compiled templates are placed in subfolders under the compiled templates. 
     * Use an empty string to compile templates at the root of the filesystem.
     *
     * @param string $templatePath   Path where the source templates are stored.
     * @param string $compilePath    Path where the compiled templates should be stored.
     * @param ezcTemplateOutputContext $context  Context to use. Default is the ezcTemplateXhtmlContext.
     */
    public function __construct( $templatePath = ".", $compilePath = ".", ezcTemplateOutputContext $context = null )
    {
        $this->properties["templatePath"] = $templatePath;
        $this->properties["compilePath"] = $compilePath;

        $this->properties["cachedTemplatesPath"] =   "cached_templates";
        $this->properties["compiledTemplatesPath"] =  "compiled_templates";

        // $this->properties["cacheSystem"] = new ezcTemplateCacheFilesystem( $this );

        $this->properties['context'] = ( $context == null ? new ezcTemplateXhtmlContext() : $context );
    }

    /**
     * Returns the unique configuration instance named $name.
     *
     * Note: You only need to specify the name if you need multiple configuration
     *       objects.
     *
     * @param string $name  Name of the configuration to use.
     * @return ezcTemplateConfiguration
     */
    public static function getInstance( $name = "default" )
    {
        if ( !isset( self::$instanceList[$name] ) )
        {
            self::$instanceList[$name] = new ezcTemplateConfiguration();
            ezcBaseInit::fetchConfig( 'ezcInitTemplateConfiguration', self::$instanceList[$name] );
        }

        return self::$instanceList[$name];
    }

    /**
     * Adds custom tags or function to the customBlock or customFunction property and 
     * indirectly add the custom extension to the template language. 
     *
     * The parameter $customBlockClass expects a class that implements either 
     * the interface ezcTemplateCustomBlock, ezcTemplateCustomFunction, or both.
     *
     * New custom blocks are added to the
     * {@link ezcTemplateConfiguration::customBlocks $customBlocks} property while
     * custom functions are added to the
     * {@link ezcTemplateConfiguration::customFunctions $customFunctions} property.
     *
     * @param string $customClass
     * @throws ezcTemplateCustomBlockException if the $customClass parameter is not a string.
     * @return void
     */
    public function addExtension( $customClass )
    {
        if ( !is_string( $customClass ) )
        {
            throw new ezcTemplateCustomBlockException( "Could not add the extension $customClass, because the given value is not a string." );
        }

        $implements = class_implements( $customClass );

        $added = false;
        if ( in_array( "ezcTemplateCustomBlock", $implements ) )
        {
            $this->properties["customBlocks"][$customClass] = $customClass;
            $added = true;
        }

        if ( in_array( "ezcTemplateCustomFunction", $implements ) )
        {
            $this->properties["customFunctions"][$customClass] = $customClass;
            $added = true;
        }

        if ( !$added)
        {
            throw new ezcTemplateCustomBlockException( "Could not add the extension $customClass. Does it implement ezcTemplateCustomBlock or ezcTemplateCustomFunction?" );
        }
    }

}
?>
