<h3>EPI (Equipamento de Proteção individual</h3>
<div class='actionbar-right'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'grupos_exposicao_riscos', 'action' => 'incluir', $this->data['GrupoExposicao']['unidade'], $this->data['GrupoExposicao']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Riscos - Grupos Exposição'));?>
</div>
<div class='lista-epi'></div>

<?php debug($epi);
if(!empty($epi)):?>
    <?php echo $paginator->options(array('update' => 'div.lista-epi')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <th class="input-xxlarge">EPI</th>
            <th class="input-medium">Controle</th>
            <th class="acoes">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($epi as $dados): ?>
            <tr>
                <td class="input-xxlarge"><?php echo $dados['Epi']['nome']; ?></td>
                <td class="input-medium"><?php echo $dados['Epi']['nome'];  ?></td>
                <td>               
                    <?php echo $this->Html->link('', array('action' => 'editar', $dados['Epi']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Epi']['count']; ?></td>
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

    function atualizaListaEpi(){
        var div = jQuery('#lista-risco-grupo-exposicao');
        bloquearDiv(div);
        div.load(baseUrl + 'grupos_exposicao_riscos/listagem/".$codigo_cliente."/' + Math.random());
    }
   
");
?>
  