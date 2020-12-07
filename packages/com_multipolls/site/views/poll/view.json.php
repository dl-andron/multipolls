<?php

defined('_JEXEC') or die('Restricted access');
 
class MultipollsViewPoll extends JViewLegacy
{
	function display($tpl = null)
	{		
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR .'/models');		
		$model = JModelLegacy::getInstance('Stat', 'MultipollsModel');

		$input = JFactory::getApplication()->input;
		$id_poll = $input->get('id_poll', '', 'int');

		$records = $model->getStat($id_poll);

		$result = '';

		if(!empty($records['question']))
		{
			foreach ($records['question'] as $question)
			{	
				$result .= '<h4>'.$question['name'].'</h4>';

				switch ($question['type'])
				{
					case '1':
						$result .= $this->_generateRadioAnswers($question['votes']);
						break;
					case '2':
						$result .= $this->_generateCheckboxAnswers($question['votes']);
						break;
					case '3':
						$result .= $this->_generateSelectAnswers($question['votes']);
						break;					
					case '5':
						$result .= $this->_generateSelectTextareaAnswers($question['votes']);
						break;
					case '6':
						$result .= $this->_generateRadioOwnAnswers($question['votes']);
						break;	
					case '7':
						$result .= $this->_generateYnAnswers($question['votes']);
						break;
					case '8':
						$result .= $this->_generateCheckboxOwnAnswers($question['votes']);
						break;		
					default:						
						break;
				}		
					
			}
		}

		$result .= '<input type="button" name="back-to-poll" class="btn btn-info" value="'.JText::_('COM_MULTIPOLLS_BACK_TO_POLL').'">';

		echo new JResponseJson($result);

	}

	private function _generateRadioAnswers($votes)
	{
		$res = '';

		$sum = 0;

		foreach ($votes as $key => $answer)
		{
			$sum += $answer['count'];
		}
		

		if(!empty($votes))
		{
			$res .= '<table class="table table-bordered">';

			foreach ($votes as $key => $answer)
			{			
				$res .= '<tbody>';			
						
				$percents = $answer['count'] != 0 ? round($answer['count']/$sum*100,2) : 0;
						
				$res .= '<tr>';
				$res .=	'<td width="40%">'.$answer['name'].'</td>';								
				$res .=	'<td><div class="progress"><div class="bar" style="width:'.$percents.'%"><div style="color:black">'.$answer['count'].' - '.$percents.' %</div></div></div></td>';
				$res .=	'</tr>';						
				$res .= '</tbody>';
			}

			$res .= '</table>';
			
		}

		return $res;
	}

	private function _generateCheckboxAnswers($votes)
	{	
		$res = '';

		if(!empty($votes))
		{	

			$count_votes = count($votes['votes']);			

			foreach ($votes['names'] as $key => $name)
			{			

				$res .= '<table class="table table-bordered">';
				$res .=	'<tbody>';	
					
				$percents = $name['count'] != 0 ? round($name['count']/$count_votes*100,2) : 0;
						
				$res .=	'<tr>';
				$res .=	'<td width="40%">'.$name['name'].'</td>';					
				$res .=	'<td><div class="progress"><div class="bar" style="width:'.$percents.'%"><div style="color:black">'.$name['count'].' - '.$percents.'%</div></div></div></td>';
				$res .=	'</tr>';					
				$res .= '</tbody></table>';				
			}
		}

		return $res;
	}

	private function _generateSelectAnswers($votes)
	{
		$res = '';

		if(!empty($votes))
		{

			$res .= '<table class="table table-bordered">';
			$res .=	'<tbody>';
			$res .=	'<tr>';
			$res .=	'<th>'.JText::_('COM_MULTIPOLLS_ANSWER').'</th>';
			$res .=	'<th>'.JText::_('COM_MULTIPOLLS_ANSWER').'</th>';
			$res .=	'</tr>';

			foreach ($votes as $answer)
			{									
				$res .=	'<tr>';
				$res .=	'<td width="40%">'.$answer['name'].'</td>';					
				$res .=	'<td>'.$answer['count'].'</td>';
				$res .=	'</tr>';					
			}

			$res .=	'</tbody></table>';
		
		}

		return $res;
	}

	private function _generateSelectTextareaAnswers($votes)
	{
		$res = '';

		if(!empty($votes))
		{	

			$res .= '<table class="table table-bordered">';
			$res .=	'<tbody>';
			$res .=	'<tr>';
			$res .=	'<th>'.JText::_('COM_MULTIPOLLS_ANSWER').'</th>';
			$res .=	'<th>'.JText::_('COM_MULTIPOLLS_ANSWER').'</th>';
			$res .=	'</tr>';

			foreach ($votes as $answer)
			{
				$res .=	'<tr>';
				$res .=	'<td width="40%">'.$answer['name'].'</td>';					
				$res .=	'<td>'.$answer['count'].'</td>';
				$res .=	'</tr>';						
			}

			$res .=	'</tbody></table>';
				
		}

		return $res;
	}

	private function _generateRadioOwnAnswers($votes)
	{
		$res = '';

		$sum = 0;

		foreach ($votes['votes'] as $key => $answer)
		{
			$sum += $answer['count'];
		}		

		if(!empty($votes))
		{
			$res .= '<table class="table table-bordered">';
			foreach ($votes['votes'] as $key => $answer)
			{			
				$res .= '<tbody>';			
					
				$percents = $answer['count'] != 0 ? round($answer['count']/$sum*100,2) : 0;
				
				$res .= '<tr>';
				$res .=	'<td width="40%">'.$answer['name'].'</td>';								
				$res .=	'<td><div class="progress"><div class="bar" style="width:'.$percents.'%"><div style="color:black">'.$answer['count'].' - '.$percents.' %</div></div></div></td>';
				$res .=	'</tr>';						
				$res .= '</tbody>';
			}
			
			$res .= '</table>';

		}

		return $res;
	}

	private function _generateYnAnswers($votes)
	{
		$res = '';

		if(!empty($votes))
		{	

			$res .= '<table class="table table-bordered">';
			$res .=	'<tbody>';
			$res .=	'<tr>';
			$res .=	'<th>'.JText::_('COM_MULTIPOLLS_ANSWER').'</th>';
			$res .=	'<th>'.JText::_('COM_MULTIPOLLS_YES').'</th>';
			$res .=	'<th>'.JText::_('COM_MULTIPOLLS_NO').'</th>';
			$res .=	'</tr>';

			foreach ($votes as $answer)
			{
				$res .=	'<tr>';
				$res .=	'<td width="40%">'.$answer['name'].'</td>';					
				$res .=	'<td>'.$answer['y'].'</td>';
				$res .=	'<td>'.$answer['n'].'</td>';
				$res .=	'</tr>';						
			}				

			$res .=	'</tbody></table>';		
		}

		return $res;
	}

	private function _generateCheckboxOwnAnswers($votes)
	{
		$res = '';

		if(!empty($votes)) {

			$count_votes = count($votes['votes']);

			foreach ($votes['names'] as $key => $name){			

				$res .= '<table class="table table-bordered">';
				$res .=	'<tbody>';	
						
				$percents = $name['count'] != 0 ? round($name['count']/$count_votes*100,2) : 0;
						
				$res .= '<tr>';
				$res .=	'<td width="40%">'.$name['name'].'</td>';
				$res .=	'<td><div class="progress"><div class="bar" style="width:'.$percents.'%"><div style="color:black">'.$name['count'].' - '.$percents.' %</div></div></div></td>';	
				$res .= '</tr>';						
				$res .=	'</tbody></table>';				
			}
		}

		return $res;
	}
}