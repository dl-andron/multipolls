<?php

defined( '_JEXEC' ) or die;

class MultipollsModelStat extends JModelLegacy 
{
    protected $lang_tag;

    public function __construct( $config = array() )
    {    
        $this->lang_tag = JFactory::getLanguage()->getTag();        
        parent::__construct( $config );
    }

    public function getStat($id_poll)
    {   
                 
        $db = $this->getDbo();

        //получаю название опроса
        $query = $db->getQuery(true);
        $query->select($db->quoteName('name_'.$this->lang_tag, 'name'));
        $query->from($db->quoteName('#__multipolls_polls'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($id_poll));        
        $db->setQuery($query);        
        $result['poll_name'] = $db->loadResult();

        $query = $db->getQuery(true);
        $query->select($db->quoteName('name_'.$this->lang_tag, 'name'));
        $query->select($db->quoteName('id_type'));
        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__multipolls_questions'));
        $query->where($db->quoteName('id_poll') . ' = ' . $db->quote($id_poll)); 
        $query->where($db->quoteName('published') . ' = ' . $db->quote('1')); 
        $query->order('ordering, id');        
        $db->setQuery($query);   
        $questions  = $db->loadAssocList();

        foreach ($questions as $question) 
        {
            $result['question'][$question['id']]['name'] = $question['name'];
            $result['question'][$question['id']]['type'] = $question['id_type'];

            switch ($question['id_type']) 
            {
                case '1':
                    $return = $this->_getRadioVotes($question['id']);
                    break;

                case '2':
                    $return = $this->_getCbVotes($question['id']);
                    break;

                case '3':
                    $return = $this->_getSelectVotes($question['id']);
                    break;

                case '4':
                    $return = $this->_getTextVotes($question['id']);
                    break;

                case '5':
                    $return = $this->_getSelectTextVotes($question['id']);
                    break;

                case '6':
                    $return = $this->_getRadioOwnVotes($question['id']);
                    break;  
					
				case '7':
                    $return = $this->_getYnVotes($question['id']);
                    break;

                default:                
                    break;
            }

            $result['question'][$question['id']]['votes'] = $return;
        }     

        return $result;
    }

    private function _getRadioVotes($id_question)
    {
        $db = $this->getDbo();
        
        $ids = $this->_getAnswers($id_question); 

        $query = $db->getQuery(true);
        $query->select($db->quoteName('id_answer'));
        $query->from($db->quoteName('#__multipolls_radio_votes'));     
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));                
        $db->setQuery($query);
        $votes = $db->loadColumn(); 

        
        $counts = array_count_values($votes);

        foreach ($ids as $key => $value) 
        {
            if(isset($counts[$value['id']]))
            {
                $ids[$key]['count'] = $counts[$value['id']];
            }
            else
            {
                $ids[$key]['count'] = 0;
            }
            
        }  

        return $ids;      
    }

    private function _getCbVotes($id_question)
    {
        $db = $this->getDbo();
       
        $ids = $this->_getAnswers($id_question);

        $query = $db->getQuery(true);
        $query->select($db->quoteName('answers'));
        $query->from($db->quoteName('#__multipolls_cb_votes'));     
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));                
        $db->setQuery($query);
        $votes = $db->loadColumn();      
        
        if(!empty($votes))
        {
            foreach ($votes as $key => $vote) 
            {
                $votes[$key] = explode(',' , $vote);
            }

            foreach ($ids as $key => $value) 
            {
                $sum = 0;

                foreach ($votes as $vote) 
                {
                    if(in_array($value['id'], $vote))
                    $sum ++;
                }

                $ids[$key]['count'] = $sum;
                
            }
        } 

        else
        {
            foreach ($ids as $key => $value) 
            {                
                $ids[$key]['count'] = 0;                
            }
        }
        
        $result['votes'] = $votes;
        $result['names'] = $ids;        

        return $result;

    }

    private function _getSelectVotes($id_question)
    {
        $db = $this->getDbo();        
        
        $ids = $this->_getAnswers($id_question);

        $query = $db->getQuery(true);
        $query->select($db->quoteName('id_answer'));
        $query->select('AVG(value) as avg'); 
        $query->from($db->quoteName('#__multipolls_select_votes'));        
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));        
        $query->group($db->quoteName('id_answer'));
        $db->setQuery($query);   
        $votes  = $db->loadAssocList('id_answer'); 

        foreach ($ids as $key => $id)
        {   
            if(isset($votes[$id['id']]['avg']))
            {
                $ids[$key]['count'] = $votes[$id['id']]['avg'];
            }
            else
            {
                $ids[$key]['count'] = 0;
            }
        }

        return $ids;
    }

    private function _getTextVotes($id_question)
    {
        $db = $this->getDbo();

        $ids = $this->_getAnswers($id_question);
        
        $query = $db->getQuery(true);             
        $query->select($db->quoteName('text'));
        $query->select($db->quoteName('id_answer'));       
        $query->from($db->quoteName('#__multipolls_text_votes', 'v'));        
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));               
        $db->setQuery($query);   
        $votes  = $db->loadAssocList();

        foreach ($ids as $key => $id) 
        {
            foreach ($votes as $vote) 
            {  
                if($id['id'] == $vote['id_answer'])
                {
                    $ids[$key]['answers'][] = $vote['text'];
                }               
            }
        }      

        return $ids;
    }

    private function _getSelectTextVotes($id_question)
    {
        $db = $this->getDbo();

        $ids = $this->_getAnswers($id_question);

        $query = $db->getQuery(true);
        $query->select($db->quoteName('id_answer'));
        $query->select('AVG(value) as avg'); 
        $query->from($db->quoteName('#__multipolls_select_text_votes'));        
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));        
        $query->group($db->quoteName('id_answer'));
        $db->setQuery($query);   
        $votes  = $db->loadAssocList('id_answer'); 

        foreach ($ids as $key => $id)
        {   
            if(isset($votes[$id['id']]['avg']))
            {
                $ids[$key]['count'] = $votes[$id['id']]['avg'];
            }
            else
            {
                $ids[$key]['count'] = 0;
            }
        }

        $query = $db->getQuery(true);             
        $query->select($db->quoteName('text'));
        $query->select($db->quoteName('id_answer'));       
        $query->from($db->quoteName('#__multipolls_select_text_votes', 'v'));        
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));               
        $db->setQuery($query);   
        $votes  = $db->loadAssocList();

        foreach ($ids as $key => $id) 
        {
            foreach ($votes as $vote) 
            {  
                if($id['id'] == $vote['id_answer'] && trim($vote['text']) != '')
                {
                    $ids[$key]['answers'][] = $vote['text'];
                }               
            }
        }   

        return $ids;        
    }

    private function _getRadioOwnVotes($id_question)
    {
        $db = $this->getDbo();

        $ids = $this->_getAnswers($id_question);

        $query = $db->getQuery(true);
        $query->select($db->quoteName('id_answer'));
        $query->from($db->quoteName('#__multipolls_radio_own_votes'));     
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));                
        $db->setQuery($query);
        $votes = $db->loadColumn(); 

        
        $counts = array_count_values($votes);

        foreach ($ids as $key => $value) 
        {
            if(isset($counts[$value['id']]))
            {
                $ids[$key]['count'] = $counts[$value['id']];
            }
            else
            {
                $ids[$key]['count'] = 0;
            }
            
        }  

        $result['votes'] = $ids;
       

        $query = $db->getQuery(true);               
        $query->select($db->quoteName('own_answer'));      
        $query->from($db->quoteName('#__multipolls_radio_own_votes'));       
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question)); 
        $query->where($db->quoteName('own_answer') . ' <> ' . $db->quote(''));       
        $query->order('id_vote');
        $db->setQuery($query);
        $textvotes = $db->loadColumn();

        $result['textvotes'] = $textvotes;

        return $result; 
    }

    private function _getYnVotes($id_question)
    {
        $db = $this->getDbo();        
        
        $ids = $this->_getAnswers($id_question);

        $query = $db->getQuery(true);
		$query->select("concat(id_answer,'_', value) as id_uniq");
        $query->select($db->quoteName('id_answer'));
		$query->select($db->quoteName('value'));
        $query->select('COUNT(*) as cnt'); 
        $query->from($db->quoteName('#__multipolls_yn_votes'));        
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question));        
        $query->group('id_answer, value');
        $db->setQuery($query);   
        // $votes  = $db->loadAssocList('id_answer');
		$votes = $db->loadAssocList('id_uniq');
		
		$arVar = array('y','n');

        foreach ($ids as $key => $id)
        {   
			foreach ($arVar as $var)
			{
				$id_uniq = $id['id'].'_'.$var;
				if(isset($votes[ $id_uniq ]['cnt']))
					$ids[ $key ][ $var ] = $votes[ $id_uniq ]['cnt'];
				else
					$ids[$key][ $var ] = 0;	
			}
        }
		
        return $ids;
    }

    private function _getAnswers($id_question)
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);               
        $query->select($db->quoteName('name_'.$this->lang_tag, 'name'));
        $query->select($db->quoteName('id'));
        $query->from($db->quoteName('#__multipolls_answers'));     
        $query->where($db->quoteName('id_question') . ' = ' . $db->quote($id_question)); 
        $query->order('ordering, id');        
        $db->setQuery($query);
        $ids = $db->loadAssocList();

        return $ids;
    }
}