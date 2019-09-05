<?php
defined( '_JEXEC' ) or die; // No direct access

/**
 * Default Controller
 * @author 
 */
class MultipollsController extends JControllerLegacy
{
	public function getResults()
    {
        if (!JSession::checkToken('get')) 
        {
            echo new JResponseJson(null, JText::_('JINVALID_TOKEN'), true);
        }
        else 
        {
            parent::display();
        }
    }

	function display( $cachable = false, $urlparams = array() )
	{
		$this->default_view = 'poll';
		parent::display( $cachable, $urlparams );
		return $this;
	}	
	
}