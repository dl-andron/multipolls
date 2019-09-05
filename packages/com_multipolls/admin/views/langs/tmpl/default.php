<?php

// No direct access
defined( '_JEXEC' ) or die;

$user = JFactory::getUser();

?>
<form action="<?php echo JRoute::_( 'index.php?option=com_multipolls&view=langs' ); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar )): ?>
	    <div id="j-sidebar-container" class="span2">
	        <?php echo $this->sidebar; ?>
	    </div>
	    <div id="j-main-container" class="span10">
    <?php else : ?>
   		<div id="j-main-container">
    <?php endif; ?>
    		
    		<table class="table table-striped" id="langList">
    			<thead>
	                <tr>                    
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center">   
                            <?php echo JText::sprintf('JSTATUS'); ?>
						</th>
						<th style="min-width:100px" class="nowrap">
                            <?php echo JText::sprintf('JGLOBAL_TITLE'); ?>							
						</th>						
						<th width="1%" class="nowrap hidden-phone">
                            <?php echo JText::sprintf('JGRID_HEADING_ID'); ?>							
						</th>	                    
	                </tr>
                </thead>
                <tbody>
                	<?php foreach ( $this->items as $i => $item ) :                		                		              		
                		$canChange = $user->authorise( 'core.edit.state', 'com_multipolls.langs' . $item->id_lang );
                	?>
                	<tr class="row<?php echo $i % 2; ?>">                		
                		<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id_lang); ?>
						</td>
                		<td class="center">
                            <div class="btn-group">
                                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'langs.', $canChange, 'cb'); ?>
                            </div>
                        </td>
                		<td>                           
                            <span><?php echo $this->escape( $item->name ); ?></span>                            
                		</td>
                		<td class="hidden-phone">
							<?php echo (int) $item->id_lang; ?>
						</td>
                	</tr>	
                	<?php endforeach; ?>	
                </tbody>	
    		</table>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_( 'form.token' ); ?>

    	</div>
</form>