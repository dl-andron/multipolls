<?php

defined( '_JEXEC' ) or die;

?>

<?php if(!empty($this->question)) :?>

	<table class="table table-bordered">
		<tbody>
			<tr>
				<th><?php echo JText::_('COM_MULTIPOLLS_ANSWER')?></th>
				<th><?php echo JText::_('COM_MULTIPOLLS_YES')?></th>
				<th><?php echo JText::_('COM_MULTIPOLLS_NO')?></th>
			</tr>
			<?php foreach ($this->question as $answer) : ?>										
				<tr>
					<td width="40%"><?php echo $answer['name']; ?></td>					
					<td><?php echo $answer['y']; ?></td>
					<td><?php echo $answer['n']; ?></td>
				</tr>					
			<?php endforeach; ?>
		</tbody>
	</table>
	
<?php endif; ?>	