<?php if(!empty($relatorio)):?>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <br/>
    <table class="table table-striped table-bordered">
        <thead>
            <tr> 
                <th class="input-xxlarge">Estação</th>
                <th class="input-xlarge numeric">Sm agendada</th>
                <th class="input-xlarge numeric">Em viagem</th>
                <th class="input-medium numeric">Operadores</th>
                <th class=""></th>
                <th class="input-xxlarge">Estação</th>
                <th class="input-xlarge numeric">Sm agendada</th>
                <th class="input-xlarge numeric">Em viagem</th>
                <th class="input-medium numeric">Operadores</th>
                <th class=""></th>
            </tr>
        </thead>
        <tbody>
            <?php $total_operador = 0;?>
            <?php $total_sm = 0;?>
            <?php foreach ($relatorio as $key => $relatorio): ?>
            	<?$eras_codigo = $relatorio['0']['eras_codigo'];?>
				<?php $total_operador += $relatorio[0]['operadores'];?>
            	<?php $total_sm += $relatorio[0]['agendadas'];?>
	            <?php if ($key%2 == 0): ?>
	            	<tr>
	            <?php endif ?>
	                <td><?php echo $relatorio[0]['eras_descricao'];?></td>
					<td class='numeric'>
						<?=$this->Html->link($this->Buonny->moeda( $relatorio[0]['agendadas'], array('nozero' => true, 'places' => 0)), "javascript:consulta_geral_sm( '{$eras_codigo}', 3 )") ?>
					</td>
					<td class='numeric'>
						<?=$this->Html->link($this->Buonny->moeda( $relatorio[0]['em_viagem'], array('nozero' => true, 'places' => 0)), "javascript:consulta_geral_sm( '{$eras_codigo}', 4 )") ?>
					</td>				
					<td class='numeric'>
						<?=$this->Html->link($this->Buonny->moeda( $relatorio[0]['operadores'], array('nozero' => true, 'places' => 0)), "javascript:viagens_operadores( '{$eras_codigo}' )") ?>
					</td>
					<td class='input-'>
					<?if( $relatorio[0]['operadores'] > 0): ?>
						<span title="Operador Logado" class="badge-empty badge badge-success"></span>
					<?else:?>
					<span title="Operador Deslogado" class="badge-empty badge badge-important"></span>
					<?endif;?>
					</td>
				<?php if ($key%2 != 0): ?>
	            	</tr>
	            <?php endif ?>
        	<?php endforeach ?>
        	<?php if ($key%2 == 0): ?>
        			<td></td>
        			<td></td>
        			<td></td>
        			<td></td>
        			<td></td>
        		</tr>
        	<?php endif ?>
    	</tbody>
    </table>
<?php $status_viagem = isset($this->data['RelatorioSm']['codigo_status_viagem']) ? $this->data['RelatorioSm']['codigo_status_viagem'] : NULL ;?>
<?php echo $this->Javascript->codeBlock("
	function consulta_geral_sm( codigo_estacao, click ) {
 		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');

	    var estacao_em_viagem = {$em_viagem};
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/relatorios_sm/listagem_consulta_geral_sm/' + Math.random());
	  	field = document.createElement('input');
	    field.setAttribute('name', 'data[RelatorioSmConsulta][eras_codigo]');
		field.setAttribute('value', codigo_estacao);
	    field.setAttribute('type', 'hidden');	    
	    form.appendChild(field);


		if( click == 3 ) {
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_status_viagem][]');
			field.setAttribute('value', 3);
		    field.setAttribute('type', 'hidden');	    
		    form.appendChild(field);
		} else {
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_status_viagem][]');
			field.setAttribute('value', 4);
		    field.setAttribute('type', 'hidden');	    
		    form.appendChild(field);

		    field = document.createElement('input');
		    field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_status_viagem][]');
			field.setAttribute('value', 5);
		    field.setAttribute('type', 'hidden');	    
		    form.appendChild(field);

		    field = document.createElement('input');
		    field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_status_viagem][]');
			field.setAttribute('value', 6);
		    field.setAttribute('type', 'hidden');	    
		    form.appendChild(field);
		}

	    if( estacao_em_viagem == 1 ){
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[RelatorioSmConsulta][data_inicial]');
			field.setAttribute('value', '{$data_inicio}');
		    field.setAttribute('type', 'hidden');	    
		    form.appendChild(field);
		    
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[RelatorioSmConsulta][data_final]');
			field.setAttribute('value', '{$data_fim}');
		    field.setAttribute('type', 'hidden');	    
		    form.appendChild(field);
	    }

	    field = document.createElement('input');
	    field.setAttribute('name', 'data[RelatorioSmConsulta][tipo_view]');
		field.setAttribute('value', 'popup');
	    field.setAttribute('type', 'hidden');	    
	    form.appendChild(field);
	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();
	}

	function viagens_operadores( codigo_estacao ) {
 		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/operadores/viagens_operadores/1/' + Math.random());
	  	field = document.createElement('input');
	    field.setAttribute('name', 'data[TVusuViagemUsuario][eras_codigo]');
		field.setAttribute('value', codigo_estacao);
	    field.setAttribute('type', 'hidden');	    
	    form.appendChild(field);

	    field = document.createElement('input');
	    field.setAttribute('name', 'data[TVusuViagemUsuario][usua_status]');
		field.setAttribute('value', 1);
	    field.setAttribute('type', 'hidden');	    
	    form.appendChild(field);

	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();
	}
	"        
    );?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>