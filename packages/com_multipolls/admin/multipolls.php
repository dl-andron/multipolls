<?php
defined( '_JEXEC' ) or die; // No direct access
/**
 * Component multipolls
 * @author
 */
require_once JPATH_COMPONENT.'/helpers/multipolls.php';
$controller = JControllerLegacy::getInstance( 'multipolls' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();