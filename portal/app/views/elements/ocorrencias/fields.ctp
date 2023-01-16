<div class="span9">
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('codigo_sm'); ?>
        <?php echo $this->BForm->hidden('codigo'); ?>
        <?php echo $this->BForm->input('placa', array('readonly' => true, 'class' => 'input-small')); ?>
        <?php
            if ($this->action == 'editar') {
                echo $this->BForm->input('data_ocorrencia', array('type' => 'text', 'readonly' => true, 'class' => 'input-medium datahora'));
            } else {
                echo $this->BForm->input('data_ocorrencia', array('type' => 'text', 'class' => 'datahora input-medium'));
            }
        ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('empresa', array('readonly' => true, 'class' => 'input-xxlarge')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('telefone_empresa', array('readonly' => true, 'class' => 'input-medium')); ?>
        <?php echo $this->BForm->input('Equipamento.Descricao', array('label' => 'Tecnologia', 'readonly' => true, 'class' => 'input-large')); ?>
        <?php echo $this->BForm->hidden('codigo_tecnologia'); ?>
    </div>
    <div class="row-fluid inline" id="tipos_ocorrencia">
        <?php echo $this->BForm->input('OcorrenciaTipoSelecionado.codigo_tipo_ocorrencia', array('class' => 'checkbox inline', 'options' => $tipos_ocorrencia, 'multiple' => 'checkbox')); ?>
    </div>
    <div class="row-fluid inline tipo_ocorrencia_descricao">
        <?php echo $form->input('descricao_tipo_ocorrencia', array('label' => 'Descrição Tipo Ocorrência', 'style' => 'display:none', 'class' => 'tipo_ocorrencia_descricao input-large')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('motorista', array('readonly' => true, 'class' => 'input-xxlarge')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('telefone_motorista', array('readonly' => true, 'class' => 'input-medium')); ?>
        <?php echo $this->BForm->input('celular_motorista', array('readonly' => true, 'class' => 'input-medium')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('longitude', array('label' => 'longitude', 'maxlength' => 11, 'class' => 'longitude input-medium')); ?>
        <?php echo $this->BForm->input('latitude', array('label' => 'latitude', 'maxlength' => 11, 'class' => 'latitude input-medium')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('local', array('label' => 'local', 'class' => 'input-xxlarge')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('rodovia', array('label' => 'rodovia', 'class' => 'input-xxlarge')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('origem', array('readonly' => true, 'class' => 'input-large')); ?>
        <?php echo $this->BForm->input('destino', array('readonly' => true, 'class' => 'input-large')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('observacao', array('class' => 'input-xxlarge')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo_status_ocorrencia', array('class' => 'input-large', 'options' => $status_ocorrencia)); ?>
        <?php echo $this->BForm->input('codigo_prioridade', array('class' => 'input-large', 'label' => 'Prioridade', 'class' => 'input-small', 'options' => array('1' => 'Baixa', '2' => 'Média', '3' => 'Alta'))); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('Usuario.apelido', array('label' => 'Supervisor', 'class' => 'input-large', 'value' => '')) ?>
        <?php echo $this->BForm->input('Usuario.senha', array('label' => 'senha', 'type' => 'password', 'class' => 'input-large', 'value' => '')) ?>
    </div>
    <div class="row-fluid">
        <?php echo $this->BForm->submit('Salvar', array('class' => 'btn')); ?>
        <?php echo $this->BForm->end(); ?>
    </div>
</div>
<?php
    $this->addScript($this->Javascript->codeBlock("jQuery(document).ready(function() {
        setup_mascaras();
        setup_datepicker();
        
        var checkboxOutros = jQuery('#OcorrenciaTipoSelecionadoCodigoTipoOcorrencia13');

        checkboxOutros.change(function() {
            var tipoOutrosSelecionado = $('#OcorrenciaTipoSelecionadoCodigoTipoOcorrencia13').is(':checked');

            if(tipoOutrosSelecionado) {
                $('.tipo_ocorrencia_descricao').show();
            } else {
                $('.tipo_ocorrencia_descricao').hide();
            }
        })
        checkboxOutros.trigger('change');

    });"));
?>