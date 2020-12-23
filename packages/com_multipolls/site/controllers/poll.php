<?php

defined( '_JEXEC' ) or die;

require_once JPATH_BASE.'/components/com_multipolls/captcha/generate.php';

class MultipollsControllerPoll extends JControllerLegacy
{
	public function saveVote()
	{
		JSession::checkToken() or die( 'Invalid Token' );

		$app = JFactory::getApplication();
		$jinput = $app->input;
		$id_poll = $jinput->get('id','', 'int');
		$url = JFactory::getURI();

		$check_cookie = $jinput->get->cookie->get('multipoll'.$id_poll, '');
		if ($check_cookie)
		{
			$this->setRedirect($url, $app->enqueueMessage(JText::_('COM_MULTIPOLLS_ALREADY_VOTED'),'error'));		
			return false;
		}
		
		$user_captcha = trim(strtolower($this->input->get('captcha', '', 'string')));
		$real_crypt_captcha = $this->input->get('real-captcha', '', 'string');		
		$real_captcha = encrypt_decrypt('decrypt', $real_crypt_captcha);

		if (!$real_captcha || $user_captcha != $real_captcha)
		{			
			$this->setRedirect($url, $app->enqueueMessage(JText::_('COM_MULTIPOLLS_CAPTCHA_ERROR'),'error'));		
			return;
		}		
		
		$votes['r'] = $jinput->get('r', array(), 'ARRAY');	    	
		$votes['cb']  = $jinput->get('cb', array(), 'ARRAY');
		$votes['s'] = $jinput->get('s', array(), 'ARRAY');
		$votes['ta'] = $jinput->get('ta', array(), 'ARRAY');
		$votes['sta'] = $jinput->get('sta', array(), 'ARRAY');
		$votes['sta-text'] = $jinput->get('sta-text', array(), 'ARRAY');
		$votes['ro'] = $jinput->get('ro', array(), 'ARRAY');	
		$votes['yn'] = $jinput->get('yn', array(), 'ARRAY');	
		$votes['cbo'] = $jinput->get('cbo', array(), 'ARRAY');	
		$votes['priority'] = $jinput->get('priority', array(), 'ARRAY');

		$model = $this->getModel('poll');

		if(!$model->checkQuestions($id_poll, $votes))
		{			
			$this->setRedirect($url, $model->getError(),'error');			
			return false;
		}

		$data = new stdClass;
		$data->votes = $votes;
		$data->ip = $jinput->server->get('REMOTE_ADDR');		
		$data->user_agent = JBrowser::getInstance()->getAgentString();
		$data->date_vote = JFactory::getDate('now', 'Europe/Minsk');	

		if (!$model->saveVote($data))
		{			
			$this->setRedirect($url, $model->getError(),'error');			
			return false;      
		}	

		$app->input->cookie->set('multipoll'.$id_poll, '1', time() + (3600 * 24), $app->get('cookie_path', '/'), $app->get('cookie_domain'));			
	        
	    $this->setRedirect($url, $app->enqueueMessage(JText::_('COM_MULTIPOLLS_VOTES_SUCCESS'),'message'));	

	    return true;	
	}
}