<?php 

defined('_JEXEC') or die;

class modMultipollsHelper
{
	public static function getAjax()
	{
		$input = JFactory::getApplication()->input;
		$id_poll  = $input->get('data');

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR .'/components/com_multipolls/models');	
		$model = JModelLegacy::getInstance('Stat', 'MultipollsModel');
		$stat = $model->getStat($id_poll);		

		if(!empty($stat['question']))
		{

			foreach ($stat['question'] as $question)
			{	
				echo '<h3>'.$question['name'].'</h3>';	

				$qstn = $question['votes'];

				switch ($question['type'])
				{
						case '1':							
							require JModuleHelper::getLayoutPath('mod_multipolls', 'show_radio');
							break;
						case '2':
							require JModuleHelper::getLayoutPath('mod_multipolls', 'show_cb');
							break;
						case '3':
							require JModuleHelper::getLayoutPath('mod_multipolls', 'show_select');
							break;
						/*case '4':						
							require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'show_text'));					
							break;*/
						case '5':
							require JModuleHelper::getLayoutPath('mod_multipolls', 'show_select_text');
							break;
						case '6':							
							require JModuleHelper::getLayoutPath('mod_multipolls', 'show_radio_own');
							break;
						case '7':							
							require JModuleHelper::getLayoutPath('mod_multipolls', 'show_yn');
							break;	
						case '8':							
							require JModuleHelper::getLayoutPath('mod_multipolls', 'show_cb_own');
							break;
						case '9':							
							require JModuleHelper::getLayoutPath('mod_multipolls', 'show_priority');
							break;			
						default:						
							break;
				}
					
			}
		}

		echo '<input type="button" name="back-to-poll" class="btn btn-info" value="'.JText::_('MOD_MULTIPOLLS_BACK_TO_POLL').'">';		
	}
}
