<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaListaOcorrencias();");
        exit;
    }
?>
<div id="ocorrencia-acao">
    <?= $bajax->form('Ocorrencia') ?>
    <?= $this->BForm->hidden('codigo') ?>
    <div class="row-fluid inline">
        <span class="span1">SM</span>
        <span class="span11">
            <?= $this->BForm->input('data_ocorrencia', array('type' => 'text', 'readonly' => true, 'label'=>false, 'title' => 'Data da Ocorrência', 'class' => 'input-medium')) ?>
            <?= $this->BForm->input('codigo_sm', array('readonly' => true, 'label' => false, 'title' => 'Número da SM', 'class' => 'input-small')) ?>
            <?php echo $this->BForm->hidden('codigo_tecnologia'); ?>
            <?= $this->BForm->input('Equipamento.Descricao', array('readonly' => true, 'label' => false, 'title' => 'Tecnologia', 'class' => 'input-medium')) ?>
            <?= $this->BForm->input('placa', array('readonly' => true, 'label' => false, 'title' => 'Placa', 'class' => 'input-small')) ?>
        </span>
    </div>
    <div class="row-fluid inline">
        <span class="span1">Empresa</span>
        <span class="span11">
            <?= $this->BForm->input('empresa', array('readonly' => true, 'label' => false, 'title' => 'Empresa', 'class' => 'input-xxlarge')) ?>
            <?= $this->BForm->input('telefone_empresa', array('readonly' => true, 'label' => false, 'title' => 'Telefone', 'class' => 'input-medium')) ?>
        </span>
    </div>
    <div class="row-fluid inline">
        <span class="span1">Motorista</span>
        <span class="span11">
            <?= $this->BForm->input('motorista', array('readonly' => true, 'label' => false, 'title' => 'Motorista', 'class' => 'input-xlarge')) ?>
            <?= $this->BForm->input('telefone_motorista', array('readonly' => true, 'label' => false, 'title' => 'Telefone', 'class' => 'input-medium')) ?>
            <?= $this->BForm->input('celular_motorista', array('readonly' => true, 'label' => false, 'title' => 'Celular', 'class' => 'input-medium')) ?>
        </span>
    </div>
    <div class="row-fluid inline">
        <?= $this->BForm->input('local', array('readonly' => true, 'label' => 'Local', 'class' => 'input-large')) ?>
        <?= $this->BForm->input('rodovia', array('readonly' => true, 'label' => 'Rodovia', 'class' => 'input-large')) ?>
        <?= $this->BForm->input('origem', array('readonly' => true, 'label' => 'Origem', 'class' => 'input-large')) ?>
        <?= $this->BForm->input('destino', array('readonly' => true, 'label' => 'Destino', 'class' => 'input-large')) ?>
    </div>
    <div id="bloqueado" class="row-fluid inline">
        <?= $this->BForm->input('OcorrenciaTipoSelecionado.codigo_tipo_ocorrencia', array('options' => $tipos_ocorrencia, 'multiple' => 'checkbox', 'label' => false, 'class' => 'checkbox inline input-large')) ?>
        <?= $this->BForm->input('descricao_tipo_ocorrencia', array('readonly' => true, 'label' => false, 'class' => 'input-medium', 'title' => 'Descrição para tipo outros')) ?>
        <?= $this->BForm->input('AssinaturaLiberacao.apelido', array('readonly' => true, 'label' => false, 'class' => 'input-medium', 'title' => 'Liberação pelo Responsável')) ?>
        <?= $this->BForm->input('codigo_prioridade', array('class' => 'input-small', 'div' => array('class' => 'form-inline'), 'label' => 'Prioridade', 'options' => array('1' => 'Baixa', '2' => 'Média', '3' => 'Alta'), 'disabled' => true)); ?>
    </div>
    <div class="row-fluid inline">
        <?= $this->BForm->input('codigo_status_ocorrencia', array('options' => $tipos_status, 'label' => 'Status', 'class' => 'input-medium input-xlarge')) ?>
        <?= $this->BForm->input('observacao', array('label' => 'Observações', 'type' => 'textarea', 'class' => 'input-xxlarge')) ?>
    </div>
    <div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
    </div>
    <?= $this->BForm->end() ?>
</div>
<?= $javascript->codeBlock("jQuery(document).ready(function(){
        jQuery('#bloqueado').block({
                    message: '',
    		overlayCSS:  {
    			opacity: 0.0,
                            cursor: 'default'
    		},

    	});
	})") ?>