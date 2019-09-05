<?php

defined( '_JEXEC' ) or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
?>

<form action="<?php echo JRoute::_( 'index.php?option=com_multipolls&view=polls' ); ?>" method="post" name="adminForm" id="adminForm">

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

    		<table class="table table-striped" id="pollList">
    			<thead>
    				<tr>    					
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
						</th>
						<th style="min-width:100px" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'name', $listDirn, $listOrder); ?>
						</th>
						<th width="20%" class="nowrap center">
							<?php echo JText::sprintf('COM_MULTIPOLLS_STAT'); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_CREATED', 'created' , $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
						</th>
    				</tr>	
    			</thead>
    			<tbody>
	    			<?php foreach ( $this->items as $i => $item ) :                		                		              		
	            		$canChange = $this->user->authorise( 'core.edit.state', 'com_multipolls.polls' . $item->id );
	            		$canEdit = $this->user->authorise('core.edit', 'com_multipolls.poll.' . $item->id);
	            	?>
	            	<tr class="row<?php echo $i % 2; ?>">	            		           		
	            		<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
	            		<td class="center">
	                        <div class="btn-group">
	                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'polls.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
	                        </div>
	                    </td>	                    	
	            		<td class="has-context">
							<div class="pull-left break-word">
								<?php if ($canEdit) : ?>
									<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_multipolls&task=poll.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
										<?php echo $this->escape($item->name); ?></a>
								<?php else : ?>
									<span><?php echo $this->escape( $item->name ); ?></span>
								<?php endif; ?>
	            			</div>	            			
	            		</td>
	            		<td class="center">
	            			<a class="btn btn-micro" href="<?php echo JRoute::_('index.php?option=com_multipolls&task=poll.show&id=' . $item->id); ?>">
					            <span class="icon-list-view"></span>
					        </a>
	            		</td>	            			            		
	            		<td class="nowrap small hidden-phone">
							<?php
								$date = $item->created;
								echo $date > 0 ? JHtml::_('date', $date, JText::_('DATE_FORMAT_LC4')) : '-';
							?>
						</td>
	            		<td class="hidden-phone">
							<?php echo (int) $item->id; ?>
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