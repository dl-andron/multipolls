<?php

defined( '_JEXEC' ) or die;

$sum = 0;

foreach ($this->question['votes'] as $key => $answer)
{
	$sum += $answer['count'];
}

?>

<?php if(!empty($this->question)): ?>

	<table class="table table-bordered">
		<?php foreach ($this->question['votes'] as $key => $answer) : ?>			
				<tbody>			
					<?php 
						$percents = $answer['count'] != 0 ? round($answer['count']/$sum*100,2) : 0;
					?>
					<tr>
						<td width="40%"><?php echo $answer['name']; ?></td>					
						<td><div class="progress"><div class="bar" style="width:<?php echo $percents ?>%"><div style="color:black"><?php echo $answer['count'] ?> - <?php echo $percents ?>%</div></div></div></td>
					</tr>						
				</tbody>
		<?php endforeach; ?>
	</table>

<?php endif; ?>