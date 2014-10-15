<?php
$default = array(
	'neighbors' => null,
	'model' => null,
	'controller' => $this->request->params['controller'],
	'action' => 'view',
	'plugin' => 'TalkBack',
);
extract(array_merge($default, compact(array_keys($default))));

if (!empty($model)) {
	$controller = Inflector::tableize($model);
} else {
	$model = Inflector::classify($controller);
}
?>

<?php if (!empty($neighbors)): ?>
	<ul class="tb-neighbors">
		<?php foreach ($neighbors as $key => $neighbor): 
			if (empty($neighbor)) {
				continue;
			}
			$url = Router::url(compact('controller', 'action', 'plugin') + array($neighbor[$model]['id']));
			$icon = '';
			if ($key == 'prev') {
				$icon = '<i class="fa fa-chevron-left"></i>';
			} else if ($key == 'next') {
				$icon = '<i class="fa fa-chevron-right"></i>';
			}
			?>
			<li class="<?php echo $key; ?>">
				<a href="<?php echo $url; ?>">
					<?php echo $icon; ?>
					<h5 class="tb-comment-title"><?php echo $neighbor[$model]['title']; ?></h5>
					<?php if (!empty($neighbor['Commenter'])): ?>
						<small><?php echo $this->Commenter->name($neighbor['Commenter']); ?></small>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
