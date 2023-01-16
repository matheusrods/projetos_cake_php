<div class='actionbar-right'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'grupos_exposicao_riscos', 'action' => 'buscar_epc', $codigo_risco), array('escape' => false, 'class' => 'btn btn-success dialog_epc', 'title' =>'Adicionar EPC'));?>
</div>

<?php if(!empty($dados_epc)): ?>
    <table class="table table-striped" id="listagem_epc">
        <thead>
            <th>EPC</th>
            <th>Controle</th>
            <th class="acoes">Ações</th>
        </thead>
        <tbody>
            <?php foreach($dados_epc as $i => $dados): ?>
                <tr class="linhas">
                    <td>
                        <?php echo $this->BForm->input('GrupoExposicaoRiscoEpc.'.$i.'.codigo_epc', array('label' => false, 'class' => 'input-xxlarge codigo_epc', 'readonly' => true, 'size'=> 1, 'options' => array($dados['Epc']['codigo'] => $dados['Epc']['nome']), 'default' => $dados['Epc']['codigo'])); ?>
                    </td>
                    <td>
                        <?php echo $this->BForm->input('GrupoExposicaoRiscoEpc.'.$i.'.controle_epc', array('label' => false, 'class' => 'input-medium','options' => array('E' => 'Existente', 'R' => 'Recomendado'))); ?>
                    </td>
                    <td>
                        <?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash ', 'title' => 'Excluir EPC', 'onclick' => 'excluirEpc(this)')); ?>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>

<?php else:?>
    <table class="table table-striped" id="listagem_epc">
        <thead>
            <th>EPC</th>
            <th>Controle</th>
            <th class="acoes">Ações</th>
        </thead>
        <tbody></tbody>
    </table>
    <div class="alert">Nenhum dado foi encontrado.</div>    
<?php endif;?>




<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();


        $(document).on('click', '.dialog_epc', function(e) {
            e.preventDefault();
            open_dialog(this, 'EPC', 880);
        });

    });

    function excluirEpc(elemento){
        $(elemento).parent().parent().remove();
    }

    function atualizaListaEpc(codigo_risco){
        var div = $('#lista-epc');
        bloquearDiv(div);
        div.load(baseUrl + 'grupos_exposicao_riscos/listagem_epc/' + codigo_risco + '/' + Math.random());
    }
    ") 
?>