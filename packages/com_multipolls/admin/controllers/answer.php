<?php

defined( '_JEXEC' ) or die;

class MultipollsControllerAnswer extends JControllerForm
{
	function __construct( $config = array() )
    {
        $this->view_list = 'answers';
        parent::__construct( $config );
    }

    public function save($key = null, $urlVar = null)
    {
        $model = $this->getModel('answer');

        $task = $this->getTask();

        if ($id_answer = $model->store()) 
        {
            $msg = JText::_( 'COM_MULTIPOLLS_SAVE_ANSWER' );
        } 
        else 
        {
            JFactory::getApplication()->enqueueMessage( JText::_( 'COM_MULTIPOLLS_SAVE_ANSWER_ERROR'), 'error');
        }

        switch ($task)
        {
            case 'apply':
                $link = 'index.php?option=com_multipolls&view=answer&layout=edit&id='.$id_answer;
                break;

            case 'save2new':
                $link = 'index.php?option=com_multipolls&view=answer&layout=edit';
                break;               

            default:
                $link = 'index.php?option=com_multipolls&view=answers';
                break;
        }
        $this->setRedirect($link, $msg);
    }
}