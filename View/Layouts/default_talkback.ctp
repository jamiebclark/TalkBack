<?php 
/**
 * Default Layout for the TalkBack Plugin
 *
 **/
$this->extend('default'); 

$this->Html->addCrumb('Test', ['controller' => 'topics', 'action' => 'index']);

echo $this->fetch('content'); ?>