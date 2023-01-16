<div class="row-fluid inline">
    <?php echo $this->BForm->input('mes', array('class' => 'input-small', 'options' => $meses, 'label' => 'Mês')); ?>
    <?php echo $this->BForm->input('ano', array('class' => 'input-small', 'options' => $anos, 'label' => 'Ano')); ?>
    <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'placeholder' => 'Nome', 'label' => 'Nome')) ?>
    <?php echo $this->BForm->input('apelido', array('class' => 'input-medium', 'placeholder' => 'Login', 'label' => 'Login')) ?>
    <?php echo $this->BForm->input('codigo_departamento', array('class' => 'input-medium', 'options' => $departamentos, 'label' => 'Departamento', 'empty' => 'Todos')); ?>
    <?php echo $this->BForm->input('codigo_operadora', array('class' => 'input-medium', 'options' => $operadoras, 'label' => 'Operadora', 'empty' => 'Todos')); ?>
    <?php echo $this->BForm->input('codigo_tipo_cobranca', array('class' => 'input-medium', 'options' => $tipo_cobranca, 'label' => 'Tipo Cobrança', 'empty' => 'Todos')); ?>
    <?php echo $this->BForm->input('identificador', array('class' => 'input-medium',  'label' => 'Contato')) ?>
</div>
<?php echo $javascript->codeBlock("
    jQuery('#RegistroTelecomCodigoOperadora').change(function() {
        var selected = jQuery(this).val();
        combo_cobranca = jQuery('#RegistroTelecomCodigoTipoCobranca');
        if (selected == null) {
            combo_cobranca.children('option').show();
            combo_cobranca.val('#target option:first');
        }else if(selected == 1 || selected == 2 || selected == 3){
           combo_cobranca.children('option').show();
           combo_cobranca.children('option[value=10]').hide();
           combo_cobranca.children('option[value=10]').css('display','none');
           combo_cobranca.val('#target option:first');
        }else if(selected == 4){
            combo_cobranca.children('option').show();
            combo_cobranca.children('option[value=8]').hide();
            combo_cobranca.children('option[value=8]').css('display','none');
            combo_cobranca.children('option[value=7]').hide();
            combo_cobranca.children('option[value=7]').css('display','none');
            combo_cobranca.children('option[value=9]').hide();
            combo_cobranca.children('option[value=9]').css('display','none');
            combo_cobranca.val('#target option:first');
        }else {
            combo_cobranca.children('option').show();
            combo_cobranca.val('#target option:first');
        }
    });

    jQuery('#RegistroTelecomCodigoTipoCobranca').change(function() {
        var selected = jQuery(this).val();
        combo_operadora = jQuery('#RegistroTelecomCodigoOperadora');
        if (selected == null) {
            combo_operadora.children('option').show();
        }else if(selected == 8 || selected == 7 || selected == 9){
           combo_operadora.children('option').show();
           combo_operadora.children('option[value=4]').hide();
           combo_operadora.children('option[value=4]').css('display','none');
        }else if(selected == 10){
            combo_operadora.children('option').show();
            combo_operadora.children('option[value=2]').hide();
            combo_operadora.children('option[value=2]').css('display','none');
            combo_operadora.children('option[value=1]').hide();
            combo_operadora.children('option[value=1]').css('display','none');
            combo_operadora.children('option[value=3]').hide();
            combo_operadora.children('option[value=3]').css('display','none');
        }else {
            combo_operadora.children('option').show();
        }
    });
    jQuery('#RegistroTelecomCodigoTipoRetorno').change(function() {
        var descricao = jQuery('input#RegistroTelecomIdentificador');
        descricao.unmask();
        descricao.removeClass('format-phone');                
        if ($(this).val() == 1 || $(this).val() == 3 || $(this).val() == 5 ||$(this).val() == 7 ||$(this).val() == 8 ||$(this).val() == 9 ) {
           descricao.addClass('telefone');
           setup_mascaras();
        } else {
           descricao.removeClass('telefone');
        }
    });

   jQuery(document).ready(function(){
        var operadora_selecionada2 = jQuery(this).val();
        if (operadora_selecionada2 == null) {
            jQuery('#RegistroTelecomCodigoTipoRetorno').children('option').show();
        }
    });

"
) ?>
