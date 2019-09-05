<?php

defined( '_JEXEC' ) or die;

class MultipollsViewQuestion extends JViewLegacy
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

	protected function _setToolBar()
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', true );
		$isNew = ( $this->item->id == 0 );
		$canDo = multipollsHelper::getActions();

		JToolBarHelper::title( JText::_( 'COM_MULTIPOLLS' ) . ': ' . ( $isNew ? JText::_( 'COM_MULTIPOLLS_NEW_QUESTION' ) : JText::_( 'COM_MULTIPOLLS_EDIT_QUESTION' ) ) );

		if ($isNew && $canDo->get( 'core.create' )) 
		{
            JToolBarHelper::apply( 'question.apply' );
            JToolBarHelper::save( 'question.save' );
            JToolBarHelper::save2new( 'question.save2new' );
            JToolBarHelper::cancel( 'question.cancel' );
        } 
        else 
        {            
            if ( $canDo->get( 'core.edit' ) && !$isNew) 
            {
                JToolBarHelper::apply( 'question.apply' );
                JToolBarHelper::save( 'question.save' );
               
                if ( $canDo->get( 'core.create' ) ) 
                {
                    JToolBarHelper::save2new( 'question.save2new' );
                }
            }          

            JToolBarHelper::cancel( 'question.cancel', 'JTOOLBAR_CLOSE' );
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