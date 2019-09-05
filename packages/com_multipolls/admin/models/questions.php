<?php

defined( '_JEXEC' ) or die;

class MultipollsModelQuestions extends JModelList
{	
	protected $lang_tag;

	public function __construct( $config = array() )
    {      

		$this->lang_tag = JFactory::getLanguage()->getTag();

        if ( empty( $config['filter_fields'] ) ) {
            //если из массива убрать поле, то кнопка "Инструменты поиска" будет сворачиваться
            $config['filter_fields'] = array( 'name', 'published' ,'created', 'poll_name', 'id_poll' );
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

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
        $this->setState('filter.published', $published);  

        $id_poll = $this->getUserStateFromRequest( $this->context . '.filter.id_poll', 'filter_id_poll');
        $this->setState( 'filter.id_poll', $id_poll );          

        parent::populateState( 'id', 'desc' );
    }

    protected function getStoreId( $id = '' )
    {
        $id .= ':' . $this->getState( 'filter.search' );
        $id .= ':' . $this->getState( 'filter.published' ); 
        $id .= ':' . $this->getState( 'filter.id_poll' );              
        return parent::getStoreId( $id );
    }

	protected function getListQuery()
    {   	

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('q.id','q.id_poll','q.created','q.published', 'q.publish_up', 'q.publish_down')));        
        $query->select($db->quoteName('q.name_'.$this->lang_tag, 'name'));
        $query->select($db->quoteName('p.name_'.$this->lang_tag, 'poll_name'));

        $query->from($db->quoteName('#__multipolls_questions', 'q'));		
        $query->join('INNER', $db->quoteName('#__multipolls_polls', 'p') . ' ON (' . $db->quoteName('q.id_poll') . ' = ' . $db->quoteName('p.id') . ')');

        $published = $this->getState( 'filter.published' );
        if ( is_numeric( $published ) ) 
        {
            $query->where($db->quoteName('q.published') . ' = ' . (int)$published  );
        }
       
        $search = $this->getState( 'filter.search' );
        if ( !empty( $search ) ) 
        {
            $search = $db->Quote( '%' . $db->escape( $search, true ) . '%' );

            $query->where($db->quoteName('q.name_'.$this->lang_tag) . ' LIKE ' . $search  );
        }

        $id_poll = $this->getState( 'filter.id_poll' );
        if ( is_numeric( $id_poll ) )
        {    
            $query->where($db->quoteName('id_poll') . ' = ' . $id_poll  );
        }

        $orderCol = $this->state->get( 'list.ordering' );
        $orderDirn = $this->state->get( 'list.direction' );
        $query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );                
        return $query;
    }
}