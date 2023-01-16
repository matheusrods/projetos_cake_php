<div class = 'form-procurar'>
	<div class='actionbar-right'>
	    <?php echo $this->Html->link('Incluir', array('action' => 'incluir', $this->passedArgs['0'], rand()), array( 'title' => 'Adicionar Tipo de Produto', 'class' => 'btn btn-success',)) ?>
	</div>
	<table id='regras-aceite-sm' class='table table-striped' style='width:1500px;max-width:none;'>
		<thead>
			<th class='input-medium'>UF Origem</th>
			<th class='input-medium'>Tipo de Transporte</th>
			<th class='input-medium'>Produto</th>
			<th class='input-medium numeric'>Valor Máximo</th>
			<th class='input-medium'>Escolta Parcial</th>
			<th class='input-medium'>Escolta Velada</th>
			<th class='input-medium'>Qtd. Escolta Velada</th>
			<th class='input-medium'>Escolta Armada</th>
			<th class='input-medium'>Qtd. Escolta Armada</th>
			<th class='input-medium'>Obrigar Isca</th>
			<th class='input-medium'>Qtd. Isca</th>
			<th class='input-medium'>Comboio</th>
			<th class='input-medium numeric'>Max.Veic.Comboio</th>
			<th class='input-medium numeric'>Max.Vr.Comboio</th>
			<th class='input-medium'>Carreteiro</th>
			<th class='input-medium'>Agregado</th>
			<th class='input-medium'>Funcionário</th>
			<th class='input-medium'>Verifica Checklist</th>
			<th class='input-medium'>Validade Checklist</th>
			<th class='input-medium'>Bloquear Entrada de SM</th>
			<th class='input-small'>Rodagem de</th>
			<th class='input-small'>Rodagem até</th>
			<th class='action-icon'></th>
			<th class='action-icon'></th>
		</thead>
		<tbody>
			<?php foreach ($racs as $key => $rac): ?>
				<tr>
					<td class='input-medium'><?= ($rac['TEstaEstado']['esta_descricao'] ? $rac['TEstaEstado']['esta_descricao'] : 'Todos') ?></td>
					<td class='input-medium'><?= ($rac['TTtraTipoTransporte']['ttra_descricao'] ? $rac['TTtraTipoTransporte']['ttra_descricao'] : 'Todos') ?></td>
					<td class='input-medium'><?= ($rac['TProdProduto']['prod_descricao'] ? $rac['TProdProduto']['prod_descricao'] : 'Todos') ?></td>
					<td class='input-medium numeric'><?= $rac['TRacsRegraAceiteSm']['racs_valor_maximo_viagem'] ? $this->Buonny->moeda($rac['TRacsRegraAceiteSm']['racs_valor_maximo_viagem'], array('nozero' => true)) : 'Todos' ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_escolta_parcial'] ? 'Sim' : 'Não' ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_escolta_velada'] ? 'Sim' : 'Não' ?></td>
					<td class='input-medium'><?= ($rac['TRacsRegraAceiteSm']['racs_qtd_escolta_velada'] != 0) ? $rac['TRacsRegraAceiteSm']['racs_qtd_escolta_velada'] : '' ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_escolta_armada'] ? 'Sim' : 'Não' ?></td>
					<td class='input-medium'><?= ($rac['TRacsRegraAceiteSm']['racs_qtd_escolta_armada'] != 0) ? $rac['TRacsRegraAceiteSm']['racs_qtd_escolta_armada'] : '' ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_obrigar_isca'] ? 'Sim' : 'Não' ?></td>
					<td class='input-medium'><?= ($rac['TRacsRegraAceiteSm']['racs_qtd_isca'] != 0) ? $rac['TRacsRegraAceiteSm']['racs_qtd_isca'] : '' ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_quantidade_comboio'] ? 'Sim' : 'Não' ?></td>
					<td class='input-medium numeric'><?= $this->Buonny->moeda($rac['TRacsRegraAceiteSm']['racs_quantidade_comboio'], array('nozero' => true, 'places' => 0)) ?></td>
					<td class='input-medium numeric'><?= $this->Buonny->moeda($rac['TRacsRegraAceiteSm']['racs_valor_maximo_comboio'], array('nozero' => true)) ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_carreteiro'] ? 'Sim' : 'Não' ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_agregado'] ? 'Sim' : 'Não' ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_funcionario_motorista'] ? 'Sim' : 'Não' ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_verificar_checklist']  ? 'Sim' : 'Não' ?></td>
					<td class='input-medium'><?= ($rac['TRacsRegraAceiteSm']['racs_validade_checklist'] != 0)  ? $rac['TRacsRegraAceiteSm']['racs_validade_checklist'] : '' ?></td>
					<td class='input-medium'><?= $rac['TRacsRegraAceiteSm']['racs_bloquear_sm_checklist']  ? 'Sim' : 'Não' ?></td>					
					<td class='input-small'><?= $rac['TRacsRegraAceiteSm']['racs_horario_viagem_de'] ?></td>
					<td class='input-small'><?= $rac['TRacsRegraAceiteSm']['racs_horario_viagem_ate'] ?></td>
					<td class='action-icon'><?php echo $html->link('', array('controller' => 'regras_aceite_sm', 'action' => 'editar', $rac['TRacsRegraAceiteSm']['racs_codigo'], $this->passedArgs['0']), array('class' => 'icon-edit', 'title' => 'Editar')) ?></td>
					<td class='action-icon'><?php echo $html->link('', "javascript:void(0)", array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "excluirRacs('{$rac['TRacsRegraAceiteSm']['racs_codigo']}')")) ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
<?php echo $this->Javascript->codeBlock("function excluirRacs(racs_codigo) {
	if (confirm('Confirma exclusao?')) {
		$.ajax({
			url: baseUrl + 'regras_aceite_sm/excluir/'+ racs_codigo + '/' + Math.random(),
			beforeSend: function() {
				bloquearDiv($('table#regras-aceite-sm'));
			},
			success: function(data){
				atualizaListaRegrasAceiteSm('{$this->passedArgs['0']}');
			}
		});
	}
	return false;
}")?>