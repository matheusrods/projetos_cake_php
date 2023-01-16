<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Fornecedir','Consulta');?>
	<?php //echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'placeholder' => 'Nome Fantasia', 'label' => 'Nome Fantasia')) ?>
	<?php //echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => 'Razão Social')) ?>						  
	<?php echo $this->BForm->input('documento', array('class' => 'input-xlarge', 'label' => 'Documentos', 'options' => $tipos_documentos, 'empty' => 'Selecione', 'default' => ' ')) ?>
	<?php echo $this->BForm->input('estado', array('class' => 'input-small', 'label' => 'Estado', 'options' => $estados, 'empty' => 'Selecione', 'default' => '', 'onchange' => 'buscaCidade(this);')) ?>
	<span id="cidade_combo" style="display: ;">
		<?php echo $this->BForm->input('cidade', array('label' => 'Cidade', 'class' => 'form-control input-large', 'default' => '','options' => $cidades)); ?>
	</span>
	<span id="carregando_cidade" style="display: none;">
		<label>Cidade</label>
	    <img src="/portal/img/ajax-loader.gif" border="0" style="padding-top: 7px;"/>
	</span>	
</div>
<div class="row-fluid inline">
	<span class="label label-info">Situação:</span>
	<div id='agrupamento'>
        <?php echo $this->BForm->input('situacao', array('type' => 'select', 'multiple' => 'checkbox', 'options' => (array)$situacao, 'label' => '', 'id' => false, 'hiddenField' => false, 'class' => 'checkbox inline input-xsmall')) ?>
    </div>

    <div id="data_periodo" >
	    <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Início', 'type' => 'text', 'class' => 'datepickerjs date input-small form-control', 'multiple', 'default' => date('d/m/Y'), 'oldvalue' => date('d/m/Y'))); ?>
		<?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Fim','type' => 'text', 'class' => 'datepickerjs date input-small form-control', 'multiple', 'default' => date('d/m/Y', strtotime("+ 30 days")), 'oldvalue' => date('d/m/Y', strtotime("+ 30 days")))); ?>
	</div>
</div>
<?php $this->addScript($this->Buonny->link_js('moment.min')) ?>
<?php $this->addScript($this->Buonny->link_js('documentos_prestador.js')); ?>