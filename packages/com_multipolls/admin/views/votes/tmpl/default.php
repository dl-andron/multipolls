<?php

defined( '_JEXEC' ) or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
?>
доработать
<form action="<?php echo JRoute::_( 'index.php?option=com_multipolls&view=votes' ); ?>" method="post" name="adminForm" id="adminForm">

	<?php if (!empty( $this->sidebar )): ?>
	    <div id="j-sidebar-container" class="span2">
	        <?php echo $this->sidebar; ?>
	    </div>
	    <div id="j-main-container" class="span10">
    <?php else : ?>
   		<div id="j-main-container">
    <?php endif; ?>
    	<?php
		// Search tools bar
			echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));			
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>

			<table class="table table-striped" id="voteList">
				<thead>
    				<tr>												
						<th style="min-width:300px" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_MULTIPOLLS_ANSWER', 'answer_name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_MULTIPOLLS_IP', 'ip' , $listDirn, $listOrder); ?>
						</th>
						<th width="20%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_MULTIPOLLS_USER_AGENT', 'user_agent' , $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_MULTIPOLLS_DATE_VOTING', 'date_voting' , $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id_vote', $listDirn, $listOrder); ?>
						</th>
    				</tr>	
    			</thead>
    			<tbody>
	    			<?php foreach ( $this->items as $i => $item ) :  
	            		$canEdit_answer = $this->user->authorise('core.edit', 'com_multipolls.answer.' . $item->id_answer);
	            		$canEdit_question = $this->user->authorise('core.edit', 'com_multipolls.question.' . $item->id_question);
	            	?>
	            	<tr class="row<?php echo $i % 2; ?>">           		
	            		<td class="has-context">
							<div class="pull-left break-word">
								<?php if ($canEdit_answer) : ?>							
									<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_multipolls&task=answer.edit&id=' . $item->id_answer); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
										<?php echo $this->escape($item->answer_name); ?></a>
								<?php else : ?>
									<span><?php echo $this->escape( $item->answer_name ); ?></span>
								<?php endif; ?>	
									<div class="small">
										<?php echo JText::_('COM_MULTIPOLLS_QUESTION') . ': '; ?>

										<?php if ($canEdit_question) : ?>							
											
											<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_multipolls&task=question.edit&id=' . $item->id_question); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
											<?php echo $this->escape($item->question_name); ?></a>
											
										<?php else : ?>										
											<span><?php echo $this->escape( $item->question_name ); ?></span>
										<?php endif; ?>			
									</div>
	            			</div>	            			
	            		</td>            		 
	            		<td class="nowrap small hidden-phone">
							<span><?php echo $this->escape( $item->ip ); ?></span>
						</td>
						<td class="nowrap small hidden-phone">
							<span><?php echo $this->escape( $item->user_agent ); ?></span>
						</td>             		
	            		<td class="nowrap small hidden-phone">
							<?php
								$date = $item->date_voting;
								echo $date > 0 ? JHtml::_('date', $date, JText::_('DATE_FORMAT_LC4')) : '-';
							?>
						</td>
	            		<td class="hidden-phone">
							<?php echo (int) $item->id_vote; ?>
						</td>
	            	</tr>	
	            	<?php endforeach; ?>
    			</tbody>
			</table>

		<?php endif; ?>		
			<?php echo $this->pagination->getListFooter(); ?>

			<input type="hidden" name="task" value="" />
		    <input type="hidden" name="boxchecked" value="0" />
		    <?php echo JHtml::_( 'form.token' ); ?>

		</div>  
</form>	