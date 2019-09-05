<?php

defined( '_JEXEC' ) or die;

class MultipollsViewQuestions extends JViewLegacy
{
	public $items;
	public $state;
	public $pagination;
	public $user;

	public function display( $tpl = null )
	{
		$this->items = $this->get( 'Items' );
        $this->pagination = $this->get( 'Pagination' );
        $this->state = $this->get( 'State' );        
        $this->user = JFactory::getUser();
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');       
        if ( $this->getLayout() !== 'modal' ) {
            $this->addToolbar();
            multipollsHelper::addSubmenu( 'questions' );
            $this->sidebar = JHtmlSidebar::render();
        }
        parent::display( $tpl );
	}

	protected function addToolbar()
    {
    	JToolBarHelper::title( JText::_( 'COM_MULTIPOLLS' ).': '. JText::_( 'COM_MULTIPOLLS_ITEM_SUBMENU_QUESTIONS' ));
    	$canDo = multipollsHelper::getActions();

    	if ( $canDo->get( 'core.create' ) ) {
            JToolBarHelper::addNew( 'question.add' );
        }

        if ( $canDo->get( 'core.edit' ) ) {
            JToolBarHelper::editList( 'question.edit' );
        }

    	if ( $canDo->get( 'core.edit.state' ) ) {
            
            JToolBarHelper::publish( 'questions.publish', 'JTOOLBAR_PUBLISH', true );
            JToolBarHelper::unpublish( 'questions.unpublish', 'JTOOLBAR_UNPUBLISH', true );

            if ( $canDo->get( 'core.delete' ) ) {
                JToolBarHelper::deleteList( 'DELETE_QUERY_STRING', 'questions.delete', 'JTOOLBAR_DELETE' );                
            }
        }

        if ( $canDo->get( 'core.admin' ) ) {
            JToolBarHelper::preferences( 'com_multipolls' );           
        }
    }
}