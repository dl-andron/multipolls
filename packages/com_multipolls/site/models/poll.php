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

		$hide_answers = $app->input->getInt('hide_answers');
		$this->setState('poll.hide_answers', $hide_answers);

		$show_result_after_vote = $app->input->getInt('show_result_after_vote');
		$this->setState('poll.show_result_after_vote', $show_result_after_vote);

		$params = $app->getParams();
		$this->setState('params', $params);
	}

	public function getItem($pk = null)
	{
		$cur_lang = JFactory::getLanguage()->getTag();
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('poll.id');
		$result_button = (!empty($result_button)) ? $result_button : (int) $this->getState('poll.result_button');
		$show_poll_name = (!empty($show_poll_name)) ? $show_poll_name : (int) $this->getState('poll.show_poll_name');
		$hide_answers = (!empty($hide_answers)) ? $hide_answers : (int) $this->getState('poll.hide_answers');
		$show_result_after_vote = (!empty($show_result_after_vote)) ? $show_result_after_vote : (int) $this->getState('poll.show_result_after_vote');

		//разрешить показ вопросов по одному
		//задаю по умолчанию
		$allow_hidden_answers = true;
		
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
			$query->select($db->quoteName('required'));
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
	        	$data->poll_content[$q['qid']]['required'] = $q['required']; 

	        	if(!in_array($q['id_type'], array(1,6,7)))
	        	{
	        		$allow_hidden_answers = false;
	        	}     	
	        }
	       	
	       	foreach ($answers as $a) 
	       	{
	       		$data->poll_content[$a['id_question']]['answers'][$a['aid']] = $a['a_name'];
	       		$data->poll_content[$a['id_question']]['images'][$a['aid']] = $a['a_image'];
	       	}
	       
	        $data->result_button = $result_button;
	        $data->show_poll_name = $show_poll_name;
	        $data->hide_answers = $hide_answers;
	        $data->allow_hidden_answers = $allow_hidden_answers;
	        $data->show_result_after_vote = $show_result_after_vote;

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

	//возвращает для вопроса максимальное значение ранжирования
	//по умолчанию 10
	private function _getSelectRangeValue($id_question)
	{
		$db = $this->getDbo();
        $query = $db->getQuery(true); 

        $query->select($db->quoteName('max_range'));
        $query->from($db->quoteName('#__multipolls_select_range'));
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));  

        $db->setQuery($query);
        $range = $db->loadResult();

        return !empty($range) ? $range : 10;
	}

	//метод исключает ответы не касающиеся выбранного опроса
	public function cleanQuestions($id_poll, $votes)
	{		
		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);		
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__multipolls_questions'));
			$query->where($db->quoteName('id_poll') . ' = '. $db->quote($id_poll));				
			$query->where($db->quoteName('published') . ' = ' . $db->quote('1'));	
			$db->setQuery($query);	
			$id_questions = $db->loadColumn();			
		}
		catch (Exception $e)
		{		    
		    $this->setError($e->getMessage());
			return false;
		}

		foreach($votes as $type => $values){
			switch ($type) {
				case "r":
					foreach($values as $key => $value){
						if(!in_array($key, $id_questions)){
							unset($votes['r'][$key]);
						}				
					}								
					break;
				case "cb":						
					foreach($values as $key => $value){
						if(!in_array($key, $id_questions)){
							unset($votes['cb'][$key]);
						}				
					}								
					break;
				case "s":	
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['s'][$key]);
						}				
					}					
					break;
				case "ta":	
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['ta'][$key]);
						}				
					}					
					break;
				case "sta":		
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['sta'][$key]);
						}				
					}					
					break;
				case "sta-text":		
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['sta-text'][$key]);
						}				
					}					
					break;	
				case "ro":	
					foreach($values as $key => $value){
						$question = explode('-', $key);
						if(!empty($question[1])){
							$id_question = $question[1];
						} else {
							$id_question = $question[0];
						}
						if(!in_array($id_question, $id_questions)){
							unset($votes['ro'][$key]);
							unset($votes['ro']['custom-'.$key]);
						}
					}					
					break;
				case "yn":
					foreach($values as $key => $value){
						$id_question = explode('-', $key)[0];
						if(!in_array($id_question, $id_questions)){
							unset($votes['yn'][$key]);
						}				
					}						
					break;
				case "cbo":
					foreach($values as $key => $value){
						$question = explode('-', $key);
						if(!empty($question[1])){
							$id_question = $question[1];
						} else {
							$id_question = $question[0];
						}
						if(!in_array($id_question, $id_questions)){
							unset($votes['cbo'][$key]);
							unset($votes['cbo']['custom-'.$key]);
						}
					}					
					break;
				case "priority":
					foreach($values as $key => $value){
						if(!in_array($key, $id_questions)){
							unset($votes['priority'][$key]);
						}				
					}								
					break;					
				default:				
					break;													
			}
		}	

		return $votes;
	}

	//метод проверяет наличие ответов на вопросы
	public function checkQuestions($id_poll, $votes)
	{
		//получаю вопросы по выбранному опросу в виде массива 	
		// Array ( [id_question] => Array ( [id_question] => 1 [required] => 0 [type] => r [answers] => Array ( [0] => id_answer1 [1] => id_answer2) ...)
		try{
			$db = $this->getDbo();
			$query = $db->getQuery(true);		
			$query->select($db->quoteName(array('id', 'required')));
			$query->select("CASE".
							" WHEN " . $db->quoteName('id_type') . " = 1 THEN 'r'".
                            " WHEN " . $db->quoteName('id_type') . " = 2 THEN 'cb'".
                            " WHEN " . $db->quoteName('id_type') . " = 3 THEN 's'".
                            " WHEN " . $db->quoteName('id_type') . " = 4 THEN 'ta'".
                            " WHEN " . $db->quoteName('id_type') . " = 5 THEN 'sta'".
                            " WHEN " . $db->quoteName('id_type') . " = 6 THEN 'ro'".
                            " WHEN " . $db->quoteName('id_type') . " = 7 THEN 'yn'".
                            " WHEN " . $db->quoteName('id_type') . " = 8 THEN 'cbo'".
                            " WHEN " . $db->quoteName('id_type') . " = 9 THEN 'priority'".
                            " END AS " . $db->quoteName('type'));
			$query->from($db->quoteName('#__multipolls_questions'));
			$query->where($db->quoteName('id_poll') . ' = '. $db->quote($id_poll));				
			$query->where($db->quoteName('published') . ' = ' . $db->quote('1'));	
			$db->setQuery($query);	
			$questionsByPoll = $db->loadAssocList('id');
		} catch (Exception $e) {		    
		    $this->setError($e->getMessage());
			return false;
		}

		foreach($questionsByPoll as $key => $q){
			try {
				$query = $db->getQuery(true);		
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__multipolls_answers'));
				$query->where($db->quoteName('id_question') . ' = '. $db->quote($q['id']));				
				$query->where($db->quoteName('published') . ' = ' . $db->quote('1'));	
				$db->setQuery($query);	
				$questionsByPoll[$key]['answers'] = $db->loadColumn();
			} catch (Exception $e) {		    
				$this->setError($e->getMessage());
				return false;
			}	
		}
		
		foreach($questionsByPoll as $idQuestion => $questionInfo){
			switch ($questionInfo['type']) {
				case "r":	
					if($questionInfo['required'] && 
							(
								empty($votes['r'][$idQuestion]) || 
								(!empty($votes['r'][$idQuestion]) && !in_array($votes['r'][$idQuestion],$questionInfo['answers']))
							)
						){						
						$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
						return false;
					}																
					break;
				case "cb":
					if($questionInfo['required'] && 
							(
								empty($votes['cb'][$idQuestion]) || 
								(!empty($votes['cb'][$idQuestion]) && count(array_intersect($questionInfo['answers'], $votes['cb'][$idQuestion])) == 0)
							)
						){			
						$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
						return false;
					}			
					break;
				case "s":					
					if($questionInfo['required']) {
						foreach($questionInfo['answers'] as $idAnswer){
							if(empty($votes['s'][$idQuestion.'-'.$idAnswer])){
								$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
								return false;
							}
						}						
					}					
					break;
				case "ta":
					if($questionInfo['required']) {
						foreach($questionInfo['answers'] as $idAnswer){
							if(empty(trim($votes['ta'][$idQuestion.'-'.$idAnswer]))){
								$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
								return false;
							}
						}						
					}					
					break;
				case "sta":	
					if($questionInfo['required']) {
						foreach($questionInfo['answers'] as $idAnswer){
							if(empty($votes['sta'][$idQuestion.'-'.$idAnswer]) || empty(trim($votes['sta-text'][$idQuestion.'-'.$idAnswer]))){
								$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
								return false;
							}
						}						
					}				
					break;					
				case "ro":
					if($questionInfo['required'] && 
							(
								empty($votes['ro'][$idQuestion]) || 
								(!empty($votes['ro'][$idQuestion]) && $votes['ro'][$idQuestion] != 'custom' && !in_array($votes['ro'][$idQuestion],$questionInfo['answers'])) ||
								($votes['ro'][$idQuestion] == 'custom' && empty(trim($votes['ro']['custom-'.$idQuestion])))
							)
						){						
						$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
						return false;
					}			
					break;
				case "yn":
					if($questionInfo['required']) {
						foreach($questionInfo['answers'] as $idAnswer){
							if(empty($votes['yn'][$idQuestion.'-'.$idAnswer])){
								$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
								return false;
							}
						}						
					}					
					break;
				case "cbo":
					if($questionInfo['required'] && 
							(
								empty($votes['cbo'][$idQuestion]) || 
								(!empty($votes['cbo'][$idQuestion]) && !in_array('custom', $votes['cbo'][$idQuestion]) && count(array_intersect($questionInfo['answers'], $votes['cbo'][$idQuestion])) == 0) ||
								(in_array('custom', $votes['cbo'][$idQuestion]) && empty(trim($votes['cbo']['custom-'.$idQuestion])))
							)
						){			
						$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
						return false;
					}			
					break;
				case "priority":
					if($questionInfo['required']) {
						foreach($questionInfo['answers'] as $idAnswer){
							if(!in_array($idAnswer, $votes['priority'][$idQuestion])){
								$this->setError(JText::_('COM_MULTIPOLLS_VOTES_ERROR'));		
								return false;
							}
						}						
					}						
					break;					
				default:				
					break;													
			}
		}

		return true;
	}

	public function saveVote($data)
	{
		$id_user = JFactory::getUser()->id;

		$db = $this->getDbo();
		$query = $db->getQuery(true);		

		$db->transactionStart();

		if(!empty($data->votes['r']))
		{
			try
			{					
				$columns = array('id_question','id_answer', 'id_user', 'ip', 'user_agent', 'date_voting');		
				$query->insert($db->quoteName('#__multipolls_radio_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['r'] as $key => $vote) 
				{			
				    $rows = array($db->quote($key), $db->quote($vote), $db->quote($id_user), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
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
			
			//каждое имя поля ввода ответа состоит из [id_вопроса-id_ответа]
			//т.к. id_вопроса у всех ответов одинаковый
			//то берем первый ответ и вытягиваем id_вопроса
			$id_question = explode("-",array_key_first($data->votes['s']))[0];	

			//проверяем максимально допустимое значение для ответа
			$max_available_val = $this->_getSelectRangeValue($id_question);

			try
			{					
				$columns = array('id_question', 'id_answer', 'value', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_select_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['s'] as $key => $vote) 
				{		
					if (!is_numeric($vote) || $vote < 1 || $vote > $max_available_val) 
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
			//каждое имя поля ввода ответа состоит из [id_вопроса-id_ответа]
			//т.к. id_вопроса у всех ответов одинаковый
			//то берем первый ответ и вытягиваем id_вопроса
			$id_question = explode("-",array_key_first($data->votes['sta']))[0];	

			//проверяем максимально допустимое значение для ответа
			$max_available_val = $this->_getSelectRangeValue($id_question);

			try
			{					
				$columns = array('id_question', 'id_answer', 'value', 'text', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_select_text_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['sta'] as $key => $vote) 
				{					
					if (!is_numeric($vote) || $vote < 1 || $vote > $max_available_val) 
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
				$columns = array('id_question','id_answer', 'id_user', 'own_answer' ,'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_radio_own_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['ro'] as $key => $vote) 
				{
					if(strpos($key, 'custom-') !== false) {	
						continue;						
					}	
					
					//показывает что есть данные для вставки
					$exist_data_ro = false;
					
					if($vote == 'custom') {
						$rows = array($db->quote($key), $db->quote(''), $db->quote($id_user), $db->quote($data->votes['ro']['custom-'.$key]), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));						
						$exist_data_ro = true;
					} else {
						$rows = array($db->quote($key), $db->quote($vote), $db->quote($id_user), $db->quote(''), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
						$exist_data_ro = true;
					}					
				 
				    $query->values(implode(',', $rows));		       
				}
				
				if($exist_data_ro){					
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
		
		if(!empty($data->votes['yn']))
		{
			try
			{					
				$columns = array('id_question', 'id_answer', 'value', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_yn_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['yn'] as $key => $vote) 
				{						
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

		if(!empty($data->votes['cbo']))
		{
			try
			{					
				$columns = array('id_question', 'answers', 'own_answer', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_cb_own_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['cbo'] as $key => $vote) 
				{	
					if(strpos($key, 'custom-') !== false) {	
						continue;						
					}	

					//показывает что есть данные для вставки
					$exist_data_cbo = false;
					
					if(in_array('custom', $vote)){							
						$rows = array($db->quote($key), $db->quote(''), $db->quote($data->votes['cbo']['custom-'.$key]), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));

						$key_custom = array_search('custom', $vote);
						unset($vote[$key_custom]);

						$query->values(implode(',', $rows));

						$exist_data_cbo = true;
					}

					if(!empty($vote)) {
						$rows = array($db->quote($key), $db->quote(implode(",", $vote)), $db->quote(''), $db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
				    	$query->values(implode(',', $rows));

				    	$exist_data_cbo = true;
					}														       
				}
			   	
			   	if($exist_data_cbo){
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
		
		if(!empty($data->votes['priority']))
		{
			try
			{					
				$columns = array('id_question', 'id_answer', 'value', 'ip', 'user_agent', 'date_voting');				
				$query->insert($db->quoteName('#__multipolls_priority_votes'));
				$query->columns($db->quoteName($columns));		

				foreach ($data->votes['priority'] as $id_question => $vote) {
					$i = 1; //приоритет по порядку
					foreach($vote as $id_answer){
				    	$rows = array($db->quote($id_question), $db->quote($id_answer), $db->quote($i) ,$db->quote($data->ip), $db->quote($data->user_agent), $db->quote($data->date_vote));
						$query->values(implode(',', $rows));	
						$i++;
					}	       
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
		$result = "<div class='answers'><h4>".$question['q_name'];

		if($question['required']){
			$result .= "<span style='color:#ff0000;font-size:9px;vertical-align:top;'> *</span>";
		}
		
		$result .= "</h4>";		

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
				$result .= $this->_generateCheckbox($id_question, $question, $context);
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

			case '8':
				$result .= $this->_generateCheckboxOwn($id_question, $question, $context);
				break;	
			
			case '9':
				$result .= $this->_generatePriority($id_question, $question);
				break;

			default:				
				break;
		}

		$result .= "</div>";	

		return $result;
	}

	private function _generateRadio($id_question, $question)
	{	
		$answers = '';

		if(isset($question['answers']))
		{			
			$answers .= "<div class='r-answers'>";

			foreach ($question['answers'] as $id => $answer) 
			{
				$answers .= "<label class='radio'>";
				
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}							
													
				$answers .= "<input type='radio' name='r[".$id_question."]' value=".$id." ";
				
				if($question['required']) {
					$answers .= "required";
				}

				$answers .= ">".$answer."</label>";					
			}

			$answers .= "</div>";		
		}

		return $answers;
	}

	private function _generateCheckbox($id_question, $question, $context)
	{	
		$answers = '';

		if(isset($question['answers']))
		{
			$answers .= "<div class='cb-answers";

			if($question['required']) {
				$answers .= " required";
			}

			$answers .= "'>";

			//сообщение об неудачной валидации			

			if ($context == 'component')
			{	
				$answers .= "<input class='valid-error' type='hidden' value='".JText::_('COM_MULTIPOLLS_VALID_ERROR_NO_ANSWERS')."'>";		
			}

			elseif ($context == 'module')
			{	
				$answers .= "<input class='valid-error' type='hidden' value='".JText::_('MOD_MULTIPOLLS_VALID_ERROR_NO_ANSWERS')."'>";		
			}
			//------------------------------

			foreach ($question['answers'] as $id => $answer) 
			{
				$answers .= "<label class='checkbox'>";
				
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}							
													
				$answers .= "<input type='checkbox' name='cb[".$id_question."][]' value=".$id.">".$answer."</label>";	
			}

			$answers .= "</div>";
		}

		return $answers;
	}

	private function _generateSelect($id_question, $question)
	{
		$answers = '';

		if(isset($question['answers']))
		{	
			$answers .= "<div class='s-answers'>";

			foreach ($question['answers'] as $id => $answer) 
			{
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}

				$answers .=	"<label for='select".$id."'>".$answer."</label>";				

				$answers .=	"<select style='width:auto' name='s[".$id_question."-".$id."]' id='select".$id."'>";

				//беру для данного вопроса максимальное значение ранжирования
				$range = $this->_getSelectRangeValue($id_question);

				for ($i = 1; $i <= $range; $i++) 
				{ 
					$answers .= "<option>".$i."</option>";
				}

				$answers .= "</select>";
			}

			$answers .= "</div>";
		}

		return $answers;
	}

	private function _generateTextArea($id_question, $question)
	{
		$answers = '';

		if(isset($question['answers']))
		{
			$answers .= "<div class='ta-answers'>";

			foreach ($question['answers'] as $id => $answer) 
			{	
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}

				$answers .= "<p>".$answer."</p>";															
			
				$answers .= "<textarea name='ta[".$id_question."-".$id."]'";
				
				if($question['required']) {
					$answers .= " required";
				}

				$answers .= "></textarea>";
			}

			$answers .= "</div>";
		}

		return $answers;
	}

	private function _generateSelectTextarea($id_question, $question)
	{
		$answers = '';

		if(isset($question['answers']))
		{
			$answers .= "<div class='sta-answers'>";

			foreach ($question['answers'] as $id => $answer) 
			{
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}

				$answers .=	"<label for='select".$id."'>".$answer."</label>";				

				$answers .=	"<select style='width:auto' name='sta[".$id_question."-".$id."]' id='select".$id."'>";

				//беру для данного вопроса максимальное значение ранжирования
				$range = $this->_getSelectRangeValue($id_question);

				for ($i = 1; $i <= $range; $i++) 
				{ 
					$answers .= "<option>".$i."</option>";
				}

				$answers .= "</select>";				

				$answers .= "<textarea name='sta-text[".$id_question."-".$id."]'";
				
				if($question['required']) {
					$answers .= " required";
				}

				$answers .= "></textarea>";
			}

			$answers .= "</div>";	
		}

		return $answers;
	}

	private function _generateRadioOwn($id_question, $question, $context)
	{
		$answers = '';

		if(isset($question['answers']))
		{
			$answers .= "<div class='ro-answers'>";

			foreach ($question['answers'] as $id => $answer) 
			{
				$answers .= "<label class='radio'>";
				
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}	
				
				$answers .= "<input type='radio' name='ro[".$id_question."]' value=".$id." ";
				
				if($question['required']) {
					$answers .= "required";
				}

				$answers .= ">".$answer."</label>";	
			}

			$answers .= "<label class='radio'><input type='radio' class='own-radio' name='ro[".$id_question."]' value='custom' ";

			if($question['required']) {
				$answers .= "required";
			}

			$answers .= ">";

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

			$answers .= "</div>";							
		}

		return $answers;
	}
		
	private function _generateYn($id_question, $question, $context)
	{
		$answers = '';

		if(isset($question['answers']))
		{
			$answers .= "<div class='yn-answers'>";

			foreach ($question['answers'] as $id => $answer) 
			{
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}

				$answers .=	"<label for='select".$id."'>".$answer."</label>";				

				$answers .= "<label style='width: 50px;display: inline-block;' ><input type='radio' name='yn[".$id_question."-".$id."]' value='y'";
				
				if($question['required']) {
					$answers .= " required";
				}
				
				$answers .= "> ";	

				if($context == 'component')
				{
					$answers .= JText::_('COM_MULTIPOLLS_YES');
				}

				elseif ($context == 'module')
				{
					$answers .= JText::_('MOD_MULTIPOLLS_YES');
				}

				$answers .=  "</label>"; 

				$answers .= "<label style='width: 50px;display: inline-block;' ><input type='radio' name='yn[".$id_question."-".$id."]' value='n'";

				if($question['required']) {
					$answers .= " required";
				}
				$answers .= "> ";

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

			$answers .= "</div>";			
		}

		return $answers;
	}

	private function _generateCheckboxOwn($id_question, $question, $context)
	{	
		$answers = '';

		if(isset($question['answers']))
		{			
			$answers .= "<div class='cbo-answers";

			if($question['required']) {
				$answers .= " required";
			}

			$answers .= "'>";

			//сообщение об неудачной валидации			

			if ($context == 'component')
			{	
				$answers .= "<input class='valid-error' type='hidden' value='".JText::_('COM_MULTIPOLLS_VALID_ERROR_NO_ANSWERS')."'>";		
			}

			elseif ($context == 'module')
			{	
				$answers .= "<input class='valid-error' type='hidden' value='".JText::_('MOD_MULTIPOLLS_VALID_ERROR_NO_ANSWERS')."'>";		
			}
			//------------------------------

			foreach ($question['answers'] as $id => $answer) 
			{
				$answers .= "<label class='checkbox'>";
				
				if ($question['images'][$id] != '')
				{
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}							
													
				$answers .= "<input type='checkbox' name='cbo[".$id_question."][]' value=".$id.">".$answer."</label>";					
			}

			$answers .= "<label class='checkbox'><input type='checkbox' class='own-checkbox' name='cbo[".$id_question."][]' value='custom'>";

			if (trim($question["name_own"]) != '')
			{
				$answers .= $question["name_own"];
			}

			$answers .="<input class='own-input' name='cbo[custom-".$id_question."]'";

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

			$answers .= "</div>";
		}

		return $answers;
	}

	private function _generatePriority($id_question, $question)
	{
		$answers = '';

		if(isset($question['answers'])) {
			$answers .= "<div class='pr-answers'>";
			$answers .= "<ul class='priority-list'>";

			foreach ($question['answers'] as $id => $answer) {				
				$answers .= "<li>";

				if ($question['images'][$id] != '') {
					$answers .= "<img src=".JUri::base(true)."/".$question['images'][$id].">";
				}						
													
				$answers .= $answer;
				$answers .= "<input type='hidden' name='priority[".$id_question."][]' value=".$id.">";	
				$answers .= "</li>";				
			}

			$answers .= "</ol></div>";
		}

		return $answers;
	}
}	
