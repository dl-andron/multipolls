<?php

defined( '_JEXEC' ) or die;	

?>

<?php if(!empty($this->question)) :?>

	<table class="table table-bordered">
		<tbody>
			<tr>
				<th><?php echo JText::_('COM_MULTIPOLLS_ANSWER')?></th>
				<th><?php echo JText::_('COM_MULTIPOLLS_COUNT_VOTES_BY_VALUE')?></th>
			</tr>
			<?php foreach ($this->question as $answer) : ?>	
				<?php $answerSum = array_sum($answer['counts']); ?>									
				<tr>
					<td width="40%"><?php echo $answer['name']; ?></td>					
					<td>
						<?php foreach ($answer['counts'] as $selectedValue => $sumVotes) : ?>
							<?php $percent = $sumVotes != 0 ? round($sumVotes/$answerSum*100,2) : 0; ?>
							<div>
								<?php echo $selectedValue.' - '.$sumVotes." (".$percent."%)"; ?>
							</div>	
						<?php endforeach; ?>
					</td>
				</tr>					
			<?php endforeach; ?>
		</tbody>
	</table>

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