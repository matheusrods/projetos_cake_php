<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => 'Código', 'type' => 'text')) ?>
	<?php echo $this->BForm->input('nome_agente', array('class' => 'input-xlarge', 'placeholder' => 'Nome', 'label' => 'Nome')) ?>
	<?php echo $this->BForm->input('codigo_grupo', array('label' => 'Grupo','class' => 'input-medium', 'options'=> $array_grupo, 'empty' => 'Todos', 'default' => ' ')); ?>
	<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => "Status", 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => '')); ?>
</div>  
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>