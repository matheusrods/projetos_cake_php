<?php if(isset($listagem) && count($listagem)) : ?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>

	<div class='well'>
    <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => $this->name, 'action' => $this->action, 'destino','export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
	</div>

    <div class="row-fluid inline">	    	
	        <table class="table table-striped" style='width:1400px;max-width:none;'>
	            <thead>
	                <tr>
	                	<th class="input-mini">Código Unidade</th>
	                    <th class="input-mini">Razão Social</th>
	                    <th class="input-mini">Unidade</th>
	                    <th class="input-mini">Setor</th>
	                    <th class="input-mini">Cargo</th>
	                    <th>Funcionário</th>
	                    <th class="input-mini">CPF</th>
	                    <th class="input-mini">Cod. Matricula</th> 
	                    <th class="input-mini">Matrícula</th> 
	                    <th class="input-mini">P. de Exame</th>
	                    <th class="input-large">Pergunta</th>
	                    <th class="input-mini">Resposta</th>
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
	                        <td><?php echo $linha['Funcionario']['cpf']; ?></td>
	                        <td><?php echo $linha['ClienteFuncionario']['codigo']; ?></td>
	                        <td><?php echo $linha['ClienteFuncionario']['matricula']; ?></td>
	                        <td><?php echo $linha['PedidoExame']['codigo']; ?></td>
	                        <td><?php echo $linha['FichaPsicossocialPerguntas']['pergunta']; ?></td>
	                        <td><?php echo $linha[0]['resposta']; ?></td>
	                    </tr>
	                <?php endforeach; ?>        
	            </tbody>
        	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['FichaPsicossocial']['count']; ?></td>
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
