<?php

defined( '_JEXEC' ) or die;

class MultipollsModelQuestion extends JModelAdmin 
{   
    public function getForm( $data = array( ), $loadData = true ) 
	{
        $form = $this->loadForm( 'com_multipolls.question', 'question', array( 'control' => 'jform', 'load_data' => $loadData ) );
        if ( empty( $form ) )
        {
            return false;
        }

        return $form;
    }

    public function getItem( $id = null ) 
    {
        $item =  parent::getItem( $id );

        $range_row = $this->getSelectRangeRow($item->id);      

        $item->id_range =  !empty($range_row['id']) ? $range_row['id'] : 0;
        if(!empty($range_row['max_range'])){
            $item->range = $range_row['max_range'];  
        }     
        
        return $item;
    }

    private function getSelectRangeRow($id_question)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true); 

        $query->select('*');
        $query->from($db->quoteName('#__multipolls_select_range'));
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));  

        $db->setQuery($query);
        $row = $db->loadAssoc();

        return $row;
    }

    protected function loadFormData() 
    {
        $data = JFactory::getApplication()->getUserState( 'com_multipolls.edit.question.data', array() );
        if ( empty( $data ) ) 
        {
            $data = $this->getItem();
        }
        return $data;
    }

    public function getTable( $type = 'multipolls_questions', $prefix = 'Table', $config = array( ) ) {
        return JTable::getInstance( $type, $prefix, $config );
    }

    public function getTableRange( $type = 'multipolls_select_range', $prefix = 'Table', $config = array( ) ) {
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

        $db = $this->getDbo();
        $query = $db->getQuery(true);       

        $db->transactionStart();

        try {
            $row->bind($data);
            $row->store();

            /*проверяю тип вопроса, если 3 или 5, то сохраняю 
            в отдельную таблицу максимальное значение ранжирования*/
            if(in_array($data['id_type'], array(3,5))){                
                $range_data['id'] = $data['id_range'];
                $range_data['id_question'] = $data['id'];
                $range_data['max_range'] = $data['range'];

                $range_row = $this->getTableRange();
                
                $range_row->bind($range_data);
                $range_row->store();
            }
        } catch (Exception $e) {   
            $db->transactionRollback();
            $this->setError($e->getMessage());
            return false;           
        }    
        
        $db->transactionCommit();

        return $row->id;
    }
    
}	