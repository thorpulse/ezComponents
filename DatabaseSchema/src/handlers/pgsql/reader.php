<?php
/**
 * File containing the ezcDbSchemaPgsqlReader class.
 *
 * @package DatabaseSchema
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler for PostgreSQL connections representing a DB schema.
 *
 * @package DatabaseSchema
 * @version //autogentag//
 */
class ezcDbSchemaPgsqlReader implements ezcDbSchemaDbReader
{
    /**
     * Contains a type map from PostgreSQL native types to generic DbSchema types.
     *
     * @var array
     */
    static private $typeMap = array(
        'int' => 'integer',
        'int2' => 'integer',
        'int4' =>  'integer',
        'int8' => 'integer',
        'integer' => 'integer',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'float' => 'float',
        'double' => 'float',
        'dec' => 'decimal',
        'decimal' => 'decimal',
        'numeric' => 'decimal',
        'fixed' => 'decimal',
        
        'date' => 'date',
        'datetime' => 'timestamp',
        'timestamp' => 'timestamp',
        'timestamp without time zone' => 'timestamp',
        'time' => 'time',
        'year' => 'integer',
       
        'char' => 'text',
        'varchar' => 'text',
        'binary' => 'blob',
        'varbinary' => 'blob',
        'tinyblob' => 'blob',
        'blob' => 'blob',
        'mediumblob' => 'blob',
        'longblob' => 'blob',
        'tinytext' => 'clob',
        'text' => 'clob',
        'mediumtext' => 'clob',
        'longtext' => 'clob',

        'character varying'=>'text',
        'bigint' => 'integer',
        'double precision' => 'float'
    );
            
            
    /**
     * Returns what type of schema reader this class implements.
     *
     * This method always returns ezcDbSchema::DATABASE
     *
     * @return int
     */
    public function getReaderType()
    {
        return ezcDbSchema::DATABASE;
    }

    /**
     * Returns a ezcDbSchema object from the database that is referenced with $db.
     *
     * @param ezcDbHandler $db
     * @return ezcDbSchema
     */
    public function loadFromDb( ezcDbHandler $db )
    {
        $this->db = $db;
        return new ezcDbSchema( $this->fetchSchema() );
    }

    /**
     * Loops over all the tables in the database and extracts schema information.
     *
     * This method extracts information about a database's schema from the
     * database itself and returns this schema as an ezcDbSchema object.
     *
     * @return ezcDbSchema
     */
    private function fetchSchema()
    {
        $schemaDefinition = array();

        $tables = $this->db->query( "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'" )->fetchAll();
        array_walk( $tables, create_function( '&$item,$key', '$item = $item[0];' ) );

        foreach ( $tables as $tableName )
        {
            $fields  = $this->fetchTableFields( $tableName );
            $indexes = $this->fetchTableIndexes( $tableName );

            $schemaDefinition[$tableName] = new ezcDbSchemaTable( $fields, $indexes );
        }

        return $schemaDefinition;
    }

    /**
     * Fetch fields definition for the table $tableName
     *
     * This method loops over all the fields in the table $tableName and
     * returns an array with the field specification. The key in the returned
     * array is the name of the field.
     *
     * @param string $tableName
     * @return array(string=>ezcDbSchemaField)
     */
    private function fetchTableFields( $tableName )
    {
        $fields = array();

        //fetching fields info from PostgreSQL
        $resultArray = $this->db->query( 
            "SELECT a.attnum, a.attname AS field, t.typname AS type,
                     format_type(a.atttypid, a.atttypmod) AS fulltype,
                     ( SELECT substring(d.adsrc for 128) FROM pg_catalog.pg_attrdef d 
                       WHERE d.adrelid = a.attrelid AND d.adnum = a.attnum AND a.atthasdef
                     ) AS default,
                     a.attlen AS length, a.atttypmod AS lengthvar, a.attnotnull AS notnull
              FROM pg_class c, pg_attribute a, pg_type t 
              WHERE c.relname = '$tableName' AND 
                    a.attnum > 0 AND 
                    a.attrelid = c.oid AND
                    a.atttypid = t.oid 
              ORDER BY a.attnum" );

        $resultArray->setFetchMode( PDO::FETCH_ASSOC );

        foreach ( $resultArray as $row )
        {
            $fieldLength = false;
            $fieldType = self::convertToGenericType( $row['fulltype'], $fieldLength, $fieldPrecision );
            if ( !$fieldLength )
            {
                $fieldLength = false;
            }

            $fieldNotNull = $row['notnull'];

            $fieldDefault = null;

            $fieldAutoIncrement = false;

            if ($row['default'] != '' ) 
            {
                //detecting autoincrement field by string like "nextval('public.TableName_FieldName_seq'::text)" 
                //in "default"
                if( strstr( $row['default'], $row['field'].'_seq' ) != false ) 
                {
                    $fieldAutoIncrement = true;
                }
                else
                {
                    //try to cut off single quotes and "::Typename" that postgreSQL
                    //adds to default value string for some types.
                    //we should do it to get clean value for default clause.
                    if ( preg_match( "@'(.*)('::[a-z ]*)$@", $row['default'], $matches ) == 1 )
                    {
                        $fieldDefault = $matches[1];
                    }
                    else
                    {
                        $fieldDefault = $row['default'];
                        if ( $fieldType == 'boolean' )
                        {
                            ( $fieldDefault == 'true' )? $fieldDefault = 'true': $fieldDefault = 'false';
                        }
                    }
                }
            }
            // FIXME: unsigned needs to be implemented
            $fieldUnsigned = false;

            $fields[$row['field']] = new ezcDbSchemaField( $fieldType, $fieldLength, $fieldNotNull, $fieldDefault, $fieldAutoIncrement, $fieldUnsigned );
        }

        return $fields;
    }

    /**
     * Converts the native PostgreSQL type in $typeString to a generic DbSchema type.
     *
     * This method converts a string like "float(5,10)" to the generic DbSchema
     * type and uses the by-reference parameters $typeLength and $typePrecision
     * to communicate the optional length and precision of the field's type.
     *
     * @param string  $typeString
     * @param int    &$typeLength
     * @param int    &$typePrecision
     * @return string
     */
    static function convertToGenericType( $typeString, &$typeLength, &$typePrecision )
    {
        preg_match( "@([a-z ]*)(\((\d*)(,(\d+))?\))?@", $typeString, $matches );
        $genericType = self::$typeMap[$matches[1]];

        if ( in_array( $genericType, array( 'text', 'decimal', 'float' ) ) && isset( $matches[3] ) )
        {
            $typeLength = $matches[3];
            if ( is_numeric( $typeLength ) )
            {
                $typeLength = (int) $typeLength;
            }
        }
        if ( in_array( $genericType, array( 'decimal', 'float' ) ) && isset( $matches[5] ) )
        {
            $typePrecision = $matches[5];
        }

        return $genericType;
    }

    /**
     * Returns whether the type $type is a numeric type
     *
     * @return bool
     */
    private function isNumericType( $type )
    {
        $types = array( 'float', 'int' );
        return in_array( $type, $types );
    }

    /**
     * Returns whether the type $type is a string type
     *
     * @return bool
     */
    private function isStringType( $type )
    {
        $types = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
        return in_array( $type, $types );
    }

    /**
     * Returns whether the type $type is a blob type
     *
     * @return bool
     */
    private function isBlobType( $type )
    {
        $types = array( 'varchar', 'char' );
        return in_array( $type, $types );
    }


    /**
     * Loops over all the indexes in the table $table and extracts information.
     *
     * This method extracts information about the table $tableName's indexes
     * from the database and returns this schema as an array of
     * ezcDbSchemaIndex objects. The key in the array is the index' name.
     *
     * @param  string
     * @return array(string=>ezcDbSchemaIndex)
     */
    private function fetchTableIndexes( $tableName )
    {
        $indexBuffer = array();
        $resultArray = array();

        //fetching index info from PostgreSQL
        $getIndexSQL = "SELECT relname, pg_index.indisunique, pg_index.indisprimary, 
                               pg_index.indkey, pg_index.indrelid 
                         FROM pg_class, pg_index
                         WHERE oid IN ( 
                                SELECT indexrelid 
                                FROM pg_index, pg_class 
                                WHERE pg_class.relname='$tableName' AND pg_class.oid=pg_index.indrelid 
                             ) 
                      AND pg_index.indexrelid = oid";
        $indexesArray = $this->db->query( $getIndexSQL )->fetchAll();

        //getting columns to which each index related.
        foreach ( $indexesArray as $row )
        {
            $myIndex[]=$row['relname'];

            $colNumbers = explode(' ', $row['indkey']);
            $colNumbersSQL = 'IN ('.join(' ,', $colNumbers ).' )';
            $indexColumns = $this->db->query( "SELECT attname 
                                               FROM pg_attribute 
                                               WHERE attrelid={$row['indrelid']} 
                                                     AND attnum $colNumbersSQL;" );

            foreach ( $indexColumns as $colRow )
            {
                $resultArray[] = array( 'key_name' => $row['relname'], 
                                        'column_name' => $colRow['attname'],
                                        'non_unique' => !$row['indisunique'],
                                        'primary' => !$row['indisprimary']
                                      );
                $indexColumnNames[] = $colRow['attname'];
            }
        }

        foreach ( $resultArray as $row )
        {
            $keyName = $row['key_name'];
            if ( substr( $keyName, -5) == '_pkey' )
            {
                $keyName = 'primary';
            }

            $indexBuffer[$keyName]['primary'] = false;
            $indexBuffer[$keyName]['unique'] = true;

            if ( $keyName == 'primary' )
            {
                $indexBuffer[$keyName]['primary'] = true;
                $indexBuffer[$keyName]['unique'] = true;
            }
            else
            {
                $indexBuffer[$keyName]['unique'] = $row['non_unique'] ? false : true;
            }

            $indexBuffer[$keyName]['fields'][$row['column_name']] = new ezcDbSchemaIndexField();
        }

        $indexes = array();

        foreach ( $indexBuffer as $indexName => $indexInfo )
        {
            $indexes[$indexName] = new ezcDbSchemaIndex( $indexInfo['fields'], $indexInfo['primary'], $indexInfo['unique'] );
        }

        return $indexes;
    }

}
?>
