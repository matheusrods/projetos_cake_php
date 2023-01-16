<div class="well">
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('codigo_cliente',array('value'=>$cliente['Cliente']['codigo'])) ?>
		<?php echo $this->BForm->hidden('vcen_pjur_pess_oras_codigo') ?>
		<?php echo $this->BForm->hidden('vcen_checklist_dias_validos') ?>
		<?php echo $this->BForm->hidden('vcen_checklist_status') ?>
		<?php echo $this->BForm->hidden('vcen_data_inicio',array('value'=>date('Y-m-d H:i:s'))) ?>

		<?php if($readonly || $readonly_referencia): ?>
			<?php echo $this->BForm->hidden('vcen_refe_referencia') ?>
			<?php echo $this->BForm->input('vcen_refe_referencia_visual',array('readonly'=>true,'label'=>'CD','value'=>$this->data['TRefeReferencia']['refe_descricao'])) ?>
		<?php else: ?>
			<?php echo $this->Buonny->input_referencia($this, '#TVcenViagemChecklistEntradaCodigoCliente', 'TVcenViagemChecklistEntrada', 'vcen_refe_referencia', false, 'CD', true, true) ?>
		<?php endif; ?>
	</div>
</div>

Veículos
<div class="well">

	<strong>Cavalo:</strong>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('vcen_veic_oras_codigo') ?>
		<?php echo $this->BForm->hidden('TCveiChecklistVeiculo.posicao_checklist') ?>
		<? if ($readonly_veiculo): ?>
			<?php echo $this->BForm->input('TVeicVeiculo.veic_placa', array('class' => 'input-small placa-veiculo', 'label'=>'Placa','onkeyup'=>(!$readonly_veiculo ? "consulta_placa('cavalo')" : "return false;"), 'readonly'=>$readonly_veiculo)) ?>
		<? else: ?>
			<?php echo $this->BForm->input('TVeicVeiculo.veic_placa', array('class' => 'input-small placa-veiculo', 'label'=>'Placa','onkeyup'=>(!$readonly_veiculo ? "consulta_placa('cavalo')" : "return false;"),'after'=>$this->Html->link('<i class="icon-search"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-success','style'=>'vertical-align: top;', 'title' => 'Procurar placa', 'onclick'=>'javascript: consulta_placa(\'carreta\');')), 'readonly'=>$readonly_veiculo)) ?>
		<? endif;?>
		<?php echo $this->BForm->input('TCidaCidade.cida_descricao', array('class' => 'input-large', 'label'=>'Cidade', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TEstaEstado.esta_sigla', array('class' => 'input-small', 'label'=>'UF', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TMvecModeloVeiculo.mvec_descricao', array('class' => 'input-large', 'label'=>'Modelo', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TVeicVeiculo.veic_ano_fabricacao', array('class' => 'input-small', 'label'=>'Ano', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TVeicVeiculo.veic_cor', array('class' => 'input-small', 'label'=>'Cor', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TTecnTecnologia.tecn_descricao', array('class' => 'input-large', 'label'=>'Tecnologia', 'readonly'=>true)) ?>
	</div>
	<br/>
	<strong>Carreta:</strong>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('vcen_carr_veic_oras_codigo',array('readonly'=>$readonly)) ?>
		
		<? if ($readonly_carreta): ?>
			<?php echo $this->BForm->input('TVeicVeiculoCarreta.veic_placa', array('class' => 'input-small placa-veiculo',  'label'=>'Placa', 'onkeyup'=>(!$readonly_carreta ? "consulta_placa('carreta')" : "return false;"),'readonly'=>$readonly_carreta)) ?>
		<? else: ?>
			<?php echo $this->BForm->input('TVeicVeiculoCarreta.veic_placa', array('class' => 'input-small placa-veiculo',  'label'=>'Placa', 'onkeyup'=>(!$readonly_carreta ? "consulta_placa('carreta')" : "return false;"),'after'=>$this->Html->link('<i class="icon-search"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-success','style'=>'vertical-align: top;', 'title' => 'Procurar placa', 'onclick'=>'javascript: consulta_placa(\'carreta\');')),'readonly'=>$readonly_carreta)) ?>
		<? endif;?>
		<?php echo $this->BForm->input('TCidaCidadeCarreta.cida_descricao', array('class' => 'input-large', 'label'=>'Cidade', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TEstaEstadoCarreta.esta_sigla', array('class' => 'input-small',  'label'=>'UF', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TMvecModeloVeiculoCarreta.mvec_descricao', array('class' => 'input-large', 'label'=>'Modelo', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TVeicVeiculoCarreta.veic_ano_fabricacao', array('class' => 'input-small', 'label'=>'Ano', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TVeicVeiculoCarreta.veic_cor', array('class' => 'input-small',  'label'=>'Cor', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TTecnTecnologiaCarreta.tecn_descricao', array('class' => 'input-large', 'label'=>'Tecnologia', 'readonly'=>true)) ?>
	</div>

</div>		


Motorista
<div class="well">
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('Cliente.codigo',array('value' => $cliente['Cliente']['codigo'])) ?>
		<?php echo $this->BForm->hidden('Cliente.codigo_documento',array('value' => $cliente['Cliente']['codigo_documento'])) ?>
		<?php echo $this->BForm->hidden('Cliente.iniciar_por_checklist',array('value' => $cliente['Cliente']['iniciar_por_checklist'])) ?>
		<?php echo $this->BForm->hidden('TVcenViagemChecklistEntrada.vcen_moto_pfis_pess_oras_codigo') ?>
		<? if ($readonly): ?>
		<?php echo $this->BForm->input('TPfisPessoaFisica.pfis_cpf', array('class' => 'input-medium formata-cpf', 'label' => 'CPF', 'readonly'=>$readonly)) ?>
		<? else: ?>
		<?php echo $this->BForm->input('TPfisPessoaFisica.pfis_cpf', array('class' => 'input-medium formata-cpf', 'label' => 'CPF', 'readonly'=>$readonly,'after'=>$this->Html->link('<i class="icon-search"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-success','style'=>'vertical-align: top;', 'title' => 'Procurar motorista', 'onclick'=>'javascript: consulta_motorista();')))) ?>
		<? endif;?>
		<?php echo $this->BForm->input('TPessPessoa.pess_nome', array('class' => 'input-xlarge', 'label' => 'Nome', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('Profissional.rg', array('class' => 'input-medium', 'label' => 'RG', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('Profissional.cnh', array('class' => 'input-medium', 'label' => 'CNH', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('TMotoMotorista.posicao_teleconsult', array('class' => 'input-xlarge', 'label' => 'Posição Teleconsult', 'readonly' => true)) ?>
	</div>
	<div class='row-fluid-inline'>
	<?php echo $this->BForm->input('ProfissionalContato.telefone', array('class' => 'input-medium', 'label' => 'Celular','readonly'=>true)) ?>
	<?php echo $this->BForm->hidden('ProfissionalContato.telefone_atual', array('readonly'=>true)) ?>
	</div>
</div>

Itens Checklist
<div>	
	<div class='row-fluid inline'>
		<?php if (is_array($itens_checklist) && count($itens_checklist) > 0): ?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Item</th>
						<th>Resultado</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($itens_checklist as $key => $item_checklist): ?>
					<tr>
						<td><?=$item_checklist['TIcheItemChecklist']['iche_descricao']?></td>
						<td>
							<?php echo $this->BForm->hidden("TVceiViagemChecklistEntradaItem.{$key}.vcei_iche_codigo", array('value'=>$item_checklist['TIcheItemChecklist']['iche_codigo'])) ?>
							<?php echo $this->BForm->input("TVceiViagemChecklistEntradaItem.{$key}.vcei_resultado", array('type' => 'radio','class'=> 'item_checklist', 'value'=>(isset($this->data['TVceiViagemChecklistEntradaItem'][$key]['vcei_resultado']) ? $this->data['TVceiViagemChecklistEntradaItem'][$key]['vcei_resultado'] : ''),'options' => $status_item_checklist,'disabled'=>$readonly,'legend' => FALSE,'required'=>$required_itens, 'label' => array('class' => 'radio inline'))) ?>
						</td>
					</tr>
				<?php endforeach;?>
				</tbody>
			</table>
		<? endif; ?>
	</div>
</div>

<div class="well">
	<div class='row-fluid inline'>
		<span style='padding-top:5px; font-weight: bold; display: inline-block; vertical-align: bottom'>Aprovado:</span>
		<?php echo $this->BForm->hidden('TVcenViagemChecklistEntrada.vcen_aprovado_esperado') ?>
		<?php if($readonly): ?>
			<?php echo $this->BForm->input("TVcenViagemChecklistEntrada.vcen_aprovado", array('type' => 'radio','class'=>'radio_aprovado','options' => $array_sim_nao,'disabled'=>$readonly,'legend' => false, 'div'=>false,'label' => array('class' => 'radio inline'))) ?>
		<?php else: ?>
			<?php echo $this->BForm->hidden('TVcenViagemChecklistEntrada.vcen_aprovado') ?>
			<?php echo $this->BForm->input("TVcenViagemChecklistEntrada.vcen_aprovadochk", array('type' => 'radio','class'=>'radio_aprovado','options' => $array_sim_nao,'readonly'=>$readonly,'legend' => false, 'div'=>false,'label' => array('class' => 'radio inline'))) ?>
		<?php endif; ?>
	</div><br/>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input("TVcenViagemChecklistEntrada.vcen_justificativa", array('type' => 'textarea','label' => 'Justificativa','class' => 'input-xxlarge','readonly'=>$readonly)) ?>
	</div>	
</div>
