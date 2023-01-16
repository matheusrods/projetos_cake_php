<h3>Serviços</h3>
<div id="fornecedor-servico-lista" class="grupo">
	<?php
	if(!empty($dados_saude)): ?>
		<table class="table table-striped">
		    <thead>
				<th>Produto / Serviço</th>
				<th class="input-small">Valor</th>
				<th>Tipo de atendimento</th>
				<th>Tempo Liberação</th>	        
		    </thead>
		    <tbody>
			    <?php if (count($dados_saude)): ?>
				    <?php foreach ($dados_saude as $saude): ?>
				    	<tr>
							<td><strong><?= $saude['Produto']['descricao'] ?></strong></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?php 
						
					//	pr($saude['ListaDePrecoProdutoServico']);exit;
						foreach($saude['ListaDePrecoProdutoServico'] as $key => $servico): ?>
					      	<tr class="text-center">
					            <td><?php echo $servico['Servico']['descricao'] ?></td>
					            <td class="input-small"><?php echo $this->Buonny->moeda($servico['valor']);?></td>
					            <td class="text-center">
			   						<?php
			   							echo $this->BForm->input('ListaDePrecoProdutoServico.tipo_atendimento', 
					                        array(
					                            'type' => 'radio', 'options' => array("Ordem de chegada", "Hora marcada", "Não se aplica"), 
					                            'name' => "data[ListaDePrecoProdutoServico][$key][tipo_atendimento]",
					                            'default'=> $servico['tipo_atendimento'],
					                            'multiple' => true,
					                            'legend' => false, 
					                            'label' => array('class' => 'radio inline input-xsmall')
					                        )	
			                    		);

					                    echo $this->BForm->hidden('cod_list_prod_servico', 
					                        array(
					                            'name' => "data[ListaDePrecoProdutoServico][$key][cod_list_prod_servico]", 
					                            'value' => $servico['codigo']
					                        )
			                    		); 
			   						?>
								</td>
								<td>
							
									<?php 
									
									if (!is_null($servico['TempoLiberacaoServico']['codigo_tempo_liberacao'])) {
										echo $this->BForm->input("TempoLiberacaoServico.codigo_tempo_liberacao", array('name' => "data[TempoLiberacaoServico][$key][codigo_tempo_liberacao]" ,'label' => false, 'class' => 'form-control', 'style' => 'text-transform: uppercase;', 'options' => $tempo_liberacao, 'default' => $servico['TempoLiberacaoServico']['codigo_tempo_liberacao'])) ;								

									} else {
										echo $this->BForm->input("TempoLiberacaoServico.codigo_tempo_liberacao", array('name' => "data[TempoLiberacaoServico][$key][codigo_tempo_liberacao]" ,'label' => false, 'class' => 'form-control', 'style' => 'text-transform: uppercase;', 'options' => $tempo_liberacao)) 	;								

									}
									?>
									<?php
									
									echo $this->BForm->hidden('codigo_servico', 
					                        array(
					                            'name' => "data[TempoLiberacaoServico][$key][codigo_servico]", 
					                            'value' => $servico['Servico']['codigo']
					                        )
			                    		); 										
									?>
									
								</td>
					      	</tr>
				      	<?php endforeach; ?>
				    <?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>

<?php
	if ($bloquear == true) {
?>		
	<style>
		#fornecedor-servico-lista input {
			cursor: not-allowed; /* aesthetics */
			pointer-events: none;
		}
	</style>
<?php
	}
?>