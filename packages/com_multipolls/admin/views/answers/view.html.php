<?php

defined( '_JEXEC' ) or die;

class MultipollsViewAnswers extends JViewLegacy
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
            multipollsHelper::addSubmenu( 'answers' );
            $this->sidebar = JHtmlSidebar::render();
        }
        parent::display( $tpl );
	}

	protected function addToolbar()
    {
    	JToolBarHelper::title( JText::_( 'COM_MULTIPOLLS' ).': '. JText::_( 'COM_MULTIPOLLS_ITEM_SUBMENU_ANSWERS' ));
    	$canDo = multipollsHelper::getActions();

    	if ( $canDo->get( 'core.create' ) ) {
            JToolBarHelper::addNew( 'answer.add' );
        }

        if ( $canDo->get( 'core.edit' ) ) {
            JToolBarHelper::editList( 'answer.edit' );
        }

    	if ( $canDo->get( 'core.edit.state' ) ) {
            
            JToolBarHelper::publish( 'answers.publish', 'JTOOLBAR_PUBLISH', true );
            JToolBarHelper::unpublish( 'answers.unpublish', 'JTOOLBAR_UNPUBLISH', true );

            if ( $canDo->get( 'core.delete' ) ) {
                JToolBarHelper::deleteList( 'DELETE_QUERY_STRING', 'answers.delete', 'JTOOLBAR_DELETE' );                
            }
        }

        if ( $canDo->get( 'core.admin' ) ) {
            JToolBarHelper::preferences( 'com_multipolls' );           
        }
    }
}	