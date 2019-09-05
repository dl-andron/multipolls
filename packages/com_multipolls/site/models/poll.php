<?php

defined('_JEXEC') or die;

class MultipollsModelPoll extends JModelItem
{	

	public function getCookie()
	{
		$app = JFactory::getApplication('site');
		$pk = $app->input->getInt('id');
		return $app->input->get->cookie->get('multipoll'.$pk, '');		
	}

	protected function populateState()
	{
		$app = JFactory::getApplication('site');
		$pk = $app->input->getInt('id');
		$this->setState('poll.id', $pk);

		$result_button = $app->input->getInt('result_button');
		$this->setState('poll.result_button', $result_button);

		$show_poll_name = $app->input->getInt('show_poll_name');
		$this->setState('poll.show_poll_name', $show_poll_name);

		$params = $app->getParams();
		$this->setState('params', $params);
	}

	public function getItem($pk = null)
	{
		$cur_lang = JFactory::getLanguage()->getTag();
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('poll.id');
		$result_button = (!empty($result_button)) ? $result_button : (int) $this->getState('poll.result_button');
		$show_poll_name = (!empty($show_poll_name)) ? $show_poll_name : (int) $this->getState('poll.show_poll_name');
		
		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);

			$query->select($db->quoteName('name_'.$cur_lang, 'name'));
			$query->select($db->quoteName('text_'.$cur_lang, 'text'));
			$query->select($db->quoteName('id'));
			$query->select($db->quoteName('published'));
	        $query->from($db->quoteName('#__multipolls_polls'));
	        $query->where($db->quoteName('id') . ' = ' . $db->quote($pk));  

	        $db->setQuery($query);
			$poll = $db->loadAssoc();
			
			if (empty($poll))
			{
				return JError::raiseError(404, JText::_('COM_MULTIPOLLS_POLL_NOT_FOUND'));
			}
			elseif ( $poll['published'] != 1)
			{	
				return JError::raiseError(404, JText::_('COM_MULTIPOLLS_POLL_NOT_PUBLISHED'));
			}
			
			$data = new stdClass;
			$data->poll_name = $poll['name'];
			$data->poll_text = $poll['text'];
			$data->poll_id = $poll['id'];

			$query = $db->getQuery(true);			
			$query->select($db->quoteName('img_url', 'q_image'));		
			$query->select($db->quoteName('id', 'qid'));
			$query->select($db->quoteName('id_type'));
			$query->select($db->quoteName('name_own_'.$cur_lang, 'name_own'));	
			$query->select($db->quoteName('name_'.$cur_lang, 'q_name'));	       	        
	        $query->from($db->quoteName('#__multipolls_questions'));	       	       
	        $query->where($db->quoteName('id_poll') . ' = ' . $db->quote($pk)); 
	        $query->where($db->quoteName('published') . ' = ' . $db->quote(1));      
	        $query->order('ordering'); 
	       
	        $db->setQuery($query);
	        $questions = $db->loadAssocList();

	        $id_questions = array();
	        foreach ($questions as $question)
	        {
	        	$id_questions[] = $question['qid'];
	        }	      

	        $query = $db->getQuery(true);			
			$query->select($db->quoteName('img_url', 'a_image'));		
			$query->select($db->quoteName('id', 'aid'));				
			$query->select($db->quoteName('name_'.$cur_lang, 'a_name'));	
			$query->select($db->quoteName('id_question'));       	        
	        $query->from($db->quoteName('#__multipolls_answers'));         
	        $query->where($db->quoteName('published') . ' = ' . $db->quote(1)); 
	        $query->where($db->quoteName('id_question') . ' IN ('. implode(",", $id_questions). ')' );   
	        $query->order('ordering');

	        $db->setQuery($query);
	        $answers = $db->loadAssocList();

	        //формирую тело опроса
	        foreach ($questions as $q) 
	        {	
	        	$data->poll_content[$q['qid']]['q_name'] = $q['q_name'];
	        	$data->poll_content[$q['qid']]['q_image'] = $q['q_image'];
	        	$data->poll_content[$q['qid']]['id_type'] = $q['id_type'];	
	        	$data->poll_content[$q['qid']]['name_own'] = $q['name_own'];        	
	        }
	       	
	       	foreach ($answers as $a) 
	       	{
	       		$data->poll_content[$a['id_question']]['answers'][$a['aid']] = $a['a_name'];
	       		$data->poll_content[$a['id_question']]['images'][$a['aid']] = $a['a_image'];
	       	}
	       
	        $data->result_button = $result_button;
	        $data->show_poll_name = $show_poll_name;

			$this->_item = $data;
		}
		catch (Exception $e)
		{
			if ($e->getCode() == 404)
			{
				// Need to go thru the error handler to allow Redirect to work.
				JError::raiseError(404, $e->getMessage());
			}
			else
			{
				$this->setError($e);
				$this->_item = false;
			}
		}		

		return $this->_item;
	}

	//метод проверяет наличие ответов на вопросы с radio-button
	public function checkQuestions($id_poll, $votes)
	{			
		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);		
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__multipolls_questions'));
			$query->where($db->quoteName('id_poll') . ' = '. $db->quote($id_poll));	
			$query->where($db->quoteName('id_type') . 'IN (1,6)');
			$query->where($db->quoteName('published') . ' = ' . $db->quote('1'));	
			$db->setQuery($query);	
			$qid = $db->loadColumn();
		}
		catch (Exception $e)
		{		    
		    $this->setError($e->getMessage());
			return false;
		}
		
		$check = array();	

		foreach ($votes['r'] as $key => $value) 
		{
			$check[] = $key;
		}		
						

		foreach ($votes['ro'] as $key => $value) 
		{
			$check[] = $key;

			if ($value == 'custom' && trim($votes['ro']['custom-'.$key]) == '') 
			{				
				$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
				return false;
			}
		}		

		foreach ($qid as $value) 
		{ 
			if (!in_array($value, $check)) 
			{
				$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
				return false;
			}
		}		

		return true;
	}

	public function saveVote($data)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);		

		$db->transactionStart();

		if(!empty($data->votes['r']))
		{
			try
			{					
				$columns = array('id_question','id_answer', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_radio_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['r'] as $key => $vote) 
				{			
				    $rows = array($db->quote($key), $db->quote($vote), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
				    $query->values(implode(',', $rows));		       
				}
			
			    $db->setQuery($query);
				$db->execute();
			}

			catch (Exception $e)
			{	
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;		    
			}
		}

		$query = $db->getQuery(true);

		if(!empty($data->votes['cb']))
		{
			try
			{					
				$columns = array('id_question','answers', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_cb_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['cb'] as $key => $vote) 
				{			
				    $rows = array($db->quote($key), $db->quote(implode(",", $vote)), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
				    $query->values(implode(',', $rows));		       
				}
			
			    $db->setQuery($query);
				$db->execute();
			}

			catch (Exception $e)
			{	
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;		    
			}
		}

		$query = $db->getQuery(true);

		if(!empty($data->votes['s']))
		{
			try
			{					
				$columns = array('id_question', 'id_answer', 'value', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_select_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['s'] as $key => $vote) 
				{		
					if (!is_numeric($vote) || $vote < 1 || $vote > 10) 
					{
					    $db->transactionRollback();
						$this->setError(JText::_('COM_MULTIPOLLS_INCORRECT_ANSWER'));
						return false;
					}
						
					$qa = explode("-",$key);
				    $rows = array($db->quote($qa[0]), $db->quote($qa[1]), $db->quote($vote), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
				    $query->values(implode(',', $rows));		       
				}
			
			    $db->setQuery($query);
				$db->execute();
			}

			catch (Exception $e)
			{	
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;		    
			}
		}

		$query = $db->getQuery(true);

		if(!empty($data->votes['ta']))
		{
			try
			{					
				$columns = array('id_question', 'id_answer', 'text', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_text_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['ta'] as $key => $vote) 
				{	
					if(trim($vote) != '')
					{
						$qa = explode("-",$key);
					    $rows = array($db->quote($qa[0]), $db->quote($qa[1]), $db->quote($vote), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
					    $query->values(implode(',', $rows));
					}					
							       
				}

				if($query->values)
				{
					$db->setQuery($query);
					$db->execute();
				}		
			    
			}

			catch (Exception $e)
			{	
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;		    
			}
		}

		$query = $db->getQuery(true);

		if(!empty($data->votes['sta']))
		{
			try
			{					
				$columns = array('id_question', 'id_answer', 'value', 'text', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_select_text_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['sta'] as $key => $vote) 
				{					
					if (!is_numeric($vote) || $vote < 1 || $vote > 10) 
					{
					    $db->transactionRollback();
						$this->setError(JText::_('COM_MULTIPOLLS_INCORRECT_ANSWER'));
						return false;
					}	

					$qa = explode("-",$key);
				    $rows = array($db->quote($qa[0]), $db->quote($qa[1]), $db->quote($vote), $db->quote($data->votes['sta-text'][$key]), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
				    $query->values(implode(',', $rows));		       
				}
			
			    $db->setQuery($query);
				$db->execute();
			}

			catch (Exception $e)
			{	
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;		    
			}
		}

		$query = $db->getQuery(true);

		if(!empty($data->votes['ro']))
		{
			try
			{					
				$columns = array('id_question','id_answer', 'own_answer' ,'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_radio_own_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['ro'] as $key => $vote) 
				{	
					if(strpos($key, 'custom-') !== false)
					{	
						continue;						
					}					
					
					elseif($vote == 'custom')
					{
						$rows = array($db->quote($key), $db->quote(''), $db->quote($data->votes['ro']['custom-'.$key]), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));						
					}

					else
					{
						$rows = array($db->quote($key), $db->quote($vote), $db->quote(''), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
					}					
				   
				    $query->values(implode(',', $rows));		       
				}
			
			    $db->setQuery($query);
				$db->execute();
			}

			catch (Exception $e)
			{	
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;		    
			}
		}
		
		
		
		
		$query = $db->getQuery(true);
		
		
		
		if(!empty($data->votes['yn']))
		{
			try
			{					
				$columns = array('id_question', 'id_answer', 'value', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_yn_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['yn'] as $key => $vote) 
				{		
					// if (!is_numeric($vote) || $vote < 1 || $vote > 10) 
					// {
					    // $db->transactionRollback();
						// $this->setError(JText::_('COM_MULTIPOLLS_INCORRECT_ANSWER'));
						// return false;
					// }
						
					$qa = explode("-",$key);
				    $rows = array($db->quote($qa[0]), $db->quote($qa[1]), $db->quote($vote), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
				    $query->values(implode(',', $rows));		       
				}
			
			    $db->setQuery($query);
				$db->execute();
			}

			catch (Exception $e)
			{	
				$db->transactionRollback();
				$this->setError($e->getMessage());
				return false;		    
			}
		}
		

		$db->transactionCommit();

		return true;
	}	

	public function getStat()
	{		
		$app = JFactory::getApplication('site');
		$pk = $app->input->getInt('id');

		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR .'/models');
		
		$model = JModelLegacy::getInstance('Stat', 'MultipollsModel');
		
		return $model->getStat($pk);
	}

	public function generateQuestion($id_question, $question, $context = 'component')
	{	
		$result = "<h4>".$question['q_name']."</h4>";		

		if ($question['q_image'] !='') 
		{
			$result .= "<div class='img-question'><img src=".JUri::base(true)."/".$question['q_image']."></div>";
		}

		switch ($question['id_type']) 
		{
			case '1':
				$result .= $this->_generateRadio($id_question, $question);
				break;

			case '2':
				$result .= $this->_generateCheckbox($id_question, $question);
				break;

			case '3':
				$result .= $this->_generateSelect($id_question, $question);
				break;

			case '4':
				$result .= $this->_generateTextArea($id_question, $question);
				break;

			case '5':
				$result .= $this->_generateSelectTextarea($id_question, $question);
				break;

			case '6':
				$result .= $this->_generateRadioOwn($id_question, $question, $context);
				break;	
				
			case '7':
				$result .= $this->_generateYn($id_question, $question, $context);
				break;

			default:				
				break;
		}		

		return $result;
	}

	private function _generateRadio($id_question, $question)
	{	
		$answers = '';

		if(isset($question['answers']))
		{
			foreach ($question['answers'] as $id => $answer) 
			{
				$answers .= "<label class='radio'>";
				
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}							
													
				$answers .= "<input type='radio' name='r[".$id_question."]' value=".$id." required>".$answer."</label>";					
			}			
		}

		return $answers;
	}

	private function _generateCheckbox($id_question, $question)
	{	
		$answers = '';

		if(isset($question['answers']))
		{
			foreach ($question['answers'] as $id => $answer) 
			{
				$answers .= "<label class='checkbox'>";
				
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}							
													
				$answers .= "<input type='checkbox' name='cb[".$id_question."][]' value=".$id.">".$answer."</label>";					
			}			
		}

		return $answers;
	}

	private function _generateSelect($id_question, $question)
	{
		$answers = '';

		if(isset($question['answers']))
		{
			foreach ($question['answers'] as $id => $answer) 
			{
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}

				$answers .=	"<label for='select".$id."'>".$answer."</label>";				

				$answers .=	"<select style='width:auto' name='s[".$id_question."-".$id."]' id='select".$id."'>";

				for ($i = 1; $i <= 10; $i++) 
				{ 
					$answers .= "<option>".$i."</option>";
				}

				$answers .= "</select>";
			}			
		}

		return $answers;
	}

	private function _generateTextArea($id_question, $question)
	{
		$answers = '';

		if(isset($question['answers']))
		{
			foreach ($question['answers'] as $id => $answer) 
			{	
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}

				$answers .= "<p>".$answer."</p>";															
			
				$answers .= "<textarea name='ta[".$id_question."-".$id."]'></textarea>";					
			}
		}

		return $answers;
	}

	private function _generateSelectTextarea($id_question, $question)
	{
		$answers = '';

		if(isset($question['answers']))
		{
			foreach ($question['answers'] as $id => $answer) 
			{
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}

				$answers .=	"<label for='select".$id."'>".$answer."</label>";				

				$answers .=	"<select style='width:auto' name='sta[".$id_question."-".$id."]' id='select".$id."'>";

				for ($i = 1; $i <= 10; $i++) 
				{ 
					$answers .= "<option>".$i."</option>";
				}

				$answers .= "</select>";

				$answers .= "<textarea name='sta-text[".$id_question."-".$id."]'></textarea>";
			}			
		}

		return $answers;
	}

	private function _generateRadioOwn($id_question, $question, $context)
	{
		$answers = '';

		if(isset($question['answers']))
		{
			foreach ($question['answers'] as $id => $answer) 
			{
				$answers .= "<label class='radio'>";
				
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}							
													
				$answers .= "<input type='radio' name='ro[".$id_question."]' value=".$id." required>".$answer."</label>";					
			}

			$answers .= "<label class='radio'><input type='radio' class='own-radio' name='ro[".$id_question."]' value='custom' required>";

			if (trim($question["name_own"]) != '')
			{
				$answers .= $question["name_own"];
			}

			$answers .="<input class='own-input' name='ro[custom-".$id_question."]'";

			if (trim($question["name_own"]) != '')
			{
				$answers .= "style='margin-left: 10px;'";
			}

			if ($context == 'component')
			{	
				$answers .= "type='text' placeholder='".JText::_('COM_MULTIPOLLS_OWN_ANSWER')."'></label>";				
			}

			elseif ($context == 'module')
			{	
				$answers .= "type='text' placeholder='".JText::_('MOD_MULTIPOLLS_OWN_ANSWER')."'></label>";				
			}
							
		}

		return $answers;
	}
		
	private function _generateYn($id_question, $question, $context)
	{
		$answers = '';

		if(isset($question['answers']))
		{
			foreach ($question['answers'] as $id => $answer) 
			{
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}

				$answers .=	"<label for='select".$id."'>".$answer."</label>";				

				$answers .= "<label style='width: 50px;display: inline-block;' ><input type='radio' name='yn[".$id_question."-".$id."]' value='y' required> ";

				if($context == 'component')
				{
					$answers .= JText::_('COM_MULTIPOLLS_YES');
				}

				elseif ($context == 'module')
				{
					$answers .= JText::_('MOD_MULTIPOLLS_YES');
				}

				$answers .=  "</label>"; 

				$answers .= "<label style='width: 50px;display: inline-block;' ><input type='radio' name='yn[".$id_question."-".$id."]' value='n' required> ";

				if ($context == 'component')
				{
					$answers .= JText::_('COM_MULTIPOLLS_NO');
				}

				elseif ($context == 'module')
				{
					$answers .= JText::_('MOD_MULTIPOLLS_NO');
				}

				$answers .=  "</label>"; 
	
				
			}			
		}

		return $answers;
	}

	
}	