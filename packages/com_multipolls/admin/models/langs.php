<?php

// No direct access
defined( '_JEXEC' ) or die;

/**
 * @author
 */
class MultipollsModelLangs extends JModelList
{

    /**
     * Конструктор класса
     * @param Array $config
     */
    public function __construct( $config = array() )
    {        
        parent::__construct( $config );
    }

    /**
     * Method to auto-populate the model state.
     * @param String $ordering
     * @param String $direction
     */
    protected function populateState( $ordering = null, $direction = null )
    {
        parent::populateState( 'id_lang', 'desc' );
    }
    
    /**
     * Составление запроса для получения списка записей
     * используется в родителськом методе getItems()
     * @return JDatabaseQuery
     */
    protected function getListQuery()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('id_lang', 'name', 'published', 'language')));
        $query->from($db->quoteName('#__multipolls_langs'));		
		
        $published = $this->getState( 'filter.published' );
        if ( is_numeric( $published ) ) {
            $query->where($db->quoteName('published') . ' = ' . (int)$published  );
        }
        
        //orderCol и orderDirn задаются в populateState()
        $orderCol = $this->state->get( 'list.ordering' ); 
        $orderDirn = $this->state->get( 'list.direction' );
        $query->order( $this->getDbo()->escape( $orderCol . ' ' . $orderDirn ) );
        return $query;
    }
    
}