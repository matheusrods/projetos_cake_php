<div class='well'>
	<strong>Código da Lista de Preço: </strong><?= $lista_de_preco['ListaDePreco']['codigo'] ?>
	<strong>Lista de Preço: </strong><?= $lista_de_preco['ListaDePreco']['descricao'] ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'listas_de_preco_produto_servico', 'action' => 'incluir', $this->passedArgs[0]), array('title' => 'Incluir Produto', 'class' => 'btn btn-success', 'escape' => false)) ?>
</div>
<?php echo $this->BForm->create('ListaDePrecoProdutoServico', array('type' => 'POST','autocomplete' => 'off', 'url' => array('controller' => 'listas_de_preco_produto', 'action' => 'index_salvar', $codigo_lista_de_preco))) ; ?>
	<table class='table' style='width:1260px;max-width:none;'>
		<thead>
			<th>Produto / Serviço</th>
			<!-- <th class='input-small numeric' style="width:-60px; display:none" title='Prêmio Mínimo'>Valor Máximo</th> -->
			<th class='input-small numeric' title='Prêmio Mínimo'>Valor Venda</th>
			<th class='input-small numeric'>Valor Base</th>
			<th style="width:153px;">Tipo de Atendimento</th>
			<th class='action-icon'></th>
			<th class='action-icon'></th>
		</thead>
		<tbody>
			<?php if (count($produtos)): ?>
				<?php foreach($produtos as $produto): ?>
					<tr>
						<td><strong><?= $produto['Produto']['descricao'] ?></strong></td>
						<!-- <td></td> -->
						<td></td>
						<td></td>
						<td></td>
						<td class='action-icon'></td>
						<td class='action-icon'></td>
					</tr>
					<?php foreach($produto['ListaDePrecoProdutoServico'] as $key => $servico): ?>
						<?php

						echo $this->BForm->hidden('ListaDePrecoProdutoServico.codigo_lpps', 
	                        array(
	                            'name' => "data[$key][codigo]", 
	                            'value' => $servico['codigo']
	                        )
                    	);

                    	echo $this->BForm->hidden('ListaDePrecoProdutoServico.codigo_lista_de_preco_produto', 
	                        array(
	                            'name' => "data[$key][codigo_lista_de_preco_produto]", 
	                            'value' => $servico['codigo_lista_de_preco_produto']
	                        )
                    	);

						?>
						<tr style='font-size:0.88em;font-style:italic'>
							<td style='padding-left:20px'>
								<?= $servico['Servico']['descricao'] ?>		
							</td>
							<td style="text-align:center;display:none" class='numeric'>
								<?= $this->BForm->input("ListaDePrecoProdutoServico.valor_maximo", array('label' => false, 'class' => 'input-mini numeric moeda', 'name' => "data[$key][valor_maximo]", 'default' => $this->Buonny->moeda($servico['valor_maximo']), 'maxlength' => 14, 'type' => 'hidden')) ?>
							</td>
							<td style="text-align:center;" class='numeric'>
								<?= $this->BForm->input("ListaDePrecoProdutoServico.valor_venda", array('label' => false, 'class' => 'input-mini numeric moeda', 'name' => "data[$key][valor_venda]", 'default' => $this->Buonny->moeda($servico['valor_venda']), 'maxlength' => 14)) ?>		
							</td>
							<td style="text-align:center;" class='numeric'>
								<?= $this->BForm->input("ListaDePrecoProdutoServico.valor", array('label' => false, 'class' => 'input-mini numeric moeda', 'name' => "data[$key][valor]", 'default' => $this->Buonny->moeda($servico['valor']), 'maxlength' => 14)) ?>								
							</td>
							<td class='input-small'>
				                <?php
				                echo $this->BForm->input('ListaDePrecoProdutoServico.tipo_atendimento', 
			                       array(
			                           'type' => 'radio', 'options' => array("Ordem de chegada", "Hora marcada", "Não se aplica"), 
			                            'name' => "data[$key][tipo_atendimento]",
			                            'default'=> $servico['tipo_atendimento'],
			                            'multiple' => true,
			                            'legend' => false, 
			                            'label' => array('class' => 'radio inline input-xsmall informacao')
			                        )	
                    			); 
				                ?>
							</td>
							<td class='action-icon'>
								
								<!-- comentado por que a nova demanda quer que cada servico seja editado nesta tela -->
								<?//= $this->Html->link('', array('controller' => 'listas_de_preco_produto_servico', 'action' => 'editar', $this->passedArgs[0], $servico['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
									
							</td>
							<td class='action-icon'>
								<?= $this->Html->link('', array('controller' => 'listas_de_preco_produto_servico', 'action' => 'excluir', $this->passedArgs[0], $servico['codigo']), array('onclick' => 'return confirm("Confirma a exclusão?")' , 'class' => 'icon-trash', 'title' => 'Excluir')) ?>	
							</td>
						</tr>
					<?php endforeach ?>
				<?php endforeach ?>
			<?php endif ?>
		</tbody>
	</table>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('controller' => 'listas_de_preco', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function() {
	setup_mascaras()
});") ?>
