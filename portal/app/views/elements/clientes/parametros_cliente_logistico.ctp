<?php echo $this->BForm->hidden('TPjurPessoaJuridica.pjur_pess_oras_codigo'); ?>
<?php echo $this->BForm->hidden('Cliente.codigo_do_cliente',array('value'=>$codigo_cliente,'label' => 'Código', 'readonly' => TRUE, 'class' => 'input-small')); ?>
<?php echo $this->BForm->hidden('Cliente.codigo_documento',array('label' => 'CNPJ', 'readonly' => TRUE)); ?>

<h4>Tempo para alerta de retenção</h4>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('TVploValorPadraoLogistico.vplo_tempo_retencao1',array('maxlength'=>'3','label' => 'Nível 1', 'class' => 'input-small numeric just-number', 'placeholder' => 'Mínutos')); ?>
    <?php echo $this->BForm->input('TVploValorPadraoLogistico.vplo_tempo_retencao2',array('maxlength'=>'3','label' => 'Nível 2', 'class' => 'input-small numeric just-number', 'placeholder' => 'Mínutos', 'readonly' => 'true')); ?>
    <?php echo $this->BForm->input('TVploValorPadraoLogistico.vplo_tempo_retencao3',array('maxlength'=>'3','label' => 'Nível 3', 'class' => 'input-small numeric just-number', 'placeholder' => 'Mínutos', 'readonly' => 'true')); ?>
</div>

<h4>Definição de Tipo de Transporte por KM</h4>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('TVploValorPadraoLogistico.vplo_km_minimo_ttra',array('maxlength'=>'5','label' => 'KM Mínimo', 'class' => 'input-small numeric just-number', 'placeholder' => 'KM Mínimo')); ?>
    <?php echo $this->BForm->input('TVploValorPadraoLogistico.vplo_ttra_codigo', array('label' => 'Tipo de Transporte', 'class' => 'input-medium','options' => $tipo_transporte , 'empty' => 'Selecione um Tipo')) ?>
</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
</div>  

<?php echo $this->Javascript->codeBlock("

    function valida_campo_nivel() {
        var nivel1 = $('#TVploValorPadraoLogisticoVploTempoRetencao1');
        var nivel2 = $('#TVploValorPadraoLogisticoVploTempoRetencao2');
        var nivel3 = $('#TVploValorPadraoLogisticoVploTempoRetencao3');


        if(nivel1.val() != '') {
            nivel2.prop('readonly', false);
        }else {
            nivel2.val('');
            nivel2.prop('readonly', true);
            nivel3.val('');
            nivel3.prop('readonly', true);
        }

        if(nivel2.val() != '') {
            nivel3.prop('readonly', false);
        }else {
            nivel3.val('');
            nivel3.prop('readonly', true);
        }
    }


    jQuery(document).ready(function(){
        valida_campo_nivel();
        setup_mascaras();
        $('#TVploValorPadraoLogisticoVploTempoRetencao1').change(function(){
           valida_campo_nivel();
        });

         $('#TVploValorPadraoLogisticoVploTempoRetencao2').change(function(){
            valida_campo_nivel();
        });

        $('#TVploValorPadraoLogisticoVploTempoRetencao3').change(function(){
            valida_campo_nivel();
        });
    });

");
?>
