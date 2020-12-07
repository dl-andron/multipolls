<?php

defined( '_JEXEC' ) or die;

class MultipollsControllerPoll extends JControllerForm
{
	function __construct( $config = array() )
    {
        $this->view_list = 'polls';
        parent::__construct( $config );
    }

    public function save($key = null, $urlVar = null)
    {
        $model = $this->getModel('poll');

        $task = $this->getTask();

        if ($id_poll = $model->store()) {
            $msg = JText::_( 'COM_MULTIPOLLS_SAVE_POLL' );
        } else {
            JFactory::getApplication()->enqueueMessage( JText::_( 'COM_MULTIPOLLS_SAVE_POLL_ERROR'), 'error');
        }

        switch ($task)
        {
            case 'apply':
                $link = 'index.php?option=com_multipolls&view=poll&layout=edit&id='.$id_poll;
                break;

            case 'save2new':
                $link = 'index.php?option=com_multipolls&view=poll&layout=edit';
                break;               

            default:
                $link = 'index.php?option=com_multipolls&view=polls';
                break;
        }
        $this->setRedirect($link, $msg);
    }

    public function show()
    {   
        $jinput = JFactory::getApplication()->input;
        $id_poll = $jinput->get('id', '', 'int');

        $model = $this->getModel('stat');
        $poll_stat = $model->getStat($id_poll);

        $view = $this->getView('poll','html');
        $view->setLayout('show');
        $view->result = $poll_stat;       

        $view->showResult();       
    }

    public function clearResults()
    {   
        $jinput = JFactory::getApplication()->input;
        $id_poll = $jinput->get('id', '', 'int');   

        $link = 'index.php?option=com_multipolls&view=polls';

        $model = $this->getModel('stat');

        if ($model->clearResults($id_poll)){           
            $this->setRedirect($link, JText::_( 'COM_MULTIPOLLS_CLEAR_STAT_CLEARED' ));
        } else {
            $this->setRedirect($link, $model->getError(), 'error');            
        }        
    }
}