<?php
/**
 * Created by PhpStorm.
 * User: dland
 * Date: 23.11.2018
 * Time: 13:03
 */
// No direct access
defined( '_JEXEC' ) or die;

/**
 * Object Class Table
 * @author
 */
class TableMultipolls_Polls extends JTable
{

    /**
     * Class constructor
     * @param Object $db (database link object)
     */
    function __construct( &$db )
    {
        parent::__construct( '#__multipolls_polls', 'id', $db );
    }

    /**
     * Method for loading data into the object field
     * @param Array $array (Featured in the field of data)
     * @param String $ignore
     * @return Boolean result
     */

    public function bind( $array, $ignore = '' )
    {

        return parent::bind( $array, $ignore );
    }

}