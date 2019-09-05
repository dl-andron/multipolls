<?php

defined('_JEXEC') or die;

$app = JFactory::getApplication();

if (!file_exists(JPATH_SITE.'/components/com_multipolls/multipolls.php'))
{   
    $app->enqueueMessage(JText::_('MOD_MULTIPOLLS_COMPONENT_NOT_FOUND'), 'error');
    return;
}

require_once __DIR__ . '/helper.php';
require_once JPATH_BASE.'/components/com_multipolls/captcha/generate.php';

JHtml::_('jquery.framework');

$id_poll = $params->get('id_poll', '');
$show_content = $params->get('show_content' , '1');
$show_title = $params->get('show_title' , '1');	
$curl_url = JFactory::getURI();

if($show_title)
{
	JFactory::getDocument()->addScript(JUri::root(true).'/modules/mod_multipolls/js/active-title.js');
}

$check_cookie = $app->input->get->cookie->get('multipoll'.$id_poll, '');

$post_id_poll = $app->input->get('id_poll', '', 'int');

if(!empty($post_id_poll) && !$check_cookie)
{	
	JSession::checkToken() or die( 'Invalid Token' );

	$user_captcha = trim(strtolower($app->input->get('captcha', '', 'string')));
	$real_crypt_captcha = $app->input->get('real-captcha', '', 'string');		
	$real_captcha = encrypt_decrypt('decrypt', $real_crypt_captcha);
		
	if (!$real_captcha || $user_captcha != $real_captcha)
	{				
		$app->redirect($curl_url, JText::_('MOD_MULTIPOLLS_CAPTCHA_ERROR'),'error');
	}

	$votes['r'] = $app->input->get('r', array(), 'ARRAY');	    	
	$votes['cb']  = $app->input->get('cb', array(), 'ARRAY');
	$votes['s'] = $app->input->get('s', array(), 'ARRAY');
	$votes['ta'] = $app->input->get('ta', array(), 'ARRAY');
	$votes['sta'] = $app->input->get('sta', array(), 'ARRAY');
	$votes['sta-text'] = $app->input->get('sta-text', array(), 'ARRAY');
	$votes['ro'] = $app->input->get('ro', array(), 'ARRAY');
	$votes['yn'] = $app->input->get('yn', array(), 'ARRAY');
		
	$poll_id = $app->input->get('id_poll', '0', 'int');

	JModelLegacy::addIncludePath(JPATH_SITE .'/components/com_multipolls/models');	
	$model = JModelLegacy::getInstance('Poll', 'MultipollsModel');

	if(!$model->checkQuestions($poll_id, $votes))
	{		
		$app->redirect($curl_url, JText::_('MOD_MULTIPOLLS_VOTES_ERROR'),'error');
	}

	$data = new stdClass;
	$data->votes = $votes;
	$data->ip = $app->input->server->get('REMOTE_ADDR');		
	$data->user_agent = JBrowser::getInstance()->getAgentString();
	$data->date_vote = JFactory::getDate();	

	if ($model->saveVote($data))
	{			
		$app->input->cookie->set('multipoll'.$id_poll, '1', time() + (3600 * 24), $app->get('cookie_path', '/'), $app->get('cookie_domain'));			
	    $app->redirect($curl_url, JText::_('MOD_MULTIPOLLS_VOTES_SUCCESS'),'message');	 	             
	}	
}

elseif ($check_cookie) 
{
	JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR .'/components/com_multipolls/models');	
	$model = JModelLegacy::getInstance('Stat', 'MultipollsModel');
	$stat = $model->getStat($id_poll);
	require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'show'));
}
else
{		
	JModelLegacy::addIncludePath(JPATH_SITE .'/components/com_multipolls/models');		
	$model = JModelLegacy::getInstance('Poll', 'MultipollsModel');
	$item = $model->getItem($id_poll);	

	$res_button = $params->get('result_button', '0');
	$show_text = $params->get('show_text' , '1');	

	JFactory::getDocument()->addScript(JUri::root(true).'/modules/mod_multipolls/js/submit.js');
	JFactory::getDocument()->addScript(JUri::root(true).'/modules/mod_multipolls/js/own-radio.js');

	if($res_button)
	{
		JFactory::getDocument()->addScript(JUri::root(true).'/modules/mod_multipolls/js/results.js');
	}	

	require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'default'));
}

?>