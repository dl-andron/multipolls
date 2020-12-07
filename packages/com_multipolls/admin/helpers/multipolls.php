<?php

defined( '_JEXEC' ) or die;

class MultipollsHelper
{
    /**
     * Добавление подменю
     * @param String $vName
     */
    static function addSubmenu( $vName )
    {
        JHtmlSidebar::addEntry(
            JText::_( 'COM_MULTIPOLLS_ITEM_SUBMENU_POLLS' ),
            'index.php?option=com_multipolls&view=polls',
            $vName == 'polls' );
            
        JHtmlSidebar::addEntry(
            JText::_( 'COM_MULTIPOLLS_ITEM_SUBMENU_QUESTIONS' ),
            'index.php?option=com_multipolls&view=questions',
            $vName == 'questions' );
            
        JHtmlSidebar::addEntry(
            JText::_( 'COM_MULTIPOLLS_ITEM_SUBMENU_ANSWERS' ),
            'index.php?option=com_multipolls&view=answers',
            $vName == 'answers' );        

        JHtmlSidebar::addEntry(
            JText::_( 'COM_MULTIPOLLS_ITEM_SUBMENU_LANGS' ),
            'index.php?option=com_multipolls&view=langs',
            $vName == 'langs' );        
    }

    /**
     * Получаем доступные действия для текущего пользователя
     * @return JObject
     */
    public static function getActions()
    {
        $user = JFactory::getUser();
        $result = new JObject;
        $assetName = 'com_multipolls';
        $actions = JAccess::getActions( $assetName );
        foreach ( $actions as $action ) {
            $result->set( $action->name, $user->authorise( $action->name, $assetName ) );
        }
        return $result;
    }

    public static function installNewLangs()
    {
        $db =JFactory::getDBO();
       
        $session =JFactory::getSession();
        $joomlaLangs = JLanguageHelper::getLanguages();
        $checkedlanguage = $session->get('multipolls_checked_language');
        if (is_array($checkedlanguage))
        {
            $newlanguages = 0;
            foreach($joomlaLangs as $lang)
            {
                if (!in_array($lang->lang_code, $checkedlanguage)) $newlanguages++;  
            }
            if ($newlanguages==0) return 0;
        }

        $query = "select * from #__multipolls_langs";
        $db->setQuery($query);
        $pollsLangs = $db->loadObjectList();
        $pollsLangsTag = array();
        foreach($pollsLangs as $lang)
        {
            $pollsLangsTag[] = $lang->language;
        }

        $checkedlanguage = array();       
        
        foreach($joomlaLangs as $lang)
        {
            $checkedlanguage[] = $lang->lang_code;
            if (!in_array($lang->lang_code, $pollsLangsTag))
            {                
                $query='ALTER TABLE `#__multipolls_polls` ADD `name_'.$lang->lang_code.'` varchar(128) NOT NULL, ADD `text_'.$lang->lang_code.'` text NOT NULL';
                $db->setQuery($query);
                if (!$db->query())
                {
                    JError::raiseWarning(500, "Error install new languages:<br>".$db->getErrorMsg());            
                }

                $query='ALTER TABLE `#__multipolls_questions` ADD `name_'.$lang->lang_code.'` varchar(128) NOT NULL, ADD `name_own_'.$lang->lang_code.'` varchar(128)';
                $db->setQuery($query);
                if (!$db->query())
                {
                    JError::raiseWarning(500, "Error install new languages:<br>".$db->getErrorMsg());            
                }

                $query='ALTER TABLE `#__multipolls_answers` ADD `name_'.$lang->lang_code.'` varchar(128) NOT NULL';
                $db->setQuery($query);
                if (!$db->query())
                {
                    JError::raiseWarning(500, "Error install new languages:<br>".$db->getErrorMsg());            
                }                             
                  
                $query = "insert into #__multipolls_langs set `language`='".$db->escape($lang->lang_code)."', `name`='".$db->escape($lang->title)."', `published`='1'";
                $db->setQuery($query);
                if (!$db->query())
                {
                    JError::raiseWarning(500, "Error install new languages:<br>".$db->getErrorMsg());            
                }
                    
                JError::raiseNotice("", JText::_("COM_MULTIPOLLS_NEW_LANGS_INSTALLED").": ".$lang->title);                    
                
            }
        }    
        $session->set("multipolls_checked_language", $checkedlanguage);
        return 1;
    }
}