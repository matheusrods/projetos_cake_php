<?php if (!isset($itens)): ?>
	<div class='well'>
		<?php echo $this->Form->create('Notaite', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_notas_fiscais', 'action' => 'por_produto'))) ?>
		<div class="row-fluid inline">
                    <?php echo $this->BForm->input('mes', array('options' => $meses, 'class' => 'input-small', 'label' => false, 'default' => date('m'))); ?>
                    <?php echo $this->BForm->input('ano', array('options' => $anos, 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
                    <?php echo $this->BForm->input('grupo_empresa', array('legend' => false, 'class' => 'input-small', 'options' => $grupos_empresas, 'type' => 'radio', 'label' => array('class' => 'radio inline'))) ?>
                    <?php echo $this->BForm->input('empresa', array('label' => false, 'placeholder' => 'Empresa', 'class' => 'input-large', 'options' => $empresas, 'empty' => 'Todas empresas')) ?>
                    <?php echo $this->BForm->input('codigo_cliente', array('label' => false, 'placeholder' => 'Código Cliente', 'class' => 'input-small', 'type' => 'text')) ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $this->BForm->end();?>
	</div>
	<?php $this->addScript($this->Javascript->codeBlock('setup_datepicker()')); ?>
	<?php $this->addScript($this->Buonny->link_js('itens_notas_fiscais')) ?>
<?php else: ?>
	<?php $codigo_empresa = isset($empresa['LojaNaveg']['codigo']) ? $empresa['LojaNaveg']['codigo'] : ''; ?>
        <?php if (isset($tipo_ranking) && isset($nome)): ?>
            <div class='well'>
                <strong><?php echo ucfirst(Inflector::singularize($tipo_ranking));?>: </strong><?php echo $nome; ?>
            </div>
        <?php else:?>
            <div class='well'>
                <strong>Grupo: </strong><?php echo $nome_grupo; ?>
                <?php if (isset($empresa) && !empty($empresa)): ?>
                    <strong>Empresa: </strong><?php echo $empresa['LojaNaveg']['razaosocia']; ?>
                <?php endif ?>
                <?php if (isset($cliente)): ?>
                    <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
                    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
                <?php endif ?>
                <?php if (isset($gestor)): ?>
                    <strong>Gestor: </strong><?php echo $this->Html->tag('span', $gestor['Gestor']['nome']); ?>
                <?php endif ?>
                <?php if (isset($corretora)): ?>
                	<strong>Código: </strong><?php echo $this->Html->tag('span', $corretora['Cliente']['codigo']); ?>
                    <strong>Corretora: </strong><?php echo $this->Html->tag('span', $corretora['Gestor']['nome']); ?>
                <?php endif ?>
                <?php if (isset($seguradora)): ?>
                	<strong>Código: </strong><?php echo $this->Html->tag('span', $seguradora['Seguradora']['codigo']); ?>
                    <strong>Seguradora: </strong><?php echo $this->Html->tag('span', $seguradora['Seguradora']['nome']); ?>
                <?php endif ?>
                <strong>Período de: </strong><?php echo $this->data['Notaite']['data_inicial']; ?><strong> até: </strong><?php echo $this->data['Notaite']['data_final']; ?>
            </div>	
        <?php endif; ?>
	<table class='table table-striped table-bordered'>
		<thead>
			<th>Produto</ht>
			<th class='numeric'>Total(R$)</ht>
			<th class='numeric'>Posição</ht>
			<th class='numeric'>Participação(%)</ht>
			<th class='numeric'>Acumulado(%)</ht>			
			<th class='action-icon'></th>			
			<th class='action-icon'></th>			
		</thead>
		<?php $total = 0 ?>
		<?php $acumulado = 0 ?>
		<?php foreach ($itens as $item): ?>
			<?php $acumulado += round($item['0']['participacao'],4) ?>
			<tr>
				<td><?= $item['NProduto']['descricao'] ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($item['0']['total']) ?></td>
				<td class='numeric'><?= $item['0']['registro'] ?></td>
				<td class='numeric'><?=  number_format(round($item['0']['participacao'],4), 4, ',', '.') ?></td>
				<td class='numeric'><?=  number_format(round($acumulado,4), 4, ',', '.') ?></td>				
				<td class='action-icon'>
					<?php 
						$codigo_cliente = isset($this->data['Notaite']['codigo_cliente']) ? $this->data['Notaite']['codigo_cliente'] : null;
						$codigo_gestor = isset($this->data['Notaite']['codigo_gestor']) ? $this->data['Notaite']['codigo_gestor'] : null;
						$codigo_seguradora = isset($this->data['Notaite']['codigo_seguradora']) ? $this->data['Notaite']['codigo_seguradora'] : null;
						$codigo_corretora = isset($this->data['Notaite']['codigo_corretora']) ? $this->data['Notaite']['codigo_corretora'] : null;
					?>
					<?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "comparativo_anual('".substr($this->data['Notaite']['data_final'],6,4)."', '{$this->data['Notaite']['grupo_empresa']}', '{$codigo_empresa}','{$item['Notaite']['produto']}','{$codigo_cliente}', '{$codigo_gestor}', '{$codigo_seguradora}', '{$codigo_corretora}')", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?>
				</td>
				<td class="action-icon"><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "clientes_por_produto('".substr($this->data['Notaite']['data_inicial'],3,2)."', '".substr($this->data['Notaite']['data_final'], 6,4)."', '{$this->data['Notaite']['grupo_empresa']}', '{$this->data['Notaite']['empresa']}', '{$codigo_cliente}', '{$item['Notaite']['produto']}','{$item['NProduto']['descricao']}')", 'class' => 'icon-list-alt', 'title' => 'Clientes por Produto')) ?></td>			
			</tr>
			<?php $total += $item['0']['total']; ?>
		<?php endforeach ?>
		<tfoot>
			<td>Total</td>
			<td class='numeric'><?= $this->Buonny->moeda($total) ?></td>
			<td></td>
			<td></td>
			<td></td>			
			<td></td>			
			<td></td>			
		</tfoot>
	</table>
<?php endif; ?>
<?= $this->Javascript->codeBlock("
	function comparativo_anual(ano, grupo_empresa, empresa, codigo_produto, codigo_cliente, codigo_gestor, codigo_seguradora, codigo_corretora) {
		var field = null;
		var form = document.createElement(\"form\");
		form.setAttribute(\"method\", \"post\");
		form.setAttribute(\"target\", \"formresult\");
        form.setAttribute(\"action\", \"/portal/itens_notas_fiscais/comparativo_anual/1\");
        form.setAttribute(\"id\", \"form_comparativo\");
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][ano]\");
        field.setAttribute(\"value\", ano);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][grupo_empresa]\");
        field.setAttribute(\"value\", grupo_empresa);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][empresa]\");
        field.setAttribute(\"value\", empresa);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][codigo_produto]\");
        field.setAttribute(\"value\", codigo_produto);
        form.appendChild(field);
        if (codigo_cliente != '') {
        	field = document.createElement(\"input\");
	        field.setAttribute(\"name\", \"data[Notaite][codigo_cliente]\");
	        field.setAttribute(\"value\", codigo_cliente);
	        form.appendChild(field);
    	}
		if (codigo_gestor != '') {
			field = document.createElement(\"input\");
			field.setAttribute(\"name\", \"data[Notaite][codigo_gestor]\");
	        field.setAttribute(\"value\", codigo_gestor);
	        form.appendChild(field);
	    }
	    if (codigo_corretora != '') {
	    	field = document.createElement(\"input\");
			field.setAttribute(\"name\", \"data[Notaite][codigo_corretora]\");
	        field.setAttribute(\"value\", codigo_corretora);
	        form.appendChild(field);
	    }
	    if (codigo_seguradora != '') {
	    	field = document.createElement(\"input\");
			field.setAttribute(\"name\", \"data[Notaite][codigo_seguradora]\");
	        field.setAttribute(\"value\", codigo_seguradora);
	        form.appendChild(field);
	    }
        document.body.appendChild(form);
        document.getElementById('form_comparativo').style.display = 'none';        
        var janela = window_sizes();
        window.open('', 'formresult', 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
        form.submit();
        $(form).remove();
	}


	function clientes_por_produto( mes, ano, grupo_empresa, empresa, cliente, produto, descricao) {

		jQuery.post('/portal/notas_fiscais/ranking_faturamento_listagem/',
			{ 				
                'data[Notafis][mes]'            : mes,
                'data[Notafis][ano]'            : ano,
                'data[Notafis][grupo_empresa]'  : grupo_empresa,
                'data[Notafis][empresa]'        : empresa,
				'data[Notafis][codigo_cliente]' : cliente,
				'data[Notafis][data_inicial]'   : mes,
				'data[Notafis][data_final]'     : ano,
				'data[Notafis][produtos]'       : produto,
				'data[Notafis][gestores]'       : '',
				'data[Notafis][corretoras]'	    : '',
                'data[Notafis][seguradoras]'    : '',
                'data[Notafis][nome]'    		: descricao
			}, function( data ) {
				var newwindow = window.open('/portal/notas_fiscais/ranking_faturamento_listagem/janela_produtos','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');
			}
		);
	}


	"
	);
?>