<?php

defined( '_JEXEC' ) or die;

JHtml::_( 'behavior.formvalidator' );
JHtml::_( 'behavior.keepalive' );
JHtml::_( 'formbehavior.chosen', 'select' );

$input = JFactory::getApplication()->input;
$cur_lang = JFactory::getLanguage()->getTag();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "answer.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{				
			Joomla.submitform(task, document.getElementById("item-form"));			
		}
	};
');
?>

<form action="<?php echo JRoute::_( 'index.php?option=com_multipolls&id=' . (int) $this->item->id ); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">	

	<?php
	 	$this->form->setFieldAttribute('id', 'type', 'hidden');
        echo $this->form->getInput( 'id' );
    ?>	

	<div class="form-horizontal">

		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>				
		
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MULTIPOLLS_ANSWER')); ?>

			<div class="row-fluid">

	            <div class="span9">
					<fieldset class="adminform">

						<?php foreach ($this->getLangs() as $lang) : ?>	

							<div class="control-group">
								<div class="control-label">
									<label id="jform_name_<?php echo $lang?>-lbl" for="jform_name_<?php echo $lang?>"><?php echo JText::_('JGLOBAL_TITLE').' ('.$lang.')'; ?></label>
								</div>
			                    <div class="controls">	                    	
			                    	<input type="text" name="jform[name_<?php echo $lang; ?>]" id="jform_name_<?php echo $lang?>" value="<?php echo $this->item->{'name_'.$lang} ?>" class="input-xxlarge input-large-text" size="40">
			                    </div>
			                </div>	

			            <?php endforeach; ?> 

			            <div class="control-group">
	                        <div class="control-label"><?php echo $this->form->getLabel( 'img_url' ); ?></div>
	                        <div class="controls"><?php echo $this->form->getInput( 'img_url' ); ?></div>
	                    </div>  

	                    <div class="control-group">
	                        <div class="control-label"><?php echo $this->form->getLabel( 'id_question' ); ?></div>
	                        <div class="controls"><?php echo $this->form->getInput( 'id_question' ); ?></div>
	                    </div>  

	                    <div class="control-group">
                       		<div class="control-label"><?php echo $this->form->getLabel( 'ordering' ); ?></div>
                       		<div class="controls"><?php echo $this->form->getInput( 'ordering' ); ?></div>
                   		</div>
						
					</fieldset>
				</div>	

				<div class="span3">
					<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				</div>		

	        </div>    

			<?php echo JHtml::_('bootstrap.endTab'); ?>			

			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
			<div class="row-fluid form-horizontal-desktop">				
				<div class="span12">					

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel( 'publish_up' ); ?></div>
                        <div class="controls"><?php echo $this->form->getInput( 'publish_up' ); ?></div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel( 'publish_down' ); ?></div>
                        <div class="controls"><?php echo $this->form->getInput( 'publish_down' ); ?></div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel( 'created' ); ?></div>
                        <div class="controls"><?php echo $this->form->getInput( 'created' ); ?></div>
                    </div>            

				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />		
		<?php echo JHtml::_('form.token'); ?>

	</div>	

</form>		