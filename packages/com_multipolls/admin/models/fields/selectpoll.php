<?php

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSelectpoll extends JFormFieldList
{		 
	protected $type = 'Selectpoll';	
	
	public function getOptions()
	{
		$lang_tag = JFactory::getLanguage()->getTag();

		$db = JFactory::getDbo();			
 	
		$columnArr = $db->getTableColumns('#__multipolls_polls');

		$exist = false;
		$polls = array();

		foreach ($columnArr as $key => $value) 
		{
		    if ($key == 'name_'.$lang_tag)
		    {
		   	 	$exist = true;
		    }
		}

		if ($exist)
		{
			$query = $db->getQuery(true);
			$query->select($db->quoteName('id', 'value'));
			$query->select($db->quoteName('name_'.$lang_tag, 'text'));
			$query->from($db->quoteName('#__multipolls_polls'));
			$db->setQuery($query);
			$polls = $db->loadAssocList();			
		}		

		$options = array_merge(parent::getOptions(), $polls);
		return $options;
	}
}
