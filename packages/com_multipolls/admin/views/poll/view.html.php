<?php

defined( '_JEXEC' ) or die;

class MultipollsViewPoll extends JViewLegacy
{
	protected $item;
	protected $form;		

	public function display( $tpl = null )
	{	
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');		

		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->_setToolBar();
		parent::display( $tpl );
	}

	public function showResult($tpl = null)
	{		
		JFactory::getApplication()->input->set( 'hidemainmenu', true );
		JToolBarHelper::title( JText::_( 'COM_MULTIPOLLS' ) . ': ' .  JText::_( 'COM_MULTIPOLLS_STAT' ));
		JToolBarHelper::cancel( 'poll.cancel', 'JTOOLBAR_CLOSE' );
		parent::display( $tpl );
	}

	protected function _setToolBar()
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', true );
		$isNew = ( $this->item->id == 0 );
		$canDo = multipollsHelper::getActions();

		JToolBarHelper::title( JText::_( 'COM_MULTIPOLLS' ) . ': ' . ( $isNew ? JText::_( 'COM_MULTIPOLLS_NEW_POLL' ) : JText::_( 'COM_MULTIPOLLS_EDIT_POLL' ) ) );

		if ($isNew && $canDo->get( 'core.create' )) 
		{
            JToolBarHelper::apply( 'poll.apply' );
            JToolBarHelper::save( 'poll.save' );
            JToolBarHelper::save2new( 'poll.save2new' );
            JToolBarHelper::cancel( 'poll.cancel' );
        } 
        else 
        {            
            if ( $canDo->get( 'core.edit' ) && !$isNew) 
            {
                JToolBarHelper::apply( 'poll.apply' );
                JToolBarHelper::save( 'poll.save' );
               
                if ( $canDo->get( 'core.create' ) ) 
                {
                    JToolBarHelper::save2new( 'poll.save2new' );
                }
            }          

            JToolBarHelper::cancel( 'poll.cancel', 'JTOOLBAR_CLOSE' );
        }
	}

	protected function getLangs()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		
		$query->select($db->quoteName('language'));
		$query->from($db->quoteName('#__multipolls_langs'));
		$query->where($db->quoteName('published') . ' = 1');		
		$db->setQuery($query);
		
		$langs = $db->loadColumn();
		return $langs;
	}
} 