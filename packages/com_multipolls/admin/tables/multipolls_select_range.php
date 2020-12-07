<?php


defined( '_JEXEC' ) or die;

class TableMultipolls_Select_Range extends JTable
{   
    function __construct( &$db )
    {
        parent::__construct( '#__multipolls_select_range', 'id', $db );
    }
  
    public function bind( $array, $ignore = '' )
    {

        return parent::bind( $array, $ignore );
    }

}