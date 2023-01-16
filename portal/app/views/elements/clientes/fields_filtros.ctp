<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
	<?php echo $this->BForm->input('razao_social', array('class' => 'input-medium', 'placeholder' => 'Razão Social', 'label' => false)) ?>
	<?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-medium', 'placeholder' => 'Nome Fantasia', 'label' => false)) ?>
	<?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'label' => false)) ?>
	<?php echo $this->BForm->input('inscricao_estadual', array('class' => 'input-medium', 'placeholder' => 'RG/Inscrição Estadual', 'label' => false)) ?>
	<?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-small', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos', 'selected' => !empty($ativo) ? $ativo : null )); ?>
	<?php echo $this->BForm->input('ultima_atualizacao', array('placeholder'=>'Atualização', 'label' => false, 'class' => 'input-small data', 'title' => 'Clientes com atualização anteriores à')); ?>
</div>        
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_gestor', array('label' => false, 'class' => 'input-medium', 'options' => $gestores, 'empty' => 'Gestor Comercial')); ?>
	<?php echo $this->BForm->input('codigo_gestor_contrato', array('label' => false, 'class' => 'input-medium', 'options' => $gestores, 'empty' => 'Gestor de Contrato')); ?>
	<?php echo $this->BForm->input('codigo_gestor_operacao', array('label' => false, 'class' => 'input-medium', 'options' => $gestores, 'empty' => 'Gestor de Operação')); ?>
	
	<?php if(empty($authUsuario['Usuario']['codigo_corretora'])): ?>
		<?php echo $this->BForm->input('codigo_corretora', array('label' => false, 'class' => 'input-medium', 'options' => $corretoras, 'empty' => 'Todas Corretoras')); ?>
	<?php endif; ?>
	<?php echo $this->Buonny->input_codigo_endereco_regiao($this, $filiais, 'Todas Filiais','codigo_endereco_regiao', false, 'Cliente') ?>	
</div>
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes', 'class' => 'btn')) ;?>