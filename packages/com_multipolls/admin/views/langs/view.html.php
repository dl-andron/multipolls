<?php

// No direct access
defined( '_JEXEC' ) or die;

class MultipollsViewLangs extends JViewLegacy
{
	/**
     * @var $items stdClass[]
     */
	public $items;    
    /**
     * @var $state JObject
     */
    public $state;

    public function display( $tpl = null )
    {
        $this->items = $this->get( 'Items' );        
        $this->state = $this->get( 'State' );        
        if ( $this->getLayout() !== 'modal' ) {
            $this->addToolbar();
            multipollsHelper::addSubmenu( 'langs' );
            $this->sidebar = JHtmlSidebar::render();
        }
        parent::display( $tpl );
    }

    protected function addToolbar()
    {
    	JToolBarHelper::title( JText::_( 'COM_MULTIPOLLS' ).': '. JText::_( 'COM_MULTIPOLLS_ITEM_SUBMENU_LANGS' ), 'cog.png' );
    	$canDo = multipollsHelper::getActions();

    	if ( $canDo->get( 'core.edit.state' ) ) {
            
            JToolBarHelper::publish( 'langs.publish', 'JTOOLBAR_PUBLISH', true );
            JToolBarHelper::unpublish( 'langs.unpublish', 'JTOOLBAR_UNPUBLISH', true );
        }

        if ( $canDo->get( 'core.admin' ) ) {
            JToolBarHelper::preferences( 'com_multipolls' );           
        }
    }

}