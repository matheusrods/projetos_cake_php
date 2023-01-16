<table class="table table-striped">
    <thead>
        <th>Horários</th>
        <th>Dias da Semana</th>
        <th></th>
    </thead>
        <?php if(!empty($dados_horario)):?>
        <tbody>
            <?php foreach($dados_horario as $horario): ?>
                <tr>
                    <td>
                        <div class="row-fluid inline">
                            <div class="control-group input text required">
                                <span>De:</span>
                                <?php echo $this->BForm->input('FornecedorHorario.de_hora', array('class' => 'hora input-small', 'label' => false, 'div' => false, 'placeholder' => 'De', 'type' => 'text', 'readonly' => true, 'value' => sprintf('%04s',$horario['FornecedorHorario']['de_hora']))); ?>
                                <span>Até:</span>
                                <?php echo $this->BForm->input('FornecedorHorario.ate_hora', array('class' => 'hora input-small', 'label' => false, 'div' => false, 'placeholder' => 'Até:', 'type' => 'text', 'readonly' => true, 'value' =>  sprintf('%04s',$horario['FornecedorHorario']['ate_hora']))); ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="row-fluid inline">
                            <?php 
                            $dias_selecionados = (explode(',', $horario['FornecedorHorario']['dias_semana']));
                                
                            $dias_semana = array('seg' =>'Segunda-Feira', 'ter' =>'Terça-Feira', 'qua'=>'Quarta-Feira', 'qui'=>'Quinta-Feira', 'sex'=>'Sexta-Feira', 'sab'=>'Sábado','dom'=>'Domingo');
                            ?>

                            <?php echo $this->BForm->input('FornecedorHorario.dias_semana', array('legend' => false, 'options' => $dias_semana, 'selected' => $dias_selecionados, 'multiple'=>'checkbox','before' => '<div class="fornecedor_radio_checkbox" style="width:750px;">','after' => '</div>', 'hiddenField' => false, 'label' => false, 'disabled' => 'disabled')) ?>
                        </div>
                    </td>
                    <td><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => 'excluirFornecedorHorario('.$horario['FornecedorHorario']['codigo'].');', 'class' => 'icon-trash ', 'title' => 'Excluir Horário')); ?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        <?php else:?>
            <tr>
                <td colspan="3">
                    <div>Nenhum dado foi encontrado.</div>
                </td>
            </tr>    
        <?php endif;?>    
</table>


<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
    });

    function excluirFornecedorHorario(codigo){
        if (confirm('Deseja realmente excluir ?')){
            $.ajax({
                type: 'POST',        
                url: baseUrl + 'fornecedores_horarios/excluir/' + codigo +  '/' + Math.random(),        
                dataType : 'json',
                success : function(data){ 
                   atualizaFornecedorHorario();
                },
                error : function(error){
                    console.log(error);
                }
            }); 
        }
    }
    
    function atualizaFornecedorHorario(){
        var div = jQuery('#fornecedor-horario-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'fornecedores_horarios/listagem/".$codigo_fornecedor."/' + Math.random());
    }
    ") 
?>