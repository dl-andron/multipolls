<?php

defined( '_JEXEC' ) or die;	

?>

<?php if(!empty($this->question)) :?>

	<?php foreach ($this->question as $key => $answer) :?>

		<h4><?php echo $answer['name'] ?></h4>

		<?php if(isset($answer['answers'])) :?>
			<?php foreach ($answer['answers'] as $value) :?>
			
				<p><?php echo $value; ?></p>

			<?php endforeach; ?>
		<?php endif; ?>	

	<?php endforeach; ?>

<?php endif; ?>