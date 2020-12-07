<?php

defined( '_JEXEC' ) or die;

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "poll.cancel")
		{				
			Joomla.submitform(task, document.getElementById("item-form"));			
		}
	};
');

?>

<form method="post" name="adminForm" id="item-form">	
	<h2><?php echo $this->result['poll_name']; ?></h2>

	<?php if(!empty($this->result['question'])) :?>

		<?php foreach ($this->result['question'] as $question) : ?>
			<h3><?php echo $question['name'] ?></h3>	

				<?php $this->question = $question['votes']; ?>

				<?php switch ($question['type']) :
						case '1':
							echo $this->loadTemplate('radio').'<hr>';
							break;
						case '2':
							echo $this->loadTemplate('cb').'<hr>';
							break;
						case '3':
							echo $this->loadTemplate('select').'<hr>';
							break;
						case '4':						
							echo $this->loadTemplate('text').'<hr>';						
							break;
						case '5':
							echo $this->loadTemplate('select_text').'<hr>';
							break;
						case '6':
							echo $this->loadTemplate('radio_own').'<hr>';
							break;	
						case '7':
							echo $this->loadTemplate('yn').'<hr>';
							break;	
						case '8':
							echo $this->loadTemplate('cb_own').'<hr>';
							break;	
						default:						
							break;
					endswitch;
				?>		
		<?php endforeach; ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />			
	<?php echo JHtml::_('form.token'); ?>

</form>	