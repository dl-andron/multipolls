<?php

defined( '_JEXEC' ) or die;

class MultipollsControllerQuestion extends JControllerForm
{
	function __construct( $config = array() )
    {
        $this->view_list = 'questions';
        parent::__construct( $config );
    }

    public function save($key = null, $urlVar = null)
    {
        $model = $this->getModel('question');

        $task = $this->getTask();

        if ($id_question = $model->store()) 
        {
            $msg = JText::_( 'COM_MULTIPOLLS_SAVE_QUESTION' );
        } 
        else 
        {
            JFactory::getApplication()->enqueueMessage( JText::_( 'COM_MULTIPOLLS_SAVE_QUESTION_ERROR'), 'error');
        }

        switch ($task)
        {
            case 'apply':
                $link = 'index.php?option=com_multipolls&view=question&layout=edit&id='.$id_question;
                break;

            case 'save2new':
                $link = 'index.php?option=com_multipolls&view=question&layout=edit';
                break;               

            default:
                $link = 'index.php?option=com_multipolls&view=questions';
                break;
        }
        $this->setRedirect($link, $msg);
    }
}