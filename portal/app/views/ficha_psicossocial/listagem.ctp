<?php if(isset($listagem) && count($listagem)) : ?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class="row-fluid inline">	    	
	        <table class="table table-striped">
	            <thead>
	                <tr>
	                	<th style="width:5%">Código Unidade</th>
	                    <th style="width:25%">Razão Social</th>
	                    <th style="width:25%">Unidade</th>
	                    <th style="width:20%">Setor</th>
	                    <th style="width:20%">Cargo</th>
	                    <th style="width:30%">Funcionário</th>
	                    <th style="width:30%">Status</th>
	                    <th style="width:25%">Ação</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php foreach ($listagem as $key => $linha): ?>
	                    <tr>
	                    	<td class="input-mini"><?= $linha['Cliente']['codigo']; ?></td>
	                        <td><?= $linha['Cliente']['razao_social']; ?></td>
	                        <td><?= $linha['Cliente']['nome_fantasia']; ?></td>
	                        <td><?php echo $linha['Setor']['descricao']; ?></td>
	                        <td><?php echo $linha['Cargo']['descricao']; ?></td>
	                        <td><?php echo $linha['Funcionario']['nome']; ?></td>
	                        <td class="right">
	                        	<?php if($linha['ClienteFuncionario']['ativo'] == '1') : ?>
	                        		<span class="badge-empty badge badge-success" title="Ativo"></span>
								<?php elseif($linha['ClienteFuncionario']['ativo'] == '2') : ?>
									<span class="badge-empty badge badge-info" title="Férias"></span>
								<?php elseif($linha['ClienteFuncionario']['ativo'] == '3') : ?>
									<span class="badge-empty badge badge-error" title="Afastado"></span>
	                        	<?php else : ?>
		                        	<span class="badge-empty badge badge-important" title="Inativo"></span>
	                        	<?php endif; ?>
	                        </td>
	                        <td>
                        		<?php echo $html->link('', array('controller' => 'ficha_psicossocial', 'action' => 'listagem_ficha_psicossocial', $linha['FuncionarioSetorCargo']['codigo'],0,$linha['FuncionarioSetorCargo']['codigo_cliente_funcionario']), array('class' => 'icon-wrench', 'title' => 'Ver/Incluir Pedidos')); ?>
	                        </td>
	                    </tr>
	                <?php endforeach; ?>        
	            </tbody>
        	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['FuncionarioSetorCargo']['count']; ?></td>
            </tr>
        </tfoot>
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div> 
        <?php echo $this->BForm->end(); ?>
    </div>
<?php echo $this->Js->writeBuffer(); ?>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('
	function mostra_botao(element) {
		if($(element).val()) {
			$("#botao").show();
		} else {
			$("#botao").hide();
		}
	}
'); ?>
