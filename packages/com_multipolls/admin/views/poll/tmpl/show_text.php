<?php

defined( '_JEXEC' ) or die;	

?>

<?php if(!empty($this->question)) :?>

	<?php foreach ($this->question as $key => $answer) :?>

		<h4><?php echo $answer['name'] ?></h4>

		<?php if(isset($answer['answers'])) :?>
			<?php foreach ($answer['answers'] as $key => $value) :?>
				<div>
					<?php echo $key; ?> (<?php echo $value; ?>)<br>	
				</div>
			<?php endforeach; ?>
		<?php endif; ?>	

	<?php endforeach; ?>

<?php endif; ?>