<?php

defined( '_JEXEC' ) or die;

class MultipollsModelPolls extends JModelList
{	

	protected $lang_tag;

	public function __construct( $config = array() )
    {      

		$this->lang_tag = JFactory::getLanguage()->getTag();

        if ( empty( $config['filter_fields'] ) ) {
            $config['filter_fields'] = array( 'name', 'published' ,'created' );
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

        parent::populateState( 'id', 'desc' );
    }

	protected function getStoreId( $id = '' )
    {
        $id .= ':' . $this->getState( 'filter.search' );
        $id .= ':' . $this->getState( 'filter.published' );               
        return parent::getStoreId( $id );
    }

	protected function getListQuery()
    {   	

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('id','created','published', 'publish_up', 'publish_down')));
        $query->select($db->quoteName('name_'.$this->lang_tag, 'name'));
        $query->from($db->quoteName('#__multipolls_polls'));		
		
        $published = $this->getState( 'filter.published' );
        if ( is_numeric( $published ) ) {
            $query->where($db->quoteName('published') . ' = ' . (int)$published  );
        }
       
        $search = $this->getState( 'filter.search' );
        if ( !empty( $search ) ) {
            $search = $db->Quote( '%' . $db->escape( $search, true ) . '%' );

            $query->where($db->quoteName('name_'.$this->lang_tag) . ' LIKE ' . $search  );
        }
        $orderCol = $this->state->get( 'list.ordering' );
        $orderDirn = $this->state->get( 'list.direction' );
        $query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );                
        return $query;
    }

    public function delete($ids)
    {   
        $db = $this->getDbo();

        $statModel = JModelLegacy::getInstance('Stat', 'MultipollsModel');

        //список вопросов
        $query = $db->getQuery(true); 

        $query->select($db->quoteName(array('id')));
        $query->from($db->quoteName('#__multipolls_questions'));
        $query->where($db->quoteName('id_poll') . 'IN (' . implode(',', $db->quote($ids)) . ')'); 
        $db->setQuery($query);
        $list_questions = $db->loadColumn();

        //список ответов
        $query = $db->getQuery(true); 

        $query->select($db->quoteName(array('id')));
        $query->from($db->quoteName('#__multipolls_answers'));
        $query->where($db->quoteName('id_question') . 'IN (' . implode(',', $db->quote($list_questions)) . ')'); 
        $db->setQuery($query);
        $list_answers = $db->loadColumn();
        
        $db->transactionStart();

        try {     
            foreach ($ids as $id) {
                $statModel->clearResults($id);//удаляем результаты
            }

            //удаляем ответы
            $query = $db->getQuery(true);
            
            $conditions = array(
                $db->quoteName('id') . 'IN (' . implode(',', $db->quote($list_answers)) . ')'
            );

            $query->delete($db->quoteName('#__multipolls_answers'));
            $query->where($conditions);

            $db->setQuery($query);
            $db->execute();

            //удаляем вопросы
            $query = $db->getQuery(true);
            
            $conditions = array(
                $db->quoteName('id') . 'IN (' . implode(',', $db->quote($list_questions)) . ')'
            );

            $query->delete($db->quoteName('#__multipolls_questions'));
            $query->where($conditions);

            $db->setQuery($query);
            $db->execute();

            //удаляем опросы
            $query = $db->getQuery(true);
            
            $conditions = array(
                $db->quoteName('id') . 'IN (' . implode(',', $db->quote($ids)) . ')'
            );

            $query->delete($db->quoteName('#__multipolls_polls'));
            $query->where($conditions);

            $db->setQuery($query);
            $db->execute();            

        } catch (Exception $e) {   
            $db->transactionRollback();
            $this->setError($e->getMessage());
            return false;           
        }

        $db->transactionCommit();

        return true;
    }
}