<?php if (empty($detalhes_evento)): ?>
	<?php echo $this->BForm->create('TEspaEventoSistemaPadrao', array('autocomplete' => 'off', 'url' => array('controller' => 'eventos_viagem', 'action' => 'detalhes_evento'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('espa_codigo', array('label' => false, 'placeholder' => 'Cod. Evento', 'class' => 'input-small just-number', 'type' => 'text')); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
<?php else: ?>
<br/>
<h4>Dados do Evento</h4>
<div class='well'>
	<?php echo $this->BForm->create('TEspaEventoSistemaPadrao', array('autocomplete' => 'off', 'url' => array('controller' => 'eventos_viagem', 'action' => 'detalhes_evento'))) ?>
	<?php echo $this->element('eventos_viagem/fields');?>
</div>

<h4>PGR's</h4>
<? if (is_array($lista_pgr) && count($lista_pgr)>0): ?>
<table id='pgrs' class='table table-striped'>
	<thead >
		<? for($i=1;$i<=4;$i++): ?>
		<th class='input-medium' <?=($i%2<4?'style="border-right: 3px solid #fff;"':"")?>>PGR</th>
		<? endfor; ?>
	</thead>
	<tbody>
		<?php foreach ($lista_pgr as $key => $pgr): ?>
			<? if (($key+1)%4==1): ?>
			<tr>
			<? endif; ?>
				<td class='input-medium' <?=((($key+1)%4)>0?'style="border-right: 3px solid #fff;"':'')?>>
					<?php echo $this->Html->link($pgr['TPgpgPg']['pgpg_codigo'], 'javascript:void(0)', array('onclick' => "visualizar_pgr('{$pgr['TPgpgPg']['pgpg_codigo']}')")) ?>
				</td>
			<? if (($key+1)%4==0): ?>
			</tr>
			<? endif; ?>
		<?php endforeach; ?>
		<? if ( ($key+1)%4>0): ?>
			<?php for($i=(($key+1)%4);$i<4;$i++): ?>
				<td <?=(($i%4)<3?'style="border-right: 3px solid #fff;"':'')?>>&nbsp;</td>
			<?php endfor; ?>
			</tr>
		<? endif; ?>
	</tbody>
</table>
<? else: ?>
<div class=''>Não existe PGR com este evento cadastrado</div>
<? endif; ?>
	<?php echo $this->Javascript->codeBlock("
	function visualizar_pgr(codigo_pgr) {
		var form = document.createElement('form');
		var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('action', '/portal/pgpg_pgs/consulta_pgr/');
		form.setAttribute('target', form_id);
		field = document.createElement('input');
		field.setAttribute('name', 'data[TPgpgPg][pgpg_codigo]');
		field.setAttribute('value', codigo_pgr);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		/*
		field = document.createElement('input');
		field.setAttribute('name', 'data[TRacsRegraAceiteSm][racs_codigo]');
		field.setAttribute('value', racs_codigo);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		*/
		document.body.appendChild(form);
		var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-30)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
		form.submit();
	}
	") ?>
<?php endif ?>