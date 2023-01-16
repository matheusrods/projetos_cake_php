<?php if(!empty($listagem)):?>
<div class="row-fluid">
	<?= $this->element('/estatistica_eventos/grafico_estatistica_eventos') ?>
</div>	
<table class='table table-striped table-bordered estatistica_eventos' style="max-width:none; white-space:nowrap">
	<thead>	
		<th><?= $agrupamento?></th>
		<th class="input-mini">Hora</th>
		<th class="input-mini numeric">Qtd. Gerada</th>
		<th class="input-mini numeric">Qtd. Tratada</th>
		<th class="input-mini numeric">Dentro do SLA</th>
		<th class="input-mini numeric">Fora do SLA</th>
		<th class="input-mini numeric">Tempo médio (Minutos)</th>
	</thead>
	<tbody>
		<?php $qtd = 0;?>	
		<?php $qtd_tratada = 0;?>	
		<?php $sla_dentro = 0;?>	
		<?php $sla_fora = 0;?>	

		<?php foreach ($listagem as $dado): ?>
			<tr>					
				<?php $hora = substr($dado[0]['data_hora_evento'], 10,6);?>
				<td><?= $dado[0]['descricao_agrupamento'] ?></td>
				<td class="input-mini"><?= $hora?></td>
				<td class="numeric input-mini"><?= $this->Html->link($this->Buonny->moeda($dado[0]['quantidade'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$this->data['TEeveEstatisticaEvento']['agrupamento']}','{$dado[0]['tipo_agrupamento']}','{$hora}',3,'null')") ?></td>
				<td class="numeric input-mini"><?= $this->Html->link($this->Buonny->moeda($dado[0]['quantidade_tratada'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$this->data['TEeveEstatisticaEvento']['agrupamento']}','{$dado[0]['tipo_agrupamento']}','{$hora}',1,'null')") ?></td>
				<td class="numeric input-mini"><?= $this->Html->link($this->Buonny->moeda($dado[0]['sla_dentro'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$this->data['TEeveEstatisticaEvento']['agrupamento']}','{$dado[0]['tipo_agrupamento']}','{$hora}',1,1)") ?></td>
				<td class="numeric input-mini"><?= $this->Html->link($this->Buonny->moeda($dado[0]['sla_fora'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$this->data['TEeveEstatisticaEvento']['agrupamento']}','{$dado[0]['tipo_agrupamento']}','{$hora}',1,2)") ?></td>
				<td class="numeric input-mini"><?= $this->Buonny->moeda($dado[0]['tempo_medio'], array('nozero' => true, 'places' => 2)) ?></td>
				<?php $qtd += $dado[0]['quantidade'];?>	
				<?php $qtd_tratada += $dado[0]['quantidade_tratada'];?>	
				<?php $sla_dentro += $dado[0]['sla_dentro'];?>	
				<?php $sla_fora += $dado[0]['sla_fora'];?>	
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<th colspan="2">Total</th>
		<th class="numeric"><?= $this->Html->link($qtd , "javascript:analitico('{$this->data['TEeveEstatisticaEvento']['agrupamento']}','','',3,'null')") ?></th>
		<th class="numeric"><?= $this->Html->link($qtd_tratada, "javascript:analitico('{$this->data['TEeveEstatisticaEvento']['agrupamento']}','','',1,'null')")?></th>
		<th class="numeric"><?= $this->Html->link($sla_dentro, "javascript:analitico('{$this->data['TEeveEstatisticaEvento']['agrupamento']}','','',1,'1')")?></th>
		<th class="numeric"><?= $this->Html->link($sla_fora, "javascript:analitico('{$this->data['TEeveEstatisticaEvento']['agrupamento']}','','',1,'2')")?></th>
		<th class="numeric">&nbsp;</th>
	</tfoot>	
</table>
<?php else:?>
	<div class="alert">Nenhum registro encontrado.</div>
<?php endif;?>



<?php echo $this->Javascript->codeBlock("
	function analitico(codigo_tipo,tipo_agrupamento,hora,status,sla) {	
		status_evento_gerado = '';
		if(status == 'null'){
			status_evento = '';
		}else if(status == 3){
			status_evento = '';
			status_evento_gerado = status;
		}else if(status == 4){
			status_evento = '';
		}else{
			status_evento = status;
		}
		if(sla == 'null'){
			status_sla = '';
		}else{
			status_sla = sla;
		}


 		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/estatistica_eventos/analitico/1/' + Math.random());
		
	    field = document.createElement('input');	  	
	   	
	    if(codigo_tipo == 1){
	   		field.setAttribute('name', 'data[TEeveEstatisticaEvento][codigo_evento]');
	    }else if(codigo_tipo == 2){
	    	field.setAttribute('name', 'data[TEeveEstatisticaEvento][codigo_estacao]');
	    }
	    field.setAttribute('value', tipo_agrupamento);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field); 

	    field = document.createElement('input');
	   	field.setAttribute('name', 'data[TEeveEstatisticaEvento][status_evento]');
	    field.setAttribute('value', status_evento);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field); 	    

    	field = document.createElement('input');
	   	field.setAttribute('name', 'data[TEeveEstatisticaEvento][status_evento_gerado]');
	    field.setAttribute('value', status_evento_gerado);
	    field.setAttribute('type', 'hidden');
    	form.appendChild(field); 
	    
	    field = document.createElement('input');
	   	field.setAttribute('name', 'data[TEeveEstatisticaEvento][status_sla]');
	    field.setAttribute('value', status_sla);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field); 

	    field = document.createElement('input');
		field.setAttribute('name', 'data[TEeveEstatisticaEvento][hora]');
	    field.setAttribute('value', hora);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field); 


	    field = document.createElement('input');
		field.setAttribute('name', 'data[TEeveEstatisticaEvento][codigo_embarcador]');
	    field.setAttribute('value','{$this->data['TEeveEstatisticaEvento']['codigo_embarcador']}');
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field); 

	    field = document.createElement('input');
		field.setAttribute('name', 'data[TEeveEstatisticaEvento][data]');
	    field.setAttribute('value', '{$this->data['TEeveEstatisticaEvento']['data']}');
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field); 

	    field = document.createElement('input');
		field.setAttribute('name', 'data[TEeveEstatisticaEvento][codigo_transportador]');
	    field.setAttribute('value', '{$this->data['TEeveEstatisticaEvento']['codigo_transportador']}');
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field); 

	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();

	}"
);
?>