<?php

defined( '_JEXEC' ) or die;

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_multipolls/css/multipolls.css');	

?>

<div class="item-page multipoll">
	<h3><?php echo $this->result['poll_name']; ?></h3>	

	<?php if($this->show_result && !empty($this->result['question'])) :?>

		<?php foreach ($this->result['question'] as $question) : ?>
				<h3><?php echo $question['name'] ?></h3>	

					<?php $this->question = $question['votes']; ?>

					<?php switch ($question['type']) :
						case '1':
							echo $this->loadTemplate('radio');
							break;
						case '2':
							echo $this->loadTemplate('cb');
							break;
						case '3':
							echo $this->loadTemplate('select');
							break;
						/*case '4':						
							echo $this->loadTemplate('text');						
							break;*/
						case '5':
							echo $this->loadTemplate('select_text');
							break;
						case '6':
							echo $this->loadTemplate('radio_own');
							break;	
						case '7':
							echo $this->loadTemplate('yn');
							break;	
						case '8':
							echo $this->loadTemplate('cb_own');
							break;	
						case '9':
							echo $this->loadTemplate('priority');
							break;	
						default:						
							break;
					endswitch;
				?>		
					
			<?php endforeach; ?>
	<?php else :?>		
		<?php
			$app = JFactory::getApplication();
			$system_message=$app->getMessageQueue();
			
			$message = !empty($system_message[0]["message"]) ? JText::_('COM_MULTIPOLLS_THANKS') : JText::_('COM_MULTIPOLLS_ALREADY_VOTED');
		?>
		<?php echo $message; ?>
	<?php endif; ?>	

</div>	