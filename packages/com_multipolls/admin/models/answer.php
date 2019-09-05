<?php

defined( '_JEXEC' ) or die;

class MultipollsModelAnswer extends JModelAdmin 
{   
    public function getForm( $data = array( ), $loadData = true ) 
	{
        $form = $this->loadForm( 'com_multipolls.answer', 'answer', array( 'control' => 'jform', 'load_data' => $loadData ) );
        if ( empty( $form ) )
        {
            return false;
        }

        return $form;
    }

    public function getItem( $id = null ) 
    {
        return parent::getItem( $id );
    }

    protected function loadFormData() 
    {
        $data = JFactory::getApplication()->getUserState( 'com_multipolls.edit.answer.data', array() );
        if ( empty( $data ) ) 
        {
            $data = $this->getItem();
        }
        return $data;
    }

    public function getTable( $type = 'multipolls_answers', $prefix = 'Table', $config = array( ) ) {
        return JTable::getInstance( $type, $prefix, $config );
    }

    public function store()
    {
        $row = $this->getTable();

        $input = JFactory::getApplication()->input;
        $data = $input->get('jform', array(), 'ARRAY');  

        $data['created'] = empty($data['created']) ? JFactory::getDate('now')->toSql() : JFactory::getDate($data['created'])->toSql();
        $data['publish_down'] = empty($data['publish_down']) ? $this->getDbo()->getNullDate() : JFactory::getDate($data['publish_down'])->toSql();         

        if ($data['published'] == 1 && (int)$data['publish_up'] == 0)
        {  
            $data['publish_up'] = JFactory::getDate('now')->toSql();
        }

        elseif ((int)$data['publish_up'] != 0)
        {
            $data['publish_up'] = JFactory::getDate($data['publish_up'])->toSql();            
        }          

        if (!$row->bind($data)) 
        {            
            return false;
        }

        if (!$row->store()) 
        {            
            return false;
        }     
      
        return $row->id;
    }
    
}	