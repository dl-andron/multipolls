<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

defined( '_JEXEC' ) or die; // No direct access
JHtml::_('jquery.framework');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_multipolls/css/multipolls.css');
$document->addScript(JUri::root(true).'/components/com_multipolls/js/own-radio.js');

require_once JPATH_BASE.'/components/com_multipolls/captcha/generate.php';

$crypt_captcha = encrypt_decrypt('encrypt' , generateRandomString(6));

?>

<div class="item-page<?php echo $this->pageclass_sfx ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
	<?php endif; ?>

	<?php if($this->item->show_poll_name): ?>
		<h2 itemprop="headline">
			<?php echo $this->escape($this->item->poll_name); ?>
		</h2>
	<?php endif; ?>

	<div class="pollBody">
		<?php echo $this->item->poll_text; ?>
	</div>

	<?php if (!empty($this->item->poll_content)) :?>

	<form method="post" class='multipoll' id='multipoll-<?php echo $this->item->poll_id ?>' name='multipoll-<?php echo $this->item->poll_id ?>'>

		<?php foreach ($this->item->poll_content as $id_question => $question) : ?>
			
			<?php echo $this->_generateQuestion($id_question, $question); ?>			

		<?php endforeach; ?>		

		<div class="control-group poll-captcha">    	

	    	<div class="controls">  
	            <input class="input" type="text" style="width:80px; display: inline-block;" name="captcha" id="captcha" maxlength="6" required="required" autocomplete="off">   
	            <input type="hidden" name="real-captcha" value="<?php echo $crypt_captcha ?>" >
	            <img style="border: 1px solid gray; background: url('<?php echo JURI::base(true)?>/components/com_multipolls/captcha/bg_capcha.png');" src = "<?php echo JURI::base(true)?>/components/com_multipolls/captcha/captcha.php?data=<?php echo $crypt_captcha?>" id="capcha-pic" alt="captcha"/>
	            <span style="cursor: pointer; margin-left:5px;" id="refresh-captcha" class="icon-refresh refresh-captcha"></span>
	        </div>
		</div>

		<div class="poll-button">			
			<input type="submit" value="<?php echo JText::_('COM_MULTIPOLLS_SEND_VOTE') ?>" class="btn btn-success btn-medium poll-btn" id="poll-<?php echo $this->item->poll_id ?>">
			<?php if ($this->item->result_button) : ?>
				<?php JFactory::getDocument()->addScript(JUri::root(true).'/components/com_multipolls/js/results.js'); ?>
				<input type="button" value="<?php echo JText::_('COM_MULTIPOLLS_CHECK_RESULT') ?>" class="btn btn-success btn-info" name="check-results">
			<?php endif; ?>	
		</div>

		<input type="hidden" name="task" value="poll.saveVote" />
		<input type="hidden" name="id_poll" value="<?php echo $this->item->poll_id ?>">
		<input id="token" type="hidden" name="<?php echo JSession::getFormToken()?>" value="1" />		

	</form>

	<?php endif; ?>

	<?php if($this->item->result_button) :?>
		<div class="results"></div>
	<?php endif; ?>

	<script type="text/javascript">   

    jQuery(document).on('click', '.refresh-captcha', function () {       
        request = jQuery(this).attr('id');            
        jQuery.ajax({
            type   : 'POST',
            url: "<?php echo JURI::base(true)?>/components/com_multipolls/captcha/generate.php",
            data : {'request' : request},
            success: function (response) {
               jQuery('#capcha-pic').attr('src', '<?php echo JURI::base(true)?>/components/com_multipolls/captcha/captcha.php?data=' + response);
               jQuery('input[name=real-captcha]').val(response);
            }
        });
        return false;       
    });  

    jQuery(".multipoll").submit(function(event) {
	    jQuery('.poll-btn', this).attr('disabled', true)
	}); 

	</script>
	
</div>
