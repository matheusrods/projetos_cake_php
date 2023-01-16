<?php if(!empty($fontes_geradoras_riscos)):?>
    <?php echo $paginator->options(array('update' => 'div.fontes_geradoras_riscos-lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Grupo de Risco</th>
            <th class="input-xxlarge">Risco</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fontes_geradoras_riscos as $dados): ?>
            <tr>
                <td class="input-xxlarge"><?php echo $dados['GrupoRisco']['descricao'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['Risco']['nome_agente'] ?></td>
                <td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => 'excluirFonteGeradoraRisco('.$dados['FonteGeradoraRisco']['codigo'].');', 'class' => 'icon-trash ', 'title' => 'Excluir Risco')); ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['FonteGeradoraRisco']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    
    <?php echo $this->Js->writeBuffer(); ?>
    <?php 
echo $this->Javascript->codeBlock("

    function excluirFonteGeradoraRisco(codigo){
        if (confirm('Deseja realmente excluir ?')){
            $.ajax({
                type: 'POST',        
                url: baseUrl + 'fontes_geradoras_riscos/excluir/' + codigo +  '/' + Math.random(),        
                dataType : 'json',
                success : function(data){ 
                    if(data == 1){                     
                        atualizaListaRiscos(); 
                    }
                },
                error : function(error){
                    console.log(error);
                }
            }); 
        }
    }    

    function atualizaListaRiscos(){
        var div = jQuery('#fontes_geradoras_riscos-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'fontes_geradoras_riscos/listagem/".$codigo_fonte_geradora."/' + Math.random());
    }
   
");
?>