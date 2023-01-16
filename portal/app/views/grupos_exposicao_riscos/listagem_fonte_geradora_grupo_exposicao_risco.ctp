<div class='actionbar-right'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'grupos_exposicao_riscos', 'action' => 'buscar_fonte_geradora', $codigo_risco), array('escape' => false, 'class' => 'btn btn-success dialog_fonte_geradora', 'title' =>'Adicionar Fonte Geradora'));?>
</div>

<?php if(!empty($dados_fonte_geradora)): ?>
    <table class="table table-striped" id="listagem_fonte_geradora">
        <thead>
            <th>Fonte Geradora</th>
            <th class="acoes">Ações</th>
        </thead>
            <tbody>
                <?php foreach($dados_fonte_geradora as $i => $dados): ?>
                    <tr class="linhas">
                        <td>
                            <?php echo $this->BForm->input('GrupoExposicaoRiscoFonteGeradora.'.$i.'.codigo_fonte_geradora', array('label' => false, 'class' => 'input-xxlarge codigo_fonte_geradora', 'readonly' => true, 'options' => array($dados['FonteGeradora']['codigo'] => $dados['FonteGeradora']['nome']))); ?>
                        </td>
                        <td>
                            <?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash ', 'title' => 'Excluir Fonte Geradora', 'onclick' => 'excluirFonteGeradora(this)')); ?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
    </table>
<?php else:?>
    <table class="table table-striped" id="listagem_fonte_geradora">
        <thead>
            <th>Fonte Geradora</th>
            <th class="acoes">Ações</th>
        </thead>
        <tbody></tbody>
    </table>
    <div id="erro_fonte_geradora" class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 



<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();

        $(document).on('click', '.dialog_fonte_geradora', function(e) {
            e.preventDefault();
            open_dialog(this, 'Fontes Geradoras', 880);
        });
    });

    function excluirFonteGeradora(elemento){
        $(elemento).parent().parent().remove();
    }

    function atualizaListaFonteGeradora(codigo_risco){
        var div = $('#lista-fonte-geradora');
        bloquearDiv(div);
        div.load(baseUrl + 'grupos_exposicao_riscos/listagem_fonte_geradora/' + codigo_risco + '/' + Math.random());
    }
    ") 
?>