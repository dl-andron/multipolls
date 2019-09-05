<?php

// No direct access
defined( '_JEXEC' ) or die;

/**
 * Controller for list current element
 * @author
 */
class MultipollsControllerLangs extends JControllerAdmin
{

    /**
     * Class constructor
     * @param array $config
     */
    function __construct( $config = array() )
    {
        parent::__construct( $config );
    }        

    public function publish()
    {
        $task = $this->getTask();
        $jinput = JFactory::getApplication()->input;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $ids  = $jinput->get('cid', '', 'array');        

        if ($task == 'unpublish')
        {
            $fields = $db->quoteName('published') . ' = ' . $db->quote(0);            
            $msg = JText::_('COM_MULTIPOLLS_PUBLISH_DOWN_LANGS');
        } 
        else if($task == 'publish')
        {
            $fields = $db->quoteName('published') . ' = ' . $db->quote(1);            
            $msg = JText::_('COM_MULTIPOLLS_PUBLISH_UP_LANGS');
        }

        $query->update($db->quoteName('#__multipolls_langs'))->set($fields);

        foreach ($ids as $id)
        {
            $query->where($db->quoteName('id_lang') . ' = ' . $db->quote($id),'OR');
        }

        $db->setQuery($query);
        $db->execute();

        $msg .= $db->getAffectedRows();

        $link = 'index.php?option=com_multipolls&view=langs';
        $this->setRedirect($link, $msg);
    }
}