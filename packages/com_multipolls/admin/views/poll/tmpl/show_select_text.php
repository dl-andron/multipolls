<?php

defined( '_JEXEC' ) or die;	

?>

<?php if(!empty($this->question)) :?>

	<table class="table table-bordered">
		<tbody>
			<tr>
				<th><?php echo JText::_('COM_MULTIPOLLS_ANSWER')?></th>
				<th><?php echo JText::_('COM_MULTIPOLLS_AVERAGE')?></th>
			</tr>
			<?php foreach ($this->question as $answer) : ?>										
				<tr>
					<td width="40%"><?php echo $answer['name']; ?></td>					
					<td><?php echo $answer['count']; ?></td>
				</tr>					
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php foreach ($this->question as $answer) : ?>	
		<?php if(isset($answer['answers'])) :?>
			
			<h4><?php echo $answer['name'] ?></h4>

			<?php foreach ($answer['answers'] as $value) :?>		

				<p><?php echo $value; ?></p>

			<?php endforeach; ?>
		<?php endif; ?>		
	<?php endforeach; ?>
		
<?php endif; ?>