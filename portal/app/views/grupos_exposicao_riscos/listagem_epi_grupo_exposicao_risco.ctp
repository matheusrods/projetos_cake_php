<div class='actionbar-right'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'grupos_exposicao_riscos', 'action' => 'buscar_epi', $codigo_risco), array('escape' => false, 'class' => 'btn btn-success dialog_epi', 'title' =>'Adicionar EPI'));?>
</div>

<?php if(!empty($dados_epi)): ?>
    <table class="table table-striped" id="listagem_epi">
        <thead>
            <th>EPI</th>
            <th>Controle</th>
            <th class="acoes">Ações</th>
        </thead>
            <tbody>
                <?php foreach($dados_epi as $i => $dados): ?>
                    <tr class="linhas">
                        <td>
                            <?php echo $this->BForm->input('GrupoExposicaoRiscoEpi.'.$i.'.codigo_epi', array('label' => false, 'class' => 'input-xxlarge codigo_epi', 'readonly' => true, 'options' => array($dados['Epi']['codigo'] => $dados['Epi']['codigo']."-".$dados['Epi']['nome']), 'default' => $dados['Epi']['codigo'])); ?>
                        </td>
                        <td>
                            <?php echo $this->BForm->input('GrupoExposicaoRiscoEpi.'.$i.'.controle_epi', array('label' => false, 'class' => 'input-medium','options' => array('E' => 'Existente', 'R' => 'Recomendado'))); ?>
                        </td>
                        <td>
                            <?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash ', 'title' => 'Excluir Epi', 'onclick' => 'excluirEpi(this)')); ?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
    </table>
<?php else:?>
    <table class="table table-striped" id="listagem_epi">
        <thead>
            <th>EPI</th>
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

        $(document).on('click', '.dialog_epi', function(e) {
            e.preventDefault();
            open_dialog(this, 'EPI', 880);
        });
    });

    function excluirEpi(elemento){
        $(elemento).parent().parent().remove();
    }

    function atualizaListaEpi(codigo_risco){
        var div = $('#lista-epi');
        bloquearDiv(div);
        div.load(baseUrl + 'grupos_exposicao_riscos/listagem_epi/' + codigo_risco + '/' + Math.random());
    }
    ") 
?>