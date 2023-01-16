<?php echo $this->BForm->create('Cliente', array('action' => 'visualizar', $this->passedArgs[0])); ?>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#gerais" data-toggle="tab">Dados Gerais</a></li>
		<li><a href="#outros-enderecos" data-toggle="tab">Outros Endereços</a></li>
		<li><a href="#atendimento" data-toggle="tab">Atendimento</a></li>
		<li><a href="#categorizacao" data-toggle="tab">Categorização</a></li>
		<li><a href="#contatos" data-toggle="tab">Contatos</a></li>
		<li><a href="#faturamento" data-toggle="tab">Faturamento</a></li>
		<li><a href="#historico" data-toggle="tab">Histórico</a></li>
		<li><a href="#ContratosProdutosCliente" data-toggle="tab">Contratos</a></li>
		<li><a href="#pesquisa_satisfacao" data-toggle="tab" class='pesquisa_satisfacao'>Pesquisa Satisfação</a></li>
	</ul>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('razao_social', array('class' => 'input-xxlarge', 'label' => 'Razão Social')); ?>
		<?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'label' => 'Nome Fantasia')); ?>
	</div>
	<div class="tab-content">
		<div class="tab-pane active" id="gerais">
			<?php echo $this->element('clientes/dados_gerais', array('edit_mode' => true)) ?>
		</div>
		<div class="tab-pane" id="outros-enderecos">
			<?php echo $this->element('clientes/outros_enderecos_financeiro') ?>
		</div>
		<div class="tab-pane" id="atendimento">
			<?php echo $this->element('clientes/atendimento', array('edit_mode' => true)) ?>
		</div>
		<div class="tab-pane" id="categorizacao">
			<?php echo $this->element('clientes/categorizacao') ?>
		</div>
		<div class="tab-pane" id="contatos">
			<?php echo $this->element('clientes/contatos_visualizar') ?>
		</div>
		<div class="tab-pane" id="faturamento">
			<?php echo $this->element('clientes/faturamento', array('edit_mode' => true)) ?>
		</div>
		<div class="tab-pane" id="historico">
			<?php echo $this->element('clientes/historico_visualizar') ?>
		</div>
		<div class="tab-pane" id="ContratosProdutosCliente" style='min-height:50px'>
		   <div class='lista'></div>
		   <?php echo $this->Javascript->codeBlock("
				jQuery(document).ready(function(){
					var div = jQuery('div.lista');
					bloquearDiv(div);
					div.load(baseUrl + 'clientes_produtos_contratos/visualiza_cliente/' + {$clientes['Cliente']['codigo']} + '/' + Math.random());
				})
			"); 
		  ?>
		</div>
		<div class="tab-pane" id="pesquisa_satisfacao" style="min-height:50px">&nbsp;</div>
	</div>
	<div class="form-actions">
		<?= $html->link('Voltar', array('action' => 'visualizar_clientes'), array('class' => 'btn')); ?>
	</div>	
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("window.vizualizar = true;"); ?>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>
<?php $data_atual = date('d/m/Y') ?>
<?php echo $javascript->codeblock("jQuery(document).ready(function(){
	setup_mascaras();
	setup_datepicker();
	jQuery('#ClienteVisualizarForm input').attr('disabled', true);
	jQuery('#ClienteVisualizarForm select').attr('disabled', true);
	$('.pesquisa_satisfacao').click(function(){					
		if( $('#pesquisa_satisfacao').html() == '&nbsp;' ) {
			$.ajax({
				type: 'POST',		
				url: '/portal/pesquisas_satisfacao/pre_listagem_pesquisa_satisfacao_analitico/'+Math.random(),		
				dataType: 'html',
				data:{
				  'data[PesquisaSatisfacao][codigo_cliente]' : '{$this->data['Cliente']['codigo']}',
				  'data[PesquisaSatisfacao][data_inicial]' : '01/01/2014',
				  'data[PesquisaSatisfacao][data_final]' : '{$data_atual}',
				  'data[PesquisaSatisfacao][codigo_usuario_pesquisa]' : '',
				  'data[PesquisaSatisfacao][codigo_produto]' : '',
				  'data[PesquisaSatisfacao][codigo_status_pesquisa]' : '',
				  'data[PesquisaSatisfacao][status_pesquisa]' : ''
				},				
				beforeSend: function(){
	                bloquearDiv($('#pesquisa_satisfacao'));
	            },
				success: function(data) {   
					if ( data != null ) {
					  $('#pesquisa_satisfacao').html(data);
					}
				  },
				error: function(data) {
					alert('Oooops! Ocorreu algum problema, tente novamente.');
				}
			})
		}
	});
});"); ?>