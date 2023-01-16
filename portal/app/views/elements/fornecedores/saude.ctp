<div id="bloquear_inputs">

    <div class='well' style="<?php if ($bloquear == true) { echo 'display:none;' ; } else { echo 'display: block;';}; ?>">
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('Fornecedor.responsavel_tecnico', array('label' => 'Responsável Técnico (*)', 'class' => 'input-xxlarge'));?>
            <?php echo $this->BForm->input('Fornecedor.codigo_conselho_profissional', array('label' => 'Conselho', 'class' => 'input-small', 'options' => $lista_conselhos));?>
            <?php echo $this->BForm->input('Fornecedor.responsavel_tecnico_conselho_numero', array('label' => 'Número do Conselho', 'class' => 'input-small'));?>
            <?php echo $this->BForm->input('Fornecedor.responsavel_tecnico_conselho_uf', array('label' => 'Estado do Conselho', 'class' => 'input-mini uf', 'empty' => false, 'options' => $estados));?>
        </div>
        <div class="row-fluid inline">
            <div class="span3 control-group" style="margin-left: 0">
                <label>Tipo de Atendimento</label>
                <?php echo $this->BForm->input('Fornecedor.tipo_atendimento', array('legend' => false, 'options' => array(0 => 'Ordem de Chegada', 1 => 'Hora Marcada'), 'type' => 'radio', 'hiddenField' => false, 'value' => !isset($this->data['Fornecedor']['tipo_atendimento']) ? '1' : $this->data['Fornecedor']['tipo_atendimento'])); ?>
            </div>
            <div class="span3 control-group" style="margin-left: -85px;width: 306px;">
                <label>Todos os Exames são feitos em um único local?</label>
                <?php echo $this->BForm->input('Fornecedor.exames_local_unico', array('legend' => false, 'options' => array(0 => 'Não', 1 => 'Sim'), 'type' => 'radio', 'hiddenField' => false)); ?>     
            </div>
            <div class="span3 control-group" style="margin-left: 0">
                <label>Utiliza Nosso Sistema de Agendamento?</label>
                <?php echo $this->BForm->input('Fornecedor.utiliza_sistema_agendamento', array('legend' => false, 'options' => array(0 => 'Não', 1 => 'Sim'), 'type' => 'radio', 'hiddenField' => false)); ?>
            </div>
            <div class="span2 control-group" style="margin-left: 0">
                <label>É ambulatório?</label>
                <?php echo $this->BForm->input('Fornecedor.ambulatorio', array('legend' => false, 'options' => array(0 => 'Não', 1 => 'Sim'), 'type' => 'radio', 'hiddenField' => false)); ?>
            </div>
            <div class="span1 control-group" style="margin-left: 0" id="div_cod_cliente">
                <?php echo $this->BForm->input('Fornecedor.ambulatorio_codigo_cliente', array('label' => 'Cod. Cliente', 'class' => 'input-small', 'maxlength' => '20'));?>
            </div>
        </div>
    </div>
    <div id="fornecedor-horario" class="fieldset" style="<?php if ($bloquear == true) { echo 'display:none;' ; } else { echo 'display: block;';}; ?>" >
        <?php echo $this->element('fornecedores/horarios'); ?>
    </div>
    <hr style="<?php if ($bloquear == true) { echo 'display:none;' ; } else { echo 'display: block;';}; ?>"/>
    <div class="row-fluid inline" style="<?php if ($bloquear == true) { echo 'display:none;' ; } else { echo 'display: block;';}; ?>">
        <?php echo $this->BForm->input('FornecedorHorario.horario_atendimento_diferenciado', array('value' => empty($dados_horario[0]['FornecedorHorario']['horario_atendimento_diferenciado']) ? 0 : $dados_horario[0]['FornecedorHorario']['horario_atendimento_diferenciado'], 'legend' => false, 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio','before' => '<div class="fornecedor_radio_checkbox js-horario"><span style="width:350px;">Algum exame possui horário de atendimento diferenciado?</span>','after' => '</div>', 'hiddenField' => false));?>
    </div>
    <div class="fieldset fornecedorHorarioDif" style="width: -webkit-fill-available; display: none;" style="<?php if ($bloquear == true) { echo 'display:none;' ; }; ?>">
        <?php if(isset($fornecedor_hDiferenciado['FornecedorHorarioDiferenciado'])) : ?>
            <h5 id="tituloHorario_edit" >(Dias e Horários) de Atendimento diferenciado: </h5>
            <?php foreach($fornecedor_hDiferenciado['FornecedorHorarioDiferenciado'] as $key => $horario_dif) : ?>
                <div id="periodos_horario_diferenciado_edit" >
                    <table id="horarioDif_<?php echo $key; ?>" class="table table-striped periodo">
                        <thead class="thead-inverse">
                            <tr>
                                <td id="dias_semana" colspan="4">
                                    <?php echo $this->BForm->hidden('FornecedorHorarioDiferenciado.'.$key.'.codigo_fornecedor', array('value' => $this->data['Fornecedor']['codigo'])); ?>
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.$key.'.codigo_servico', 
                                    array(
                                        'options' => $servico_fornecedor, 
                                        'empty' => 'Selecione o Exame',
                                        'value' => $horario_dif['codigo_servico'],
                                        'label' => 'Exames', 
                                        'class' => 'js-uni', 
                                        'style' => 'width: 77%; margin-bottom: 0; margin-top: -6px', 
                                        'div' => 'control-group input text width-full padding-left-10', 
                                        'required' => false)); 
                                    ?>
                                </td>
                                <td>
                                    <?php if($key > 0) : ?>
                                        <?php echo $this->Html->link('<i class="icon-minus icon-minus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-danger', 'title' =>'Incluir', 'onclick' => "$(this).parents('.periodo').remove();")); ?>
                                    <?php else : ?>
                                        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-warning', 'title' =>'Incluir', 'onclick' => "fornecedores.addHorarioEdit(); jQuery('.hora').mask('99:99');")); ?>
                                    <?php endif; ?>                                     
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <span>Dias da semana:</span>
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.$key.'.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'seg') !== false ? 'checked' : ''))); ?> Seg.
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.$key.'.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'ter') !== false ? 'checked' : ''))); ?> Ter.
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.$key.'.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'qua') !== false ? 'checked' : ''))); ?> Qua.
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.$key.'.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'qui') !== false ? 'checked' : ''))); ?> Qui.
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.$key.'.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'sex') !== false ? 'checked' : ''))); ?> Sex.
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.$key.'.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'sab') !== false ? 'checked' : ''))); ?> Sab.
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.$key.'.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'dom') !== false ? 'checked' : ''))); ?> Dom.
                                </td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.($key).'.de_hora', array('class' => 'form-control hora', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'value' => sprintf("%04s", $horario_dif['de_hora']))); ?>
                                </td>
                                <td>
                                    <label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> ATÉ </label>
                                    <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.'.($key).'.ate_hora', array('class' => 'form-control hora', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'value' => sprintf("%04s", $horario_dif['ate_hora']))); ?>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>                                  
                            </tr>
                        </tbody>
                    </table>
                </div>              
            <?php endforeach; ?>
        <?php else : ?>
            <h5 id="tituloHorario">(Dias e Horários) de Atendimento diferenciado: </h5>
            <div id="periodos_horario_diferenciado">
                <table id="horarioDif_0" class="table table-striped periodo">
                    <thead class="thead-inverse">
                        <tr>
                            <td id="dias_semana" colspan="4">
                                <?php echo $this->BForm->hidden('FornecedorHorarioDiferenciado.0.codigo_fornecedor', array('value' => $this->data['Fornecedor']['codigo'])); ?>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.codigo_servico', 
                                    array(
                                        'options' => $servico_fornecedor, 
                                        'empty' => 'Selecione o Exame',
                                        'label' => 'Exames', 
                                        'class' => 'js-uni', 
                                        'style' => 'width: 100%; margin-bottom: 0; margin-top: -6px', 
                                        'div' => 'control-group input text width-full padding-left-10', 
                                        'required' => false)); 
                                    ?>
                            </td>
                            <td>
                                <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-warning', 'title' =>'Incluir', 'onclick' => "fornecedores.addHorario(); jQuery('.hora').mask('99:99');")); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <span>Dias da semana: </span>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
                            </td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
                            </td>
                            <td>
                                <label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> ATÉ </label>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.0.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
                            </td>
                            <td></td>
                            <?php //force ?>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <hr style="<?php if ($bloquear == true) { echo 'display:none;' ; } else { echo 'display: block;';}; ?>"/>
    <div id="modelos" style="<?php if ($bloquear == true) { echo 'display:none;' ; } else { echo 'display: block;';}; ?>">
        <div id="horario_periodo" style="display:none;">
            <div id="periodos_horario_diferenciado">
                <table id="horarioDif_X" class="table table-striped periodo">
                    <thead class="thead-inverse">
                        <tr>
                            <td id="dias_semana" colspan="4">
                                <?php echo $this->BForm->hidden('FornecedorHorarioDiferenciado.X.codigo_fornecedor', array('value' => $this->data['Fornecedor']['codigo'])); ?>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.codigo_servico', 
                                    array(
                                        'options' => $servico_fornecedor, 
                                        'empty' => 'Selecione o Exame',
                                        'label' => 'Exames', 
                                        'class' => 'js-uni', 
                                        'style' => 'width: 100%; margin-bottom: 0; margin-top: -6px', 
                                        'div' => 'control-group input text width-full padding-left-10', 
                                        'required' => false)); 
                                    ?>
                            </td>
                            <td>
                                <?php echo $this->Html->link('<i class="icon-minus icon-minus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-danger', 'title' =>'Remover', 'onclick' => "$(this).parents('.periodo').remove();")); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <span>Dias da semana: </span>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
                            </td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
                            </td>
                            <td>
                                <label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> ATÉ </label>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="horario_periodo_edit" style="display:none;">
            <div id="periodos_horario_diferenciado_edit">
                <table id="horarioDif_X" class="table table-striped periodo">
                    <thead class="thead-inverse">
                        <tr>
                            <td id="dias_semana" colspan="4">
                                <?php echo $this->BForm->hidden('FornecedorHorarioDiferenciado.X.codigo_fornecedor', array('value' => $this->data['Fornecedor']['codigo'])); ?>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.codigo_servico', 
                                    array(
                                        'options' => $servico_fornecedor, 
                                        'empty' => 'Selecione o Exame',
                                        'label' => 'Exames', 
                                        'class' => 'js-uni', 
                                        'style' => 'width: 100%; margin-bottom: 0; margin-top: -6px', 
                                        'div' => 'control-group input text width-full padding-left-10', 
                                        'required' => false)); 
                                    ?>
                            </td>
                            <td>
                                <?php echo $this->Html->link('<i class="icon-minus icon-minus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-danger', 'title' =>'Remover', 'onclick' => "$(this).parents('.periodo').remove();")); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <span>Dias da semana: </span>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
                            </td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
                            </td>
                            <td>
                                <label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> ATÉ </label>
                                <?php echo $this->BForm->input('FornecedorHorarioDiferenciado.X.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="fornecedor-corpo_clinico" class="fieldset" style="<?php if ($bloquear == true) { echo 'display:none;' ; } else { echo 'display: block;';}; ?>"style="<?php if ($bloquear == true) { echo 'display:none;' ; } else { echo 'display: block;';}; ?>">
        <?php echo $this->element('fornecedores/medicos'); ?>
    </div>
    <div id="fornecedor-saude" class="fieldset" style="display: block;">
    <?php echo $this->element('fornecedores/servicos_saude'); ?>
    </div>

</div>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaFornecedorHorario();
        atualizaFornecedorMedico();
        $('.fornecedorHorarioDif').hide();
        if( $('#FornecedorHorarioHorarioAtendimentoDiferenciado0').is(\":checked\") ){
            $('.fornecedorHorarioDif').hide();
        }
        if( $('#FornecedorHorarioHorarioAtendimentoDiferenciado1').is(\":checked\") ){
            $('.fornecedorHorarioDif').show();
        }
        $('input:radio[id=\"FornecedorHorarioHorarioAtendimentoDiferenciado1\"]').click(function() {
            $('.fornecedorHorarioDif').show();
        });
        $('input:radio[id=\"FornecedorHorarioHorarioAtendimentoDiferenciado0\"]').click(function() {
            $('.fornecedorHorarioDif').hide();
        }); 

        //ambulatorio
        if( $('#FornecedorAmbulatorio1').is(\":checked\") ){
            $('#div_cod_cliente').show();
        }
        if( $('#FornecedorAmbulatorio0').is(\":checked\") ){
            $('#div_cod_cliente').hide();
        }

        $('input:radio[id=\"FornecedorAmbulatorio1\"]').click(function(){
            $('#div_cod_cliente').show();
        });

        $('input:radio[id=\"FornecedorAmbulatorio0\"]').click(function(){
            $('#div_cod_cliente').hide();
        });
        //fim ambulatorio

    });
    function atualizaFornecedorHorario(){
        var div = jQuery('#fornecedor-horario-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'fornecedores_horarios/listagem/".$this->data['Fornecedor']['codigo']."/' + Math.random());
    }
    function atualizaFornecedorMedico(){
        var div = jQuery('#fornecedor-medico-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'fornecedores_medicos/listagem/".$this->data['Fornecedor']['codigo']."/' + Math.random());
    }

    var divsaude = jQuery('div#saude');
    var codigo_prestador = $('#FornecedorCodigo').val();

    $(\"input:radio[id='FornecedorTipoAtendimento0']\").click(function() {
        var tipo = '0';
        swal({
            type: 'warning',
            title: 'Atenção',
            text: 'Você deseja atualizar o tipo de atendimento de todos os serviços como Ordem de Chegada?',
            showCancelButton: true,
            confirmButtonColor: '#FF0000',
            cancelButtonColor: '#ADD8E6',
            cancelButtonText: 'Não',
            confirmButtonText: 'Sim',
            showLoaderOnConfirm: true
        }, 
        function(){
            $.ajax({
                url: baseUrl + 'fornecedores/atualiza_tipo_atendimento/' + codigo_prestador + '/' + tipo + '/',
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    bloquearDiv(divsaude);  
                },
            })
            .done(function(response) {
                if(response == 1) {
                    desbloquearDiv(divsaude);
                    // $( '#saude' ).load(window.location.href + ' #saude' );
                    location.reload();
                    swal('Sucesso!', 'Todos os serviços deste prestador estao como Ordem de chegada. Aguarde só um momento para atualizarmos a sua tela.', 'success');
                } else {
                    swal('Erro!', 'Não foi possivel atualiza todos os serviços :)', 'error');
                     desbloquearDiv(divsaude);
                }
            });
        });
    });

    $(\"input:radio[id='FornecedorTipoAtendimento1']\").click(function() {
        var tipo = '1';
        swal({
            type: 'warning',
            title: 'Atenção',
            text: 'Você deseja atualizar o tipo de atendimento de todos os serviços como Hora Marcada?',
            showCancelButton: true,
            confirmButtonColor: '#FF0000',
            cancelButtonColor: '#ADD8E6',
            cancelButtonText: 'Não',
            confirmButtonText: 'Sim',
            showLoaderOnConfirm: true
        }, 
        function(){
            $.ajax({
                url: baseUrl + 'fornecedores/atualiza_tipo_atendimento/' + codigo_prestador + '/' + tipo + '/',
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                   bloquearDiv(divsaude);
                },
            })
            .done(function(response) {
               if(response == 1) {
                    desbloquearDiv(divsaude);
                    // $( '#saude' ).load(window.location.href + ' #saude' );
                    location.reload();
                    swal('Sucesso!', 'Todos os serviços deste prestador estao como Hora Marcada. Aguarde só um momento para atualizarmos a sua tela.', 'success');
                } else {
                    swal('Erro!', 'Não foi possivel atualiza todos os serviços :)', 'error');
                     desbloquearDiv(divsaude);
                }
            });
        });
    });
")
?>
