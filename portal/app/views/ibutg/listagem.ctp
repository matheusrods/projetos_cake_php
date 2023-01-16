<?php if(!empty($ibutg)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código</th>
            <th class="input-xxlarge">Nome da Atividade</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ibutg as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Ibutg']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['Ibutg']['nome_atividade'] ?></td>
                <td>
	                <?php echo $this->Html->link('', array('action' => 'editar', $dados['Ibutg']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Ibutg']['count']; ?></td>
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
    <?php echo $this->Js->writeBuffer(); ?>
    <?php echo $this->Javascript->codeBlock("
	    function atualizaListaIbutg() {
		    var div = jQuery('div.lista');
		    bloquearDiv(div);
		    div.load(baseUrl + 'ibutg/listagem/' + Math.random());
		}
	"); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    