<?php echo $this->BForm->create('VeiculoOcorrencia', array('url' => array('controller' => 'veiculos_ocorrencias', 'action' => 'incluir', $codigo_veiculo)));?>
    <?php echo $this->BForm->hidden('Veiculo.codigo')?>
    <div class='row-fluid inline parent'>        
        <?php echo $this->BForm->input('Veiculo.placa',array('class' => 'input-small', 'label' => 'Placa','disabled'=>true ))?>
        <?php echo $this->BForm->input('EnderecoCidade.descricao',array('class' => 'input-xlarge', 'label' => 'Cidade do Emplacamento','disabled'=>true  ))?>
        <?php echo $this->BForm->input('EnderecoEstado.descricao',array('class' => 'input-small', 'label' => 'Estado','disabled'=>true  ))?>
        <?php echo $this->BForm->input('Veiculo.ano',array('class' => 'input-small', 'label' => 'Ano','disabled'=>true  ))?>
    </div>
    <div class='row-fluid inline parent'>
        <?php echo $this->BForm->input('VeiculoCor.descricao',array('class' => 'input-small', 'disabled'=>true, 'label' => 'Cor' ))?>
        <?php echo $this->BForm->input('Veiculo.chassi',array('class' => 'input-xlarge', 'label' => 'Chassi Nº', 'disabled'=>true ))?>
        <?php echo $this->BForm->input('Veiculo.renavam',array('class' => 'input-xlarge', 'label' => 'Renavam','disabled'=>true ))?>
    </div>
    <div class='row-fluid inline parent'>
        <?php echo $this->BForm->input('VeiculoFabricante.descricao',array('class' => 'input-xlarge', 'label' => 'Marca','disabled'=>true ))?>
        <?php echo $this->BForm->input('VeiculoModelo.descricao',array('class' => 'input-xlarge', 'label' => 'Modelo','disabled'=>true ))?>
    </div>
    <div class='row-fluid inline parent'>
        <?php echo $this->BForm->input('VeiculoOcorrencia.codigo_ocorrencia',array('class' => 'input-xlarge', 'label' => 'Tipo de Ocorrência', 'options' => $tipo_ocorrencias ))?>
        <?php echo $this->BForm->input('VeiculoOcorrencia.data_ocorrencia',array('class' => 'input-small data','type'=>'text', 'label' => 'Data da Ocorrência', 'id'=> 'datepicker')); ?>  
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('VeiculoOcorrencia.observacao', array('label' => 'Observações', 'type' => 'textarea', 'class' => 'input-xxlarge')) ?>
    </div>
    <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
          <?php echo $html->link('Voltar',array('controller' => 'veiculos_ocorrencias', 'action' => 'index'), array('class' => 'btn')) ;?>
    </div>
<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_mascaras();
        setup_datepicker();
	});
');
?>