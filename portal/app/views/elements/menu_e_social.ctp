<?php
$menusCadastro    = '';
$menusOperacoes   = '';
$menusConsultas   = '';
$subMenusESocial  = '';

if (!empty($subMenusESocial)):
	$menusCadastro .= "<li>".$this->Html->link('E-Social','javascript:void(0)');
        $menusCadastro .= "<ul>".$subMenusESocial."</ul>";
    $menusCadastro .= "</li>";
endif;


?>
<ul class="sf-menu menu-admin">
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastro','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operação','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>	
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consulta','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>	
	<?php  echo $this->element('menu_terceiros_e_social', array('terceiros' => true)); ?>
</ul>
