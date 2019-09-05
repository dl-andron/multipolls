<?php

defined( '_JEXEC' ) or die;

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_multipolls/css/multipolls.css');

$crypt_captcha = encrypt_decrypt('encrypt' , generateRandomString(6));

?>

<?php if(!empty($item)) :?>
<div class="item-poll-<?php echo $item->poll_id ?>">	

	<?php if ($show_title) :?>
		<h2 itemprop="headline"<?php if ($show_content): ?>class="active-title"<?php endif;?>>
			<?php echo $item->poll_name; ?>
		</h2>
	<?php endif; ?>

	<div class="poll-body">

		<?php if ($show_text) :?>
			<div class="poll-desc">
				<?php echo $item->poll_text; ?>
			</div>
		<?php endif; ?>

		<?php if(!empty($item->poll_content)) :?>

		<form method="post" class='multipoll' id='multipoll-<?php echo $item->poll_id ?>' name='multipoll-<?php echo $item->poll_id ?>'>			

			<?php foreach ($item->poll_content as $id_question => $question) : ?>
			
				<?php echo $model->generateQuestion($id_question, $question, 'module'); ?>			

			<?php endforeach; ?>
			
			<div class="control-group poll-captcha">
		    	<div class="controls">  
		            <input class="input" type="text" style="width:80px; display: inline-block;" name="captcha" id="captcha" maxlength="6" required="required" autocomplete="off">   
		            <input type="hidden" name="real-captcha" value="<?php echo $crypt_captcha ?>" >
		            <img style="border: 1px solid gray; background: url('<?php echo JURI::base(true)?>/components/com_multipolls/captcha/bg_capcha.png');" src = "<?php echo JURI::base(true)?>/components/com_multipolls/captcha/captcha.php?data=<?php echo $crypt_captcha?>" class="capcha-pic" id="capcha-pic" alt="captcha"/>
		            <span style="cursor: pointer; margin-left:5px;" id="refresh-captcha" class="icon-refresh refresh-captcha"></span>
		        </div>
			</div>	

			<div class="poll-button">			
				<input type="submit" value="<?php echo JText::_('MOD_MULTIPOLLS_SEND_VOTE') ?>" class="poll-btn btn btn-success" id="vote-poll-<?php echo $item->poll_id ?>">

				<?php if($res_button) : ?>
					<input type="button" value="<?php echo JText::_('MOD_MULTIPOLLS_CHECK_RESULT') ?>" class="btn btn-info" name="results">
				<?php endif; ?>

			</div>

			<input type="hidden" name="id_poll" value="<?php echo $item->poll_id ?>">

			<?php echo JHtml::_('form.token'); ?>	

		</form>
		
		<?php endif; ?>

		<?php if($res_button) :?>
			<div class="results"></div>
		<?php endif; ?>
		
	</div>	

	<script type="text/javascript">   

	    jQuery(document).on('click', '#multipoll-<?php echo $item->poll_id ?> .refresh-captcha', function () {       
	        request = jQuery(this).attr('id');   
	        jQuery.ajax({
	            type   : 'POST',
	            url: "<?php echo JURI::base(true)?>/components/com_multipolls/captcha/generate.php",
	            data : {'request' : request},
	            success: function (response) {
	               jQuery('#multipoll-<?php echo $item->poll_id ?> .capcha-pic').attr('src', '<?php echo JURI::base(true)?>/components/com_multipolls/captcha/captcha.php?data=' + response);
	               jQuery('#multipoll-<?php echo $item->poll_id ?> input[name=real-captcha]').val(response);
	            }
	        });
	        return false;       
	    });     

	</script>

</div>
<?php endif; ?>