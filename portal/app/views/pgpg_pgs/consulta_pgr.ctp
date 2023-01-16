<?php if (empty($pgr)): ?>
	<?php echo $this->BForm->create('TPgpgPg', array('autocomplete' => 'off', 'url' => array('controller' => 'pgpg_pgs', 'action' => 'consulta_pgr'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('pgpg_codigo', array('label' => false, 'placeholder' => 'Nº PGR', 'class' => 'input-small just-number', 'type' => 'text')); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
<?php else: ?>
	<? //debug($dados_embarcador); ?>


<ul class="nav nav-tabs">
  <li class="active"><a href="#pgr" data-toggle="tab">PGR</a></li>
  <?php if ($exibir_racs): ?>
  	<li><a href="#regras-aceite-sm" data-toggle="tab">Regras Aceite SM</a></li>
  <?php endif; ?>
  <li><a href="#clientes" data-toggle="tab">Clientes</a></li>
</ul>

<div class="tab-content">
	<!-- CONFIGURAÇÃO PGR -->
	<div class="tab-pane active bs-docs-sidebar" id="pgr">
		<div class='well'>
			<strong>PGR: </strong><?= $pgr['TPgpgPg']['pgpg_codigo'] ?> - <?= $pgr['TPgpgPg']['pgpg_descricao'] ?> <strong>Tipo: </strong><?= ($pgr['TPgpgPg']['pgpg_tipo_pgr'] == 'L' ? 'Logístico' : ($pgr['TPgpgPg']['pgpg_tipo_pgr'] == 'G' ? 'GR' : 'Indefinido')) ?>
		</div>
		<div class='row'>
			<div class="span3 bs-docs-sidebar">
				<ul class="nav nav-list bs-docs-sidenav">
					<?php $ptits = Set::extract('/TPitePgItem/pite_ptit_codigo', $pgai_pg_associa_items) ?>
					<?php foreach($ptit_pg_tipo_items AS $key => $ptit_pg_tipo_item): ?>
						<?php if (in_array($key, $ptits)): ?>
							<li><a href="#ptit_<?= $key ?>"><i class="icon-chevron-right"></i> <?= $ptit_pg_tipo_item ?></a></li>
						<?php endif ?>
					<?php endforeach ?>
				</ul>
			</div>
			<div class='span8'>
				<?php $pite_ptit_codigo = null ?>
				<?php foreach($pgai_pg_associa_items as $pgai_pg_associa_item): ?>
					<?php if ($pite_ptit_codigo != null && $pgai_pg_associa_item['TPitePgItem']['pite_ptit_codigo'] <> $pite_ptit_codigo): ?>
						</table>
						</section>
					<?php endif ?>
					<?php if ($pgai_pg_associa_item['TPitePgItem']['pite_ptit_codigo'] <> $pite_ptit_codigo): ?>
						<section id="ptit_<?= $pgai_pg_associa_item['TPitePgItem']['pite_ptit_codigo'] ?>">
						<h4><?= $ptit_pg_tipo_items[$pgai_pg_associa_item['TPitePgItem']['pite_ptit_codigo']] ?></h4>
						<table class='table table-striped'>
						<?php $pite_ptit_codigo = $pgai_pg_associa_item['TPitePgItem']['pite_ptit_codigo'] ?>
					<?php endif ?>
					<tr>
						<td><?= $pgai_pg_associa_item['TPitePgItem']['pite_descricao'] ?></td>
						<td class='action-icon'>
							<?php if ($pgai_pg_associa_item['0']['qtd_parametros'] > 0):?>
								<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-wrench', 'title' => 'Visualizar Parâmetros', 'onclick' => "parametros('{$pgr['TPgpgPg']['pgpg_codigo']}', '{$pgai_pg_associa_item['TPitePgItem']['pite_codigo']}')")) ?>
							<?php endif ?>
						</td>
						<td class='action-icon'>
							<?php if ($pgai_pg_associa_item['0']['qtd_acoes'] > 0):?>
								<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-eye-open', 'title' => 'Visualizar Ações', 'onclick' => "acoes('{$pgr['TPgpgPg']['pgpg_codigo']}', '{$pgai_pg_associa_item['TPitePgItem']['pite_codigo']}')")) ?>
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>
				<?php if (!empty($pgai_pg_associa_item)): ?>
					</table>
					</section>
				<?php endif ?>
			</div>
		</div>
	</div>
	<?php if ($exibir_racs): ?>
		<div class="tab-pane" id="regras-aceite-sm">
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
					<th class='input-medium'>Comboio</th>
					<th class='input-medium numeric'>Max.Veic.Comboio</th>
					<th class='input-medium numeric'>Max.Vr.Comboio</th>
					<th class='input-medium'>Carreteiro</th>
					<th class='input-medium'>Agregado</th>
					<th class='input-medium'>Funcionário</th>
					<th class='input-medium'>Verifica Checklist</th>
					<th class='input-medium'>Validade Checklist</th>
					<th class='input-small'>Rodagem de</th>
					<th class='input-small'>Rodagem até</th>
					<th class='action-icon'></th>
					<th class='action-icon'></th>
				</thead>
				<tbody>
					<?php if ($racs): ?>
						<tr>
							<td class='input-medium'><?= ($racs['TEstaEstado']['esta_descricao'] ? $racs['TEstaEstado']['esta_descricao'] : 'Todos') ?></td>
							<td class='input-medium'><?= ($racs['TTtraTipoTransporte']['ttra_descricao'] ? $racs['TTtraTipoTransporte']['ttra_descricao'] : 'Todos') ?></td>
							<td class='input-medium'><?= ($racs['TProdProduto']['prod_descricao'] ? $racs['TProdProduto']['prod_descricao'] : 'Todos') ?></td>
							<td class='input-medium numeric'><?= $racs['TRacsRegraAceiteSm']['racs_valor_maximo_viagem'] ? $this->Buonny->moeda($racs['TRacsRegraAceiteSm']['racs_valor_maximo_viagem'], array('nozero' => true)) : 'Todos' ?></td>
							<td class='input-medium'><?= $racs['TRacsRegraAceiteSm']['racs_escolta_parcial'] ? 'Sim' : 'Não' ?></td>
							<td class='input-medium'><?= $racs['TRacsRegraAceiteSm']['racs_escolta_velada'] ? 'Sim' : 'Não' ?></td>
							<td class='input-medium'><?= ($racs['TRacsRegraAceiteSm']['racs_qtd_escolta_velada'] != 0) ? $racs['TRacsRegraAceiteSm']['racs_qtd_escolta_velada'] : '' ?></td>
							<td class='input-medium'><?= $racs['TRacsRegraAceiteSm']['racs_escolta_armada'] ? 'Sim' : 'Não' ?></td>
							<td class='input-medium'><?= ($racs['TRacsRegraAceiteSm']['racs_qtd_escolta_armada'] != 0) ? $racs['TRacsRegraAceiteSm']['racs_qtd_escolta_armada'] : '' ?></td>
							<td class='input-medium'><?= $racs['TRacsRegraAceiteSm']['racs_obrigar_isca'] ? 'Sim' : 'Não' ?></td>
							<td class='input-medium'><?= $racs['TRacsRegraAceiteSm']['racs_quantidade_comboio'] ? 'Sim' : 'Não' ?></td>
							<td class='input-medium numeric'><?= $this->Buonny->moeda($racs['TRacsRegraAceiteSm']['racs_quantidade_comboio'], array('nozero' => true, 'places' => 0)) ?></td>
							<td class='input-medium numeric'><?= $this->Buonny->moeda($racs['TRacsRegraAceiteSm']['racs_valor_maximo_comboio'], array('nozero' => true)) ?></td>
							<td class='input-medium'><?= $racs['TRacsRegraAceiteSm']['racs_carreteiro'] ? 'Sim' : 'Não' ?></td>
							<td class='input-medium'><?= $racs['TRacsRegraAceiteSm']['racs_agregado'] ? 'Sim' : 'Não' ?></td>
							<td class='input-medium'><?= $racs['TRacsRegraAceiteSm']['racs_funcionario_motorista'] ? 'Sim' : 'Não' ?></td>
							<td class='input-medium'><?= $racs['TRacsRegraAceiteSm']['racs_verificar_checklist']  ? 'Sim' : 'Não' ?></td>
							<td class='input-medium'><?= ($racs['TRacsRegraAceiteSm']['racs_validade_checklist'] != 0)  ? $racs['TRacsRegraAceiteSm']['racs_validade_checklist'] : '' ?></td>
							<td class='input-small'><?= $racs['TRacsRegraAceiteSm']['racs_horario_viagem_de'] ?></td>
							<td class='input-small'><?= $racs['TRacsRegraAceiteSm']['racs_horario_viagem_ate'] ?></td>
							<td class='action-icon'><?php echo $html->link('', array('controller' => 'regras_aceite_sm', 'action' => 'editar', $racs['TRacsRegraAceiteSm']['racs_codigo']), array('class' => 'icon-edit', 'title' => 'Editar', 'onclick' => 'return open_dialog(this, "Editar Regra de Aceite", 950)')) ?></td>
							<td class='action-icon'><?php echo $html->link('', "javascript:void(0)", array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "excluirRacs('{$racs['TRacsRegraAceiteSm']['racs_codigo']}')")) ?></td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
	<div class="tab-pane" id="clientes">
		<div id="filtros_clientes" style="display: none;">
		<?php echo $bajax->form('TPgpgPg', array('autocomplete' => 'off', 'url' => array('controller' => 'pgpg_pgs', 'action' => 'lista_clientes', 'codigo_pgr' => $this->data['TPgpgPg']['pgpg_codigo']), 'divupdate' => '#filtros_clientes')) ?>
			<?php echo $this->BForm->hidden('pgpg_codigo'); ?>
		<?php echo $this->BForm->end() ?>		
		</div>
		<div id="lista-clientes" class="lista-clientes">
		</div>
	</div>

</div>
	<?php echo $this->Javascript->codeBlock("function acoes(pgai_pgpg_codigo, pgai_pite_codigo) {
		var field = null;
		var form = document.createElement('form');
		var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
		form.setAttribute('action', '/portal/pgai_pg_associa_items/consulta_acoes/' + Math.random());
		field = document.createElement('input');
		field.setAttribute('name', 'data[PgaiPgAssociaItem][pgai_pgpg_codigo]');
		field.setAttribute('value', pgai_pgpg_codigo);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[PgaiPgAssociaItem][pgai_pite_codigo]');
		field.setAttribute('value', pgai_pite_codigo);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
		document.body.appendChild(form);
		form.submit();
	}
	function parametros(pgai_pgpg_codigo, pgai_pite_codigo) {
		var field = null;
		var form = document.createElement('form');
		var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
		form.setAttribute('action', '/portal/pgai_pg_associa_items/consulta_parametros/' + Math.random());
		field = document.createElement('input');
		field.setAttribute('name', 'data[PgaiPgAssociaItem][pgai_pgpg_codigo]');
		field.setAttribute('value', pgai_pgpg_codigo);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[PgaiPgAssociaItem][pgai_pite_codigo]');
		field.setAttribute('value', pgai_pite_codigo);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
		document.body.appendChild(form);
		form.submit();
	}

	function carrega_lista_clientes(codigo_cliente) {
        $('#lista-clientes').html('<img src=\'img/loading.gif\' />');
        $.ajax({
            url: baseUrl + 'pgpg_pgs/lista_clientes/codigo_pgr:' + codigo_cliente + '/' + Math.random(),
            dataType: 'html',
            success: function(data) {
                $('#lista-clientes').html(data);
            },
        });		
	}

	jQuery(document).ready(function() {
		carrega_lista_clientes(".$this->data['TPgpgPg']['pgpg_codigo'].");
	});
	",false) ?>
<?php endif ?>
