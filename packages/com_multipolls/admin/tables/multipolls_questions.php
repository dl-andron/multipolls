<?php


defined( '_JEXEC' ) or die;

class TableMultipolls_Questions extends JTable
{   
    function __construct( &$db )
    {
        parent::__construct( '#__multipolls_questions', 'id', $db );
    }
  
    public function bind( $array, $ignore = '' )
    {

        return parent::bind( $array, $ignore );
    }

}