<div class="row-fluid inline">
<?php echo $this->Buonny->input_produto_servico($this, $produtos, empty($servicos) ? array() : $servicos, false); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código',false,'Consulta');?>
	
	<?php echo $this->BForm->input('estado', array('class' => 'input-small bselect2', 'placeholder' => 'Estados', 'label' => false, 'options' => $estados, 'empty' => 'Estado', 'default' => '', 'onchange' => 'buscaCidade(this);')) ?> 

  	<span id="cidade_combo">
  		<?php echo $this->BForm->input('cidade', array('label' => false, 'class' => 'input-xlarge bselect2', 'default' => '','options' => $cidades)); ?>
	</span>
    <div id="carregando_cidade" style="display: none;">
    	<img src="/portal/img/ajax-loader.gif" border="0" style="padding-top: 7px;"/>
	</div>
</div>
<div class="row-fluid inline">	
	<?php echo $this->BForm->input('ativo', array('label' => false, 'title' => 'Status', 'class' => 'input-small', 'default' => '','options' => array(0 => 'Inativo', 1 => 'Ativo'), 'empty' => 'Selecione')); ?>
</div>