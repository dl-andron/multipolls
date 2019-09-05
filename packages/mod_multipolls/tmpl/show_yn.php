<?php

defined( '_JEXEC' ) or die;

?>

<?php if(!empty($qstn)): ?>

	<table class="table table-bordered">
		<tbody>
			<tr>
				<th><?php echo JText::_('MOD_MULTIPOLLS_ANSWER')?></th>
				<th><?php echo JText::_('MOD_MULTIPOLLS_YES')?></th>
				<th><?php echo JText::_('MOD_MULTIPOLLS_NO')?></th>
			</tr>
			<?php foreach ($qstn as $answer) : ?>										
				<tr>
					<td width="40%"><?php echo $answer['name']; ?></td>					
					<td><?php echo $answer['y']; ?></td>
					<td><?php echo $answer['n']; ?></td>
				</tr>					
			<?php endforeach; ?>
		</tbody>
	</table>
	
<?php endif; ?>	