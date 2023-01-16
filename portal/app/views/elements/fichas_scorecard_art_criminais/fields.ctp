    <div class='row-fluid inline'>
        <?= $this->Buonny->input_codigo_artigo_criminal($this, 'codigo_artigo_criminal', 'Artigo Criminal', true) ?>
        <?= $this->BForm->input('data_ocorrencia',array('class' => 'input-small data','type'=>'text', 'label' => 'Data do Fato')); ?>  
        <?= $this->BForm->input('numero_dp', array('class' => 'input-small numeric just-number', 'label' => 'DP', 'title' => 'Delegacia de Polícia')); ?> 
    </div>
    <div class='row-fluid inline'>
        <?= $this->BForm->input('local_ocorrencia', array('class' => 'input-xlarge', 'label' => 'Local')); ?> 
        <?= $this->Buonny->input_codigo_endereco_cidade($this, 'codigo_endereco_cidade', 'Cidade', true) ?>
    </div>
    <div class='row-fluid inline'>
        <?= $this->BForm->input('inquerito', array('class' => 'input-xlarge', 'label' => 'Inquérito')); ?> 
        <?= $this->BForm->input('data_inquerito', array('class' => 'input-small data', 'type' => 'text', 'label' => 'Data Inquérito')); ?> 
        <?= $this->BForm->input('processo', array('class' => 'input-small', 'label' => 'Processo')); ?> 
        <?= $this->BForm->input('data_processo', array('class' => 'input-small data', 'type' => 'text', 'label' => 'Data Processo')); ?> 
    </div>
    <div class='row-fluid inline'>
        <?= $this->BForm->input('codigo_instituicao',array('class' => 'input-xxlarge','label'=>'Jurisdição', "options" => $instituicoes, 'empty' => 'Selecione')); ?>
    </div>
    <div class='row-fluid inline'>
        <?= $this->BForm->input('codigo_prestador',array('label'=>'Prestador', "options" => $prestadores, 'empty' => 'Selecione')); ?>
        <?= $this->BForm->input('codigo_situacao',array('class' => 'input-xxlarge','label'=>'Situação', "options" => $situcoes_processos, 'empty' => 'Selecione')); ?>
    </div>
    <div class='row-fluid inline'>
        <?= $this->BForm->input('observacao',array('class' => 'input-xxlarge ','type'=>'textarea','cols'=>'90', 'label' => 'Observação')); ?>   
    </div>
    <div class="form-actions">
      <?= $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>

    </div> 
<?= $this->BForm->end() ?>
<?= $this->Javascript->codeBlock('jQuery(document).ready(function() {setup_datepicker()});', false); ?>