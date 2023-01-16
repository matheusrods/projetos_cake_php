<?php if(!empty($medicao)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
            	<th class="input-xlarge">Risco</th>
	            <th class="input-xlarge">Cliente</th>
	            <th class="input-xlarge">Cargo</th>
	            <th class="input-xlarge">Setor</th>
            	<th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medicao as $dados): ?>
            <tr>
            	<td class="input-xlarge"><?php echo $array_risco[$dados['Medicao']['codigo_risco']]; ?></td>
                <td class="input-xlarge"><?php echo $array_cliente[$dados['Medicao']['unidade']]; ?></td>
                <td class="input-xlarge"><?php echo $array_setor[$dados['Medicao']['codigo_setor']]; ?></td>
                <td class="input-xlarge"><?php echo $array_cargo[$dados['Medicao']['codigo_cargo']]; ?></td>
                <td>
	                <?php echo $this->Html->link('', array('action' => 'editar', $dados['Medicao']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Medicao']['count']; ?></td>
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
	    function atualizaListaMedicao() {
		    var div = jQuery('div.lista');
		    bloquearDiv(div);
		    div.load(baseUrl + 'medicao/listagem/' + Math.random());
		}
	"); ?>
	
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    