<?php

defined( '_JEXEC' ) or die;


class MultipollsViewPoll extends JViewLegacy
{
	protected $item;
	protected $params;
	protected $state;
	protected $cookie;
	public $result;

	public function display( $tpl = null )
	{	
		$this->cookie = $this->get('Cookie');

		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}
		
		if ($this->cookie)
		{	
			$this->result = $this->get('Stat');
			$this->setLayout('show');
			parent::display( $tpl );
		}
		else
		{
			$this->item  = $this->get('Item');
			$this->state = $this->get('State');
			$this->params = $this->state->get('params');			

			$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

			$this->_prepareDocument();
			parent::display($tpl);
		}		
	}

	protected function _generateQuestion($id_question, $question)
	{
		return $this->getModel()->generateQuestion($id_question, $question);
	}

	protected function _prepareDocument()
	{	
		$title = $this->params->get('page_title', '');		

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
	}
}