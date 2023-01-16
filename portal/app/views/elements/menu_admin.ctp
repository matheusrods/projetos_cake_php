<?php 
$menusCadastro = '';
$menusOperacoes = '';
$menusConsultas = '';
$subMenusAlertas = '';

//SUB MENU. ITEM: ALERTAS
$subMenusAlertas .= $this->BMenu->link('Grupo de Alertas',array('controller'=>'alertas_agrupamentos','action'=>'index'), array('wrapper'=>'li'));
$subMenusAlertas .= $this->BMenu->link('Tipos de Alertas',array('controller'=>'alertas_tipos','action'=>'index'), array('wrapper'=>'li'));
if (!empty($subMenusAlertas)): 
	$menusCadastro .= "<li>".$this->Html->link('Alertas','javascript:void(0)'); 
        $menusCadastro .= "<ul>".$subMenusAlertas."</ul>";
    $menusCadastro .= "</li>";
endif; 

$menusCadastro .= $this->BMenu->link('Multi Empresas',array('controller'=>'multi_empresas','action'=>'index'), array('wrapper'=>'li'));
$menusCadastro .= $this->BMenu->link('Objetos',array('controller'=>'objetos_acl','action'=>'index'), array('wrapper'=>'li'));


?>

<ul class="sf-menu menu-admin">
	<?php if (!empty($menusCadastro)): ?>
		<li><?php echo $this->Html->link('Cadastros','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusCadastro ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusOperacoes)): ?>
		<li><?php echo $this->Html->link('Operações','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusOperacoes ?>
			</ul>
		</li>
	<?php endif; ?>
	<?php if (!empty($menusConsultas)): ?>
		<li><?php echo $this->Html->link('Consultas','javascript:void(0)'); ?>
			<ul>
				<?php echo $menusConsultas ?>
			</ul>
		</li>
	<?php endif; ?>
</ul>