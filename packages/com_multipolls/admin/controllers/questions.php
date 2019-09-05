<?php

defined( '_JEXEC' ) or die;

class MultipollsControllerQuestions extends JControllerAdmin
{	
	function __construct( $config = array() )
    {    	
        parent::__construct( $config );
    }

    public function getModel( $name = 'Question', $prefix = 'MultipollsModel', $config = array( 'ignore_request' => true ) )
    {
        return parent::getModel( $name, $prefix, $config );
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
            $msg = JText::_('COM_MULTIPOLLS_PUBLISH_DOWN_QUESTIONS');  
        }
        else if($task == 'publish')
        {
            $fields = $db->quoteName('published') . ' = ' . $db->quote(1);
            $msg = JText::_('COM_MULTIPOLLS_PUBLISH_UP_QUESTIONS');
        }

        $query->update($db->quoteName('#__multipolls_questions'))->set($fields);

        foreach ($ids as $id)
        {
            $query->where($db->quoteName('id') . ' = ' . $db->quote($id),'OR');
        }

        $db->setQuery($query);
        $db->execute();

        $msg .= $db->getAffectedRows();

        $link = 'index.php?option=com_multipolls&view=questions';
        $this->setRedirect($link, $msg);
	}
}    