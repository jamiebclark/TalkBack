<?php
$url = $this->Comment->urlArray(['action' => 'view', $comment['Comment']['id']]);
$unsubscribeUrl = $this->Comment->urlArray([
	'controller' => 'commenters', 
	'action' => 'edit',
	$commenter['Commenter']['id'],
	'#' => 'email-control',
]);

if (!empty($this->request->params['prefix'])) {
	$url[$this->request->params['prefix']] = false;
}

$text = $this->DisplayText->text($comment['Comment']['body'], ['html' => false]);
$text = strip_tags($text);

?>
A new comment has been added to a conversation you're a part of:
"<?php echo trim($text, "\r\n\t "); ?>"

Posted by <?php echo $comment['Commenter'][Configure::read('TalkBack.Commenter.displayField')]; ?> 
on <?php echo date('F j, Y h:iA', strtotime($comment['Comment']['created'])); ?> 

View here:
<?php echo Router::url($url, true); ?>

If you would like to stop getting these email communications, please follow this link to unsubscribe:
<?php echo Router::url($unsubscribeUrl, true); ?>