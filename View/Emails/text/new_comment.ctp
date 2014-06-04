<?php
$url = $this->Comment->urlArray(array('action' => 'view', $comment['Comment']['id']));
$unsubscribeUrl = $this->Comment->urlArray(array(
	'controller' => 'commenter_email_controls', 
	'action' => 'index',
	$commenter['Commenter']['id'],
	$comment['Comment']['id'],
));

if (!empty($this->request->params['prefix'])) {
	$url[$this->request->params['prefix']] = false;
}
?>
A new comment has been added to a conversation you're a part of:
"<?php echo $this->DisplayText->text($comment['Comment']['body'], array('html' => false)); ?>"

Posted by <?php echo $comment['Commenter'][Configure::read('TalkBack.Commenter.displayField')]; ?> 
on <?php echo date('F j, Y h:iA', strtotime($comment['Comment']['created'])); ?> 

View here:
<?php echo Router::url($url, true); ?>

If you would like to stop getting these email communications, please follow this link to unsubscribe:
<?php echo Router::url($unsubscribeUrl, true); ?>