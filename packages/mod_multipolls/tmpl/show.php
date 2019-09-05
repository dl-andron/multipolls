<?php

defined( '_JEXEC' ) or die;
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_multipolls/css/multipolls.css');

?>

<div class="item-page multipoll">

	<?php if ($show_title) :?>
		<h2 itemprop="headline"<?php if ($show_content): ?>class="active-title"<?php endif;?>><?php echo $stat['poll_name']; ?></h2>
	<?php endif; ?>	
		
		<?php if(!empty($stat['question'])) :?>

			<?php foreach ($stat['question'] as $question) : ?>
				<h3><?php echo $question['name'] ?></h3>	

					<?php $qstn = $question['votes']; ?>

					<?php switch ($question['type']) :
							case '1':							
								require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'show_radio'));
								break;
							case '2':
								require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'show_cb'));
								break;
							case '3':
								require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'show_select'));
								break;
							/*case '4':						
								require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'show_text'));					
								break;*/
							case '5':
								require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'show_select_text'));
								break;
							case '6':							
								require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'show_radio_own'));
								break;	
							case '7':							
								require JModuleHelper::getLayoutPath('mod_multipolls', $params->get('layout', 'show_yn'));
								break;	
							default:						
								break;
							endswitch;
				?>	
					
			<?php endforeach; ?>
		<?php endif; ?>	
</div>	