<?php


defined( '_JEXEC' ) or die;

class TableMultipolls_Answers extends JTable
{   
    function __construct( &$db )
    {
        parent::__construct( '#__multipolls_answers', 'id', $db );
    }
  
    public function bind( $array, $ignore = '' )
    {

        return parent::bind( $array, $ignore );
    }

}