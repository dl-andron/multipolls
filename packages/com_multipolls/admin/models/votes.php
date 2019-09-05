<?php

defined( '_JEXEC' ) or die;

class MultipollsModelVotes extends JModelList
{	
	protected $lang_tag;

	public function __construct( $config = array() )
    {      

		$this->lang_tag = JFactory::getLanguage()->getTag();

        if ( empty( $config['filter_fields'] ) ) {
            //если из массива убрать поле, то кнопка "Инструменты поиска" будет сворачиваться 
            //и стрелки при направлении сортировки по полю не отобразятся
            $config['filter_fields'] = array( 'date_voting', 'user_agent', 'ip', 'answer_name', 'id_poll', 'id_question' );
        }
        parent::__construct( $config );
    }

    protected function populateState( $ordering = null, $direction = null )
    {
        if ( $layout = JFactory::getApplication()->input->get( 'layout' ) ) {
            $this->context .= '.' . $layout;
        }        

        $search = $this->getUserStateFromRequest( $this->context . '.filter.search', 'filter_search' );
        $this->setState( 'filter.search', $search ); 

        $id_poll = $this->getUserStateFromRequest( $this->context . '.filter.id_poll', 'filter_id_poll');
        $this->setState( 'filter.id_poll', $id_poll );    

        $id_question = $this->getUserStateFromRequest( $this->context . '.filter.id_question', 'filter_id_question');
        $this->setState( 'filter.id_question', $id_question );      

        parent::populateState( 'id_vote', 'desc' );
    }

    protected function getStoreId( $id = '' )
    {
        $id .= ':' . $this->getState( 'filter.search' );       
        $id .= ':' . $this->getState( 'filter.id_poll' );   
        $id .= ':' . $this->getState( 'filter.id_question' );            
        return parent::getStoreId( $id );
    }

	protected function getListQuery()
    {   	

        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select($db->quoteName(array('id_vote','ip','user_agent', 'date_voting')));   
        $query->select($db->quoteName('q.id','id_question'));  
        $query->select($db->quoteName('a.id','id_answer'));     
        $query->select($db->quoteName('a.name_'.$this->lang_tag, 'answer_name')); 
        $query->select($db->quoteName('q.name_'.$this->lang_tag, 'question_name'));      

        $query->from($db->quoteName('#__multipolls_votes', 'v'));		
        $query->join('INNER', $db->quoteName('#__multipolls_answers', 'a') . ' ON (' . $db->quoteName('a.id') . ' = ' . $db->quoteName('v.id_answer') . ')'); 

        $query->join('INNER', $db->quoteName('#__multipolls_questions', 'q') . ' ON (' . $db->quoteName('q.id') . ' = ' . $db->quoteName('a.id_question') . ')');     
       
        $search = $this->getState( 'filter.search' );
        if ( !empty( $search ) ) 
        {
            $search = $db->Quote( '%' . $db->escape( $search, true ) . '%' );

            $query->where($db->quoteName('a.name_'.$this->lang_tag) . ' LIKE ' . $search  );
        }

        $id_poll = $this->getState( 'filter.id_poll' );
        if ( is_numeric( $id_poll ) )
        {    
            $query->where($db->quoteName('q.id_poll') . ' = ' . $id_poll  );
        }

        $id_question = $this->getState( 'filter.id_question' );
        if ( is_numeric( $id_question ) )
        {    
            $query->where($db->quoteName('q.id') . ' = ' . $id_question );
        }

        $orderCol = $this->state->get( 'list.ordering' );
        $orderDirn = $this->state->get( 'list.direction' );
        $query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );                
        return $query;
    }
}