
<div class='row-fluid inline'>
	<div style=<?php echo (!isset($this->passedArgs['searcher']) ? "''": "'display:none'") ?> >
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'Referencia') ?>
	</div>
	<?php echo $this->BForm->input('descricao', array('label' => false, 'placeholder' => 'Descrição','type' => 'text', 'class' => 'input-xlarge')) ?>
	<?php echo $this->BForm->input('estado', array('label' => false, 'empty' => 'Estado','options' => $estados, 'class' => 'input-small')) ?>
	<?php echo $this->BForm->input('cidade', array('label' => false, 'placeholder' => 'Cidade','type' => 'text', 'class' => 'input-large')) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('classe', array('label' => false, 'empty' => 'Classe','options' => $classes, 'class' => 'medium')) ?>
	<?php echo $this->BForm->input('bandeira', array('label' => false, 'empty' => 'Bandeira','options' => $bandeiras, 'class' => 'medium')) ?>
	<?php echo $this->BForm->input('regiao', array('label' => false, 'empty' => 'Região','options' => $regioes, 'class' => 'medium')) ?>
	<div class="control-group input text">
		<?php echo $this->BForm->input('coordenadas', array('label' => 'Com Lat/Long não encontrados', 'type' => 'checkbox')) ?>
	</div>
</div>
<div class='row-fluid inline'>
  <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
  <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>