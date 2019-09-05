<?php

defined( '_JEXEC' ) or die;

class MultipollsModelAnswers extends JModelList
{	
	protected $lang_tag;

	public function __construct( $config = array() )
    {      

		$this->lang_tag = JFactory::getLanguage()->getTag();

        if ( empty( $config['filter_fields'] ) ) {
            //если из массива убрать поле, то кнопка "Инструменты поиска" будет сворачиваться
            $config['filter_fields'] = array( 'name', 'published' ,'created', 'question_name', 'id_question' );
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

        $id_question = $this->getUserStateFromRequest( $this->context . '.filter.id_question', 'filter_id_question');
        $this->setState( 'filter.id_question', $id_question );          

        parent::populateState( 'id', 'desc' );
    }

    protected function getStoreId( $id = '' )
    {
        $id .= ':' . $this->getState( 'filter.search' );
        $id .= ':' . $this->getState( 'filter.published' ); 
        $id .= ':' . $this->getState( 'filter.id_question' );              
        return parent::getStoreId( $id );
    }

	protected function getListQuery()
    {   	

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('a.id','a.id_question','a.created','a.published', 'a.publish_up', 'a.publish_down')));        
        $query->select($db->quoteName('a.name_'.$this->lang_tag, 'name'));
        $query->select($db->quoteName('q.name_'.$this->lang_tag, 'question_name'));

        $query->from($db->quoteName('#__multipolls_answers', 'a'));		
        $query->join('INNER', $db->quoteName('#__multipolls_questions', 'q') . ' ON (' . $db->quoteName('a.id_question') . ' = ' . $db->quoteName('q.id') . ')');

        $published = $this->getState( 'filter.published' );
        if ( is_numeric( $published ) ) 
        {
            $query->where($db->quoteName('q.published') . ' = ' . (int)$published  );
        }
       
        $search = $this->getState( 'filter.search' );
        if ( !empty( $search ) ) 
        {
            $search = $db->Quote( '%' . $db->escape( $search, true ) . '%' );

            $query->where($db->quoteName('a.name_'.$this->lang_tag) . ' LIKE ' . $search  );
        }

        $id_question = $this->getState( 'filter.id_question' );
        if ( is_numeric( $id_question ) )
        {    
            $query->where($db->quoteName('id_question') . ' = ' . $id_question  );
        }

        $orderCol = $this->state->get( 'list.ordering' );
        $orderDirn = $this->state->get( 'list.direction' );
        $query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );                
        return $query;
    }
}