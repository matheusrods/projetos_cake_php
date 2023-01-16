<div class="div_comboio">
    <?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'editar_comboio',$this->data['TViagViagem']['sm_pai'],$this->data['TViagViagem']['codigo_cliente']))) ?>
    <div>
            <div class="row-fluid inline">
                <?php echo $this->BForm->hidden('codigo_cliente'); ?>
                <?php echo $this->BForm->hidden('sm_pai'); ?>
                <?php if($this->data['editar']): ?>
                    <?php echo $this->Buonny->input_referencia($this, '#TViagViagemCodigoCliente', 'TViagViagem', 'refe_codigo_origem',FALSE,'Alvo Origem',TRUE); ?>
                    <?php echo $this->Buonny->input_referencia($this, '#TViagViagemCodigoCliente', 'TViagViagem', 'refe_codigo_destino',FALSE,'Alvo Destino',TRUE); ?>
                <?php else: ?>
                    <?php echo $this->BForm->input('refe_codigo_origem_visual',array('readonly' => TRUE,'label' => 'Alvo Origem')); ?>
                    <?php echo $this->BForm->input('refe_codigo_destino_visual',array('readonly' => TRUE, 'label' => 'Alvo Destino')); ?>
                <?php endif; ?>
            </div>
    </div>
    <h4>SMs</h4>
    <div class="lista-sms">
        <table class='table table-striped'>
            <thead>
                <th class='input-large'>SM</th>
                <th class='input-large'>Placa</th>
                <th class='input-large'>Motorista</th>
                <th class='input-large'></th>
            </thead>
            <tbody class="sms-table">
                <?php if(isset($this->data['TViagViagem']['viag_codigo_sm'])): ?>
                    <?php foreach($this->data['TViagViagem']['viag_codigo_sm'] as $key => $value): ?>
                        <tr>
                            <td><?php echo $this->BForm->input('viag_codigo_sm_'.$key,array('label' => false,'class' => 'viag_codigo_sm just-number','name' => 'data[TViagViagem][viag_codigo_sm][]','value' => $this->data['TViagViagem']['viag_codigo_sm'][$key],'readonly' => !$this->data['editar'])) ?></td>
                            <td><?php echo $this->BForm->input('placa',array('label' => false,'class' => 'placa-veiculo','readonly' => true,'name' => 'data[TViagViagem][placa][]','value' => $this->data['TViagViagem']['placa'][$key])) ?></td>
                            <td><?php echo $this->BForm->input('motorista',array('label' => false,'readonly' => true,'name' => 'data[TViagViagem][motorista][]','value' => $this->data['TViagViagem']['motorista'][$key])) ?></td>
                            <?php if($this->data['editar']): ?>
                                <?php if($key == 0): ?>
                                    <td><?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "adiciona_linha_sm(jQuery(this).parent().parent())")); ?></td>
                                <?php else: ?>
                                    <td><?php echo $this->Html->link('<i class="icon-minus"></i>', 'javascript:void(0)',array('class' => 'btn', 'escape' => false, 'onclick' => "remove_linha_sm(jQuery(this).parent().parent())")); ?></td>
                                <?php endif; ?>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($this->data['editar']): ?>
        <div class="well">
            <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn')); ?>
            <?php echo $this->Html->link('Cancelar',array('controller' => 'viagens','action' => 'editar_comboio2'),array('class' => 'btn')); ?>
            <?php echo $this->Html->link('Excluir Comboio',array('controller' => 'viagens','action' => 'excluir_comboio', $this->data['TViagViagem']['sm_pai'],$this->data['TViagViagem']['codigo_cliente'] ) ,array('class' => 'btn btn-danger', 'id'=>'excluir_comboio')); ?>
        </div>
    <?php else: ?>
        <div class="well">
            <?php echo $this->Html->link('Voltar',array('controller' => 'viagens','action' => 'editar_comboio2'),array('class' => 'btn')); ?>
        </div>
    <?php endif; ?>
<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_mascaras();
    });
"); ?>
<?php 
if($this->data['editar']):
    echo $this->Javascript->codeBlock("
        $(document).ready(function(){
            $('#excluir_comboio').click(function(){
                if(!confirm('Deseja remover este comboio?')){ 
                    return false;
                }else{
                    bloquearDiv($('.div_comboio'));
                }
            })        
            setup_mascaras();
            $(document).on('blur','.viag_codigo_sm',function(){
                var linha = $(this).parent().parent().parent();
                var sm = $(linha).find('.viag_codigo_sm');
                var placa = $(linha).find('#TViagViagemPlaca');
                var motorista = $(linha).find('#TViagViagemMotorista');

                sm.parent().removeClass('error').find('.error-message').remove();
                if($(this).val() != '' && $.isNumeric($(this).val())){
                    bloquearDivSemImg($(linha).find('td'));
                    $.get(baseUrl+'/viagens/carrega_dados_sm/'+$(this).val()+'/'+$('#TViagViagemCodigoCliente').val()+'/'+Math.random(),function(data){
                        data = $.parseJSON(data);
                        if(data){
                            if(data.cliente){
                                placa.val(data.TVeicVeiculo.veic_placa);
                                motorista.val(data.TPessPessoa.pess_nome);
                            }else{
                                placa.val('');
                                motorista.val('');
                                sm.parent().addClass('error').append('<div class=\"help-block error-message\">SM de outro cliente.</div>');
                            }
                        }else{
                            placa.val('');
                            motorista.val('');
                            sm.parent().addClass('error').append('<div class=\"help-block error-message\">SM não encontrada</div>');
                        }
                        $(linha).find('td').unblock();
                    });
                }
            });
        });

        function adiciona_linha_sm(linha) {
            if ($(linha).find('#TViagViagemViagCodigoSm').val() != '' && $(linha).find('#TViagViagemPlaca').val() != '') {
                var insert_tr = $(linha).clone();
                insert_tr.find('.error').removeClass('error').find('.form-error').removeClass('form-error');
                insert_tr.find('.error-message').remove();
                insert_tr.find('.viag_codigo_sm').val('');
                insert_tr.find('#TViagViagemPlaca').removeClass('format-plate').val('');
                insert_tr.find('#TViagViagemMotorista').val('');
                insert_tr.find('a').attr('onclick', 'remove_linha_sm(jQuery(this).parent().parent())').removeClass('btn-success');
                insert_tr.find('i').removeClass('icon-plus').removeClass('icon-white').addClass('icon-minus');
                $('.sms-table').append(insert_tr);
                setup_mascaras();
            } else {
                alert('Informe uma SM válida');
            }
        }

        function remove_linha_sm(linha) {
            jQuery(linha).remove();
        }

    "); 
endif;
?>
