<?php

defined( '_JEXEC' ) or die;

?>

<?php if(!empty($this->question)) : ?>

	<?php $count_votes = count($this->question['votes']) ?>

	<?php foreach ($this->question['names'] as $key => $name) : ?>			

		<table class="table table-bordered">
			<tbody>	
				<?php
					$percents = $name['count'] != 0 ? round($name['count']/$count_votes*100,2) : 0;
				?>
				<tr>
					<td width="40%"><?php echo $name['name']; ?></td>					
					<td><div class="progress"><div class="bar" style="width:<?php echo $percents ?>%"><div style="color:black"><?php echo $name['count'] ?> - <?php echo $percents ?>%</div></div></div></td>
				</tr>						
			</tbody>
		</table>	
		
	<?php endforeach; ?>

<?php endif; ?> 