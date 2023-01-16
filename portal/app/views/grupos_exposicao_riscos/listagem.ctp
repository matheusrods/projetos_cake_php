<?php
if(!empty($grupos_exposicao_riscos)):?>
    <?php echo $paginator->options(array('update' => 'div.lista-risco-grupo-exposicao')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código</th>
            <th class="input-xxlarge">Agente</th>
            <th class="input-xxlarge">Substância</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grupos_exposicao_riscos as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['GrupoExposicaoRisco']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo  Risco::retorna_grupo($dados['Risco']['codigo_grupo'] )?></td>
                <td class="input-xxlarge"><?php echo $dados['Risco']['nome_agente'] ?></td>
                <td>               
                    <?php echo $this->Html->link('', array('controller'=> 'grupos_exposicao_riscos', 'action' => 'editar', $dados['ClienteSetor']['codigo_cliente'],$dados['GrupoExposicaoRisco']['codigo_grupo_exposicao'], $dados['GrupoExposicaoRisco']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['GrupoExposicaoRisco']['count']; ?></td>
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
$this->addScript($this->Buonny->link_js('comum.js'));
echo $this->Javascript->codeBlock("

    function atualizaListaRiscos(){
        var div = jQuery('#lista-risco-grupo-exposicao');
        bloquearDiv(div);
        div.load(baseUrl + 'grupos_exposicao_riscos/listagem/".$codigo_cliente."/' + Math.random());
    }
   
");
?>
  