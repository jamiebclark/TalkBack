<?php $this->extend('default'); ?>
<h2><?php echo $commenter['Commenter'][Configure::read('TalkBack.Commenter.displayField')]; ?></h2>
<?php if ($currentCommenter['Commenter']['id'] == $commenter['Commenter']['id']): ?>
	<?php echo $this->Html->link('Edit Settings', ['action' => 'edit'], ['class' => 'btn']); ?>
<?php endif; ?>

<?php 
/*
echo $this->Layout->tabMenu([
	['Profile', ['action' => 'view', $commenter['Commenter']['id']]],
	['Topics', ['action' => 'topics', $commenter['Commenter']['id']]],
	['Replies', ['action' => 'comments', $commenter['Commenter']['id']]],
]);
*/
?>
<?php echo $this->fetch('content'); ?>