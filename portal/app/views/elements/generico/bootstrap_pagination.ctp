<?php $span = isset($span) ? $span : 8; ?>
<?php $page = isset($this->params['named']['page']) ? $this->params['named']['page'] : 1; ?>
<div class="numbers pagination">
	<ul>
		<?php echo $this->Paginator->prev(
			'« Anterior',
			array(
				'escape' => false,
				'tag' => 'li'
			),
			'<a onclick="return false;">« Anterior</a>',
			array(
				'class'=>'disabled prev',
				'escape' => false,
				'tag' => 'li'
			)
		);?>
		
		<?php $count = $page + $span; ?>
		<?php $i = $page - $span; ?>
		<?php while ($i < $count): ?>
			<?php $options = ''; ?>
			<?php if ($i == $page): ?>
				<?php $options = ' class="active"'; ?>
			<?php endif; ?>
			<?php if ($this->Paginator->hasPage($i) && $i > 0): ?>
				<li<?php echo $options; ?>><?php echo $this->Html->link($i, array("page" => $i, str_replace(' ', '', microtime()))); ?></li>
			<?php endif; ?>
			<?php $i += 1; ?>
		<?php endwhile; ?>
		
		<?php echo $this->Paginator->next(
			'Próxima »',
			array(
				'escape' => false,
				'tag' => 'li'
			),
			'<a onclick="return false;">Próxima »</a>',
			array(
				'class' => 'disabled next',
				'escape' => false,
				'tag' => 'li'
			)
		);?>
	</ul>
</div>