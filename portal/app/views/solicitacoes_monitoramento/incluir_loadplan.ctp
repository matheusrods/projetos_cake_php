<div class='row'>
	<?php echo $this->BForm->create('Recebsm', array('url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'incluir_loadplan',rand())));?>
	<div class="span3 bs-docs-sidebar">
		<ul class="nav nav-list bs-docs-sidenav">
			
			<li>
				<div>
					<?php echo $this->BForm->input('LogIntegracao.codigo',array('label' => false, 'placeholder' => 'Loadplan', 'maxlength' => 20, 'class' => 'input-medium', 'type' => 'text')); ?>
					<div class="btn-group">
						<?php echo $this->Html->link('Carregar','javascript:void(0)',array('class' => 'btn btn-primary', 'id' => 'carregar-loadplan')); ?>
						<?php echo $this->Html->link('+ Parada','javascript:void(0)',array('class' => 'btn', 'id' => 'carregar-parada')); ?>
					</div>
				</div>
			</li>
			<li class="nav-header"></li>
			<li class="nav-header"></li>
			<li>
				<div class="btn-group">
				<?= $this->BForm->submit('Gerar SM', array('div' => false, 'class' => 'btn btn-success', 'name' => 'data[Acao][tipo]')); ?>
				<?= $this->BForm->button('Cancelar Geração', array('div' => false, 'class' => 'btn sm-cancelar', 'name' => 'data[Acao][tipo]')); ?>
				</div>
			</li>
			<li class="nav-header"></li>
			<li class="nav-header"></li>
			<li class="well">
				<p>Para devolução ou importação sem o número do Loadplan clique em uma das opções abaixo: </p>
				<p><?php echo $this->Html->link('SM Devolução',array('controller' => 'solicitacoes_monitoramento','action' => 'incluir_sm_tipo_transporte',$ttra_devolucao,rand()),array('class' => 'sm-padrao')); ?></p>				
				<p><?php echo $this->Html->link('SM Importação',array('controller' => 'solicitacoes_monitoramento','action' => 'incluir_sm_tipo_transporte',$ttra_importacao,rand()),array('class' => 'sm-padrao')); ?></p>			</li>
		</ul>
	</div>
	<div class='span9'>
		<?php if($menssagem): ?>
		<section class="form-actions alert-error veiculo-error" >
			<h5>Erros:</h5>
			<?php echo $menssagem ?>
		</section>
		<?php endif; ?>
		<h5 style='margin-top:30px'><?= $this->Html->link('Dados Principais', 'javascript:void(0)', array('id' => 'dado', 'class' => 'link-hide-show dados')) ?>
		</h5>
			<div id ='dados' style = 'display:none' >
				<div id ='titulo'style='margin-top:-90px' >
					<section id='cliente_produto'>
						<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_cliente_produto') ?>
					</section>
					<section id='embatran'>
						<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_embarcador_transportador') ?>
					</section>
					<section id='motorista'>
						<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_motorista') ?>
					</section>
					<section id='gerenciadora'>
						<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_gerenciadora') ?>
					</section>
					<section id='placas'>
						<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_placas') ?>
					</section>
				</div>
			</div>	
		<section id='loadplan' style='margin-top:-90px'>
			<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_loadplan') ?>
		</section>
		<section id='escolta'>
			<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_escolta') ?>
		</section>
		<section id='iscas'>
			<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_iscas') ?>
		</section>
		<section id='observacao'>
			<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_observacao') ?>
		</section>
	</div>
	<?php echo $this->BForm->hidden('Recebsm.pedido_cliente') ?>
	<?php echo $this->BForm->hidden('Recebsm.monitorar_retorno',array('value' => 0)) ?>
	<?php echo $this->BForm->hidden('Recebsm.dta_fim') ?>
	<?php echo $this->BForm->hidden('Recebsm.hora_fim') ?>
	<?php echo $this->BForm->hidden('Recebsm.temperatura') ?>
	<?php echo $this->BForm->hidden('Recebsm.temperatura2') ?>
	<?php echo $this->BForm->hidden('temperatura',array('value' => 0)) ?>
	
	<?php echo $this->BForm->hidden('codigo_alvos_emb') ?>
	<?php echo $this->BForm->hidden('codigo_alvos_tra') ?>
	<?php echo $this->BForm->end(); ?>
	<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
	<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
	
</div>
<?php echo $this->Buonny->link_js('sm_online'); ?>
<?php echo $this->Javascript->codeBlock('
	var codigo_cliente_emb = "'.$this->data['Recebsm']['codigo_alvos_emb'].'";
	var codigo_cliente_tra = "'.$this->data['Recebsm']['codigo_alvos_tra'].'";

	jQuery(document).ready(function() {
		setup_datepicker();
		setup_time();
		setup_mascaras();
		$.placeholder.shim();

		autocomplete_escolta("RecebsmEscolta");
		$("#dado").click(function(event){
           	jQuery("div#dados").slideToggle("slow");

        })
		
		$(".sm-padrao").click(function(){
			if(!confirm("Deseja continuar o lançamento da SM?"))
				return false;
		});

		$(".sm-cancelar").click(function(){
			if(confirm("Deseja realmente sair da inclusão de Solicitação de Monitoramento?")){
				$("form#RecebsmIncluirLoadplanForm").submit();
			}

			return false;
		});

		$(document).on("keydown","table.destino input",function(e){

			if($(this).attr("maxlength") != "undefined" && 
			   e.which != 8 && 
			   e.which != 9 &&
			   e.which != 20 &&
			   e.which != 18 &&
			   e.which != 46 &&
			   e.which != 17){

				if($(this).val().length >= $(this).attr("maxlength")){
					alert("Numero máximo de caracteres excedido, para acrescentar informações, adicione um novo registro clicando no \"+\"");
					return false;
				}
			}

		});
	})'
) ?>
