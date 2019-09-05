<?php

defined( '_JEXEC' ) or die;

class MultipollsViewVotes extends JViewLegacy
{
	protected $items;	
	protected $pagination;	
    protected $state;
    protected $user;

	public function display( $tpl = null )
	{
		$this->items = $this->get( 'Items' );
        $this->state = $this->get( 'State' );
        $this->pagination = $this->get( 'Pagination' ); 
        $this->user = JFactory::getUser();      
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');       
        if ( $this->getLayout() !== 'modal' ) {
            $this->addToolbar();
            multipollsHelper::addSubmenu( 'votes' );
            $this->sidebar = JHtmlSidebar::render();
        }
        parent::display( $tpl );
	}

	protected function addToolbar()
    {
    	JToolBarHelper::title( JText::_( 'COM_MULTIPOLLS' ).': '. JText::_( 'COM_MULTIPOLLS_ITEM_SUBMENU_VOTES' ));
    	$canDo = multipollsHelper::getActions();    	

        if ( $canDo->get( 'core.admin' ) ) {
            JToolBarHelper::preferences( 'com_multipolls' );           
        }
    }
}