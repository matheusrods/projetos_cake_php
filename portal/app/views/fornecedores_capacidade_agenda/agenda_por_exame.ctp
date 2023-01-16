<div id="agenda_por_fornecedor">
	<div class='inline well'>
		<?php echo $this->BForm->input('Fornecedor.razao_social', array('value' => $dados_fornecedor['Fornecedor']['razao_social'], 'class' => 'input-xxlarge', 'label' => 'Razão Social' , 'readonly' => true, 'type' => 'text')); ?>
		<?php echo $this->BForm->input('Fornecedor.codigo_documento', array('value' => $dados_fornecedor['Fornecedor']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ', 'readonly' => true, 'type' => 'text')); ?>
		<div style="clear: both;"></div>
	</div>
	
	<div class='actionbar-right'>
		<a href="/portal/fornecedores_capacidade_agenda/incluir/<?php echo $dados_fornecedor['Fornecedor']['codigo']; ?>" class="btn btn-success"><i class="icon-plus icon-white"></i> Incluir</a>
	</div>		
	
	<?php if(count($resultado_exames)):?>
		<div id="listagem">
		    <table class="table table-striped">
		        <thead>
		            <tr>
			            <th class="input-xxlarge">Exame</th>
			            <th class="input-small" style="text-align: center;">Ação</th>
		            </tr>
		        </thead>
			    <tbody>
			        <?php foreach ($resultado_exames as $exames): ?>
				        <tr>
				            <td><?php echo $exames['Servico']['descricao'] ? $exames['Servico']['descricao'] : 'PADRÃO' ?></td>
				            <td style="width:60px; text-align: center;">
				                <?= $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "FornecedoresCapacidadeAgenda.atualizaStatusAgenda('{$exames['ListaDePreco']['codigo_fornecedor']}', '{$exames['FornecedorCapacidadeAgenda']['codigo_lista_de_preco_produto_servico']}', '{$exames['FornecedorCapacidadeAgenda']['ativo']}')"));?>
				                <?php if($exames['FornecedorCapacidadeAgenda']['ativo']== 0): ?>
				                    <span class="badge-empty badge badge-important" title="Desativado"></span>
				                <?php elseif($exames['FornecedorCapacidadeAgenda']['ativo']== 1): ?>
				                    <span class="badge-empty badge badge-success" title="Ativo"></span>
				                <?php endif; ?>
				                
								<?= $html->link('', array('action' => 'editar', $codigo_fornecedor, $exames['FornecedorCapacidadeAgenda']['codigo_lista_de_preco_produto_servico']), array('class' => 'icon-wrench', 'title' => 'Editar')) ?>
							</td>
				        </tr>
			        <?php endforeach; ?>
			    </tbody>
		    </table>
		</div>	
	<?php else : ?>
		<div class="alert">Nenhum dado foi encontrado.</div>
	<?php endif; ?>
</div>
<?php echo $this->Buonny->link_js('fornecedores_capacidade_agenda'); ?>