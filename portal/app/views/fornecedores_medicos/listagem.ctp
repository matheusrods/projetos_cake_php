<table class="table table-striped">
    <thead>
        <th class="input-xxlarge">Nome</th>
        <th>Número do Conselho</th>
        <th></th>
        <th></th>
        <th></th>
    </thead>
    <?php if (!empty($medicos)) : ?>
        <tbody>
            <?php foreach ($medicos as $key => $medico) : ?>
                <tr>
                    <td class="input-xxlarge"><?php echo $medico['Medico']['nome']; ?></td>
                    <td><?php echo $medico['ConselhoProfissional']['descricao'] . " - " . $medico['Medico']['numero_conselho'] . "/" . $medico['Medico']['conselho_uf']; ?></td>
                    <td class='action-icon'><?php echo $this->Html->link('', '#modal_horario', array('onclick' => 'horariosMedico(' . $medico['FornecedorMedico']['codigo_fornecedor'] . ', ' . $medico['FornecedorMedico']['codigo_medico'] . ', 1);', 'class' => 'icon-calendar ', 'title' => 'Horários disponíveis')); ?></td>
                    <td class="action-icon"><?php echo $this->Html->link('', '#modal_realizacao', array('onclick' => 'especialidades(' . $medico['FornecedorMedico']['codigo_fornecedor'] . ', ' . $medico['FornecedorMedico']['codigo_medico'] . ', 1);', 'class' => 'icon-wrench ', 'title' => 'Especialidades')); ?></td>
                    <td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => 'excluirFornecedorMedico(' . $medico['FornecedorMedico']['codigo'] . ');', 'class' => 'icon-trash ', 'title' => 'Excluir Médico')); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    <?php else : ?>
        <tr>
            <td colspan="3">
                <div>Nenhum dado foi encontrado.</div>
            </td>
        </tr>
    <?php endif; ?>
</table>
<div class="modal fade" id="modal_realizacao" data-backdrop="static"></div>

<div class="modal fade" id="modal_horario" data-backdrop="static"></div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        
        
    });
        
    function excluirFornecedorMedico(codigo){
        if (confirm('Deseja realmente excluir ?')){
            $.ajax({
                type: 'POST',        
                url: baseUrl + 'fornecedores_medicos/excluir/' + codigo +  '/' + Math.random(),        
                dataType : 'json',
                success : function(data){ 
                    atualizaFornecedorMedico(); 
                },
                error : function(error){
                    console.log(error);
                }
            }); 
        }
    }
    function atualizaFornecedorMedico(){
        var div = jQuery('#fornecedor-medico-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'fornecedores_medicos/listagem/" . $codigo_fornecedor . "/' + Math.random());
    }
    function horariosMedico(codigo_fornecedor, codigo_medico, mostra) {
    
         if(mostra) {
          
            var div = jQuery('div#modal_horario');
            bloquearDiv(div);
            div.load(baseUrl + 'fornecedores_medicos/modal_fornecedor_medico_horarios/' + codigo_fornecedor + '/' + codigo_medico + '/' + Math.random());
    
            $('#modal_horario').css('z-index', '1050');
            $('#modal_horario').modal('show');
        } else {           
            $('#modal_horario').modal('hide');
        }
    }
    
    function especialidades(codigo_fornecedor,codigo_medico,mostra) {
        if(mostra) {
            
            var div = jQuery('div#modal_realizacao');
            bloquearDiv(div);
            div.load(baseUrl + 'fornecedores_medicos/modal_fornecedor_medico_especialidade/' + codigo_fornecedor + '/' + codigo_medico + '/' + Math.random());
    
            $('#modal_realizacao').css('z-index', '1050');
            $('#modal_realizacao').modal('show');
        } else {           
            $('#modal_realizacao').modal('hide');
        }
    }
    ");
?>