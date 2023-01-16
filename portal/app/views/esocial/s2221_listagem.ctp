<?php if(is_array($listagem) && count($listagem) >= 1) : ?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class="row-fluid inline">

		<?php echo $this->BForm->create('Esocial', array('type' => 'post' ,'url' => array('controller' => 'esocial','action' => 's2221_gerar_zip'))); ?>
    		<div class="row-fluid inline" style="text-align:right; ">
    			<button id="botao" type="submit" class="btn btn-success btn-lg" ><i class="glyphicon glyphicon-share"></i> <i class="icon-download-alt icon-white"></i> Gerar Zip </button>
    		</div>
	    	
	        <table class="table table-striped">
	            <thead>
	                <tr>
	                	<th ></th>
	                	<th >Unidade</th>
	                    <th >Nome Funcionário</th>
	                    <th >CPF</th>
	                    <th >Matrícula</th>
	                    <th >Numero Pedido</th>
	                    <th >Data</th>
                        <th >Status</th>
	                    <th >Ação</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php foreach ($listagem as $key => $linha): ?>
	                    <tr>
	                    	<td>
	                    		<?php echo $this->BForm->input('Esocial.'.$key.'.codigo', array('type' => 'checkbox', 'label' => false, 'value' => $linha['PedidoExame']['codigo'], 'multiple', 'hiddenField' => false)); ?>
	                    	</td>
	                        <td class="input-mini"><?= $linha['PedidoExame']['codigo_cliente']; ?></td>
	                        <td><?= $linha['Funcionario']['nome']; ?></td>
	                        <td><?= $linha['Funcionario']['cpf']; ?></td>
	                        <td><?php echo $linha['ClienteFuncionario']['matricula']; ?></td>
	                        <td><?php echo $linha['PedidoExame']['codigo']; ?></td>
	                        <td><?php echo date_format(date_create_from_format("Y-m-d", $linha[0]['data_baixa']), "d/m/Y"); ?></td>
                            <td>
                                <?php if(is_null($linha['ItemPedidoExameRecusado']['codigo'])) : ?>
                                    <span class="badge badge-empty badge-success" title="Exame Realizado"></span>
                                <?php else : ?>
                                    <span class="badge badge-empty badge-important" title="Exame Recusado"></span>
                                <?php endif; ?>
                            </td>
	                        <td>
	                    		<?php echo $html->link('', array('controller' => 'esocial', 'action' => 's2221_gerar', $linha['PedidoExame']['codigo']), array('class' => 'icon-download-alt', 'title' => 'Gerar XML S-2221')); ?>
	                        </td>
	                    </tr>
	                <?php endforeach; ?>        
	            </tbody>
	        	<tfoot>
		            <tr>
		                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['PedidoExame']['count']; ?></td>
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
