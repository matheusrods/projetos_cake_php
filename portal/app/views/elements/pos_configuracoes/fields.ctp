
	<div class="row-fluid inline">
	<?= $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['PosCategorias']['codigo'])? $this->data['PosCategorias']['codigo'] : '')); ?>
	
	<div class="row-fluid inline">
	<?= $this->BForm->hidden('codigo_cliente', array('value' => $this->data['Matriz']['codigo'])); ?>
	<br>
		<strong>Código Cliente:</strong> <?= $this->data['Matriz']['codigo']?><br>
		<strong>Nome:</strong> <?= $this->data['Matriz']['nome_fantasia']?><br>
		<br>
    </div>
	   
	<div class="row-fluid inline">
		<?= $this->BForm->input('codigo_pos_ferramenta', array('class' => 'input-xlarge', 'label' => 'Ferramenta <h11 style="color:red">*</h11>', 'options' => array('1' => 'Plano de Ação', '2' => 'Safety walk & talk', '3' => 'Observador EHS'), 'default'=> '3', 'empty' => 'Selecione a Ferramenta')); ?>
    </div>

	<div class="row-fluid inline">
		<?= $this->BForm->input('chave', array('label' => 'Chave <h11 style="color:red">*</h11>', 'class' => 'input-xlarge'));?>
	</div>

	<div class="row-fluid inline">
		<?= $this->BForm->input('descricao', array('label' => 'Descrição <h11 style="color:red">*</h11>', 'class' => 'input-xlarge'));?>
	</div>

	<div class="row-fluid inline">
		<?= $this->BForm->input('valor', array('label' => 'Valor <h11 style="color:red">*</h11>', 'class' => 'input-xlarge'));?>
	</div>
	
	<div class="row-fluid inline">
		<?= $this->BForm->input('observacao', array('label' => 'Observação', 'type'  => 'textarea', 'class' => 'input-xxlarge')); ?>
	</div>

	<div class="row-fluid inline">
		<?= $this->BForm->input('ativo', array( 'label' => 'Status <h11 style="color:red">*</h11>', 'class' => 'input-small', 'options' => array('0'=>'Inativo', '1'=>'Ativo'), 'empty' => 'Todos', 'default' => 1)); ?>
	</div>


 <div class='form-actions'>
	 <?= $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'pos_configuracoes', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
