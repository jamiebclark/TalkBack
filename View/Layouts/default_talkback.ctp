<?php 
/**
 * Default Layout for the TalkBack Plugin
 *
 **/
$this->extend('default'); 


// Looks for a breadcrumb element
$crumbElement = $this->request->params['controller'] . '/crumbs';
$crumbFile = APP . 'Plugin' . DS . 'TalkBack' . DS . 'View' . DS . 'Elements' . DS . str_replace('/', DS, $crumbElement) . '.ctp';
if (is_file($crumbFile)) {
	$this->element('TalkBack.' . $crumbElement);
	// TODO: REMOVED THIS FOR NOW
	// echo $this->Html->getCrumbs();
}

echo $this->fetch('content'); ?>