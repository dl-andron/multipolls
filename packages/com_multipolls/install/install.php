<?php

defined('_JEXEC') or die();

class com_multipollsInstallerScript
{

	function install($parent)
	{
		$joomlaLangs = JLanguageHelper::getLanguages();		

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$sqlpolls = array();
		$sqlqa = array();
		$sqlqoa = array();
		$values = array();

		foreach ($joomlaLangs as $lang) 
		{
			$values[] = "{$db->quote($lang->lang_code)}, {$db->quote($lang->title)}, 1";
			$sqlpolls[] = 'ADD `name_'.$lang->lang_code.'` varchar(500) NOT NULL, ADD `text_'.$lang->lang_code.'` text NOT NULL';
			$sqlqa[] = 'ADD `name_'.$lang->lang_code.'` varchar(500) NOT NULL';
			$sqlqoa[] = 'ADD `name_own_'.$lang->lang_code.'` varchar(300)';
		}		
		
		$query='ALTER TABLE `#__multipolls_polls` '.implode(",", $sqlpolls);
	    $db->setQuery($query);
	    if (!$db->query())
	    {
            JError::raiseWarning(500, "Error install new languages:<br>".$db->getErrorMsg());            
        }

        $query='ALTER TABLE `#__multipolls_questions` '.implode(",", $sqlqa).','.implode(",", $sqlqoa);
        $db->setQuery($query);
	    if (!$db->query())
	    {
            JError::raiseWarning(500, "Error install new languages:<br>".$db->getErrorMsg());            
        }

        $query='ALTER TABLE `#__multipolls_answers` '.implode(",", $sqlqa);
        $db->setQuery($query);
	    if (!$db->query())
	    {
            JError::raiseWarning(500, "Error install new languages:<br>".$db->getErrorMsg());            
        }

        $query = $db->getQuery(true);
        $columns = array('language', 'name', 'published');				
		$query
		    ->insert($db->quoteName('#__multipolls_langs'))
		    ->columns($db->quoteName($columns))
		    ->values($values);
		$db->setQuery($query);
		$db->execute();
	}

	function update($parent) 
    {
    	$joomlaLangs = JLanguageHelper::getLanguages();	

    	$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$sqlqoa = array();

		$query->select('COUNT(*)');
		$query->from('INFORMATION_SCHEMA.COLUMNS');
		$query->where($db->quoteName('table_name') . ' = '. $db->quote(JFactory::getConfig()->get('dbprefix').'multipolls_questions'));	
		$query->where($db->quoteName('table_schema') . ' = '. $db->quote(JFactory::getConfig()->get('db')));	
		$query->where($db->quoteName('column_name') . ' LIKE '. $db->quote('name_own_%'));		
		$db->setQuery($query);		
		$count = $db->loadResult();
		

    	foreach ($joomlaLangs as $lang) 
		{
			if($count == 0)
			{
				$sqlqoa[] = 'ADD `name_own_'.$lang->lang_code.'` varchar(300)';
			}
			
			$sqlpolls[] = 'MODIFY `name_'.$lang->lang_code.'` varchar(500) NOT NULL';
			$sqlqa[] = 'MODIFY `name_'.$lang->lang_code.'` varchar(500) NOT NULL';
		}

		if($count == 0)
		{	
			$query='ALTER TABLE `#__multipolls_questions` '.implode(",", $sqlqoa);
	        $db->setQuery($query);
		    if (!$db->query())
		    {
	            JError::raiseWarning(500, "Error update multipolls_questions:<br>".$db->getErrorMsg());            
	        }
	    } 

        $query='ALTER TABLE `#__multipolls_polls` '.implode(",", $sqlpolls);
	    $db->setQuery($query);
	    if (!$db->query())
	    {
            JError::raiseWarning(500, "Error install multipolls_polls:<br>".$db->getErrorMsg());            
        }

        $query='ALTER TABLE `#__multipolls_questions` '.implode(",", $sqlqa);
        $db->setQuery($query);
	    if (!$db->query())
	    {
            JError::raiseWarning(500, "Error install multipolls_questions:<br>".$db->getErrorMsg());            
        }

        $query='ALTER TABLE `#__multipolls_answers` '.implode(",", $sqlqa);
        $db->setQuery($query);
	    if (!$db->query())
	    {
            JError::raiseWarning(500, "Error install multipolls_answers:<br>".$db->getErrorMsg());            
        }
    }

	function uninstall($parent)
	{
    }

}