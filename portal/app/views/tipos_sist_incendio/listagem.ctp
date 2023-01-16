<?php if(!empty($tipos_sist_incendio)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código</th>
            <th class="input-xxlarge">Nome</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tipos_sist_incendio as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['TipoSistIncendio']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['TipoSistIncendio']['nome'] ?></td>
                <td>
	                <?php echo $this->Html->link('', array('action' => 'editar', $dados['TipoSistIncendio']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['TipoSistIncendio']['count']; ?></td>
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
	    function atualizaListaTipoSistIncendio() {
		    var div = jQuery('div.lista');
		    bloquearDiv(div);
		    div.load(baseUrl + 'tipos_sist_incendio/listagem/' + Math.random());
		}
	"); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    