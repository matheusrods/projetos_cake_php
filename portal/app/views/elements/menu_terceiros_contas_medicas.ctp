<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';


?>
<?php if (!isset($terceiros)): ?>
	<ul class="sf-menu menu-admin">
<?php endif; ?>
	<?php $diff_name = isset($terceiros) ? ' Terceiros' : '' ?>
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastro '.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operação '.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consulta '.$diff_name,'javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
<?php if (!isset($terceiros)): ?>
	</ul>
<?php endif; ?>