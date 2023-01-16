<div class='row'>
	<?php echo $this->BForm->create('Recebsm', array('url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'incluir', $remonta,rand() )));?>
	<div class="span3 bs-docs-sidebar">
		<ul class="nav nav-list bs-docs-sidenav">
			
			<li><a href="#placas"><i class="icon-chevron-right"></i> Veiculos</a></li>
			<li><a href="#viagem"><i class="icon-chevron-right"></i> Dados da Viagem</a></li>
			<li><a href="#rota"><i class="icon-chevron-right"></i> Rota</a></li>
			<li><a href="#origem"><i class="icon-chevron-right"></i> Origem</a></li>
			<li><a href="#destino"><i class="icon-chevron-right"></i> Itinerario</a></li>
			<li><a href="#escolta"><i class="icon-chevron-right"></i> Escolta</a></li>
			<li><a href="#iscas"><i class="icon-chevron-right"></i> Equipamentos Extras</a></li>
			<li><a href="#observacao"><i class="icon-chevron-right"></i> Observação</a></li>
			
			<li class="nav-header"></li>
			<li>
				<div class="btn-group">
				<?= $this->BForm->submit('Gerar SM', array('div' => false, 'class' => 'btn btn-success', 'id' => 'GerarSm', 'name' => 'data[Acao][tipo]')); ?>
				<?= $this->BForm->button('Cancelar Geração', array('div' => false, 'class' => 'btn sm-cancelar', 'name' => 'data[Acao][tipo]')); ?>
				</div>
			</li>

			<li class="nav-header"></li>
			<li>
				<div>
					<?php echo $html->link('<i class="icon-plus icon-white"></i> Salvar Modelo', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-primary add-modelo')) ?>
				</div>
			</li>
			<li class="nav-header"></li>
			<li>
				<div id="modelo" style="display:none" >
					<div id="modelo-erro" style="display:none" class="help-block alert-error form-actions well">Erro!</div>
					<div id="modelo-sucesso" style="display:none" class="help-block alert-success form-actions well">Sucesso!</div>

					<div id="modelo-form">
						<?php echo $this->BForm->input('TMviaModeloViagem.mvia_descricao',array('label' => false, 'placeholder' => 'Descrição', 'maxlength' => 20, 'class' => 'input-medium')); ?>
						<?php echo $this->Html->link('Salvar','javascript:void(0)',array('class' => 'btn btn-success', 'id' => 'salvar-modelo')); ?>&nbsp;
						<?php echo $this->Html->link('Cancelar','javascript:void(0)',array('class' => 'btn', 'id' => 'cancelar-modelo')); ?>
					</div>
				</div>
			</li>
		</ul>
	</div>
	<div class='span9'>
		<?php if($menssagem): ?>
		<section class="form-actions alert-error veiculo-error" >
			<h5>Erros:</h5>
			<?php echo $menssagem ?>
		</section>
		<?php endif; ?>
		<h5 style='margin-top:30px'><?= $this->Html->link('Dados Principais', 'javascript:void(0)', array('id' => 'dado', 'class' => 'link-hide-show dados')) ?> <i class="icon-arrow-left"></i> <span style="font-size:12px;font-weight:normal;">(clique aqui para mais informações)</span>
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
				</div>
			</div>	
			
		<section id='placas' style='margin-top:-90px'>
			<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_placas') ?>
		</section>
		

		<div id='carga-modelo'>
			<section id='viagem'>
				<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_viagem') ?>
			</section>
			<section id='rota'>
				<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_rota') ?>
			</section>
			<div id="itinerario">
				<section id='origem'>
					<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_origem') ?>
				</section>
				<section id='destino'>
					<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_destino') ?>
				</section>
			</div>
		</div>		
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
	<?php echo $this->BForm->hidden('Recebsm.rota_sm') ?>
	<?php echo $this->BForm->hidden('TMviaModeloViagem.mvia_codigo') ?>
	<?php echo $this->BForm->hidden('codigo_alvos_emb') ?>
	<?php echo $this->BForm->hidden('codigo_alvos_tra') ?>
	<?php echo $this->BForm->hidden('vppj_bloquear_sem_rota', array('value' => isset($vppj_bloquear_sem_rota) ? $vppj_bloquear_sem_rota : null)) ?>
	<?php echo $this->BForm->end(); ?>
	<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
	<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
	
</div>
<?php echo $this->Buonny->link_js('sm_online'); ?>
<?php echo $this->Javascript->codeBlock('
	var codigo_cliente_emb = "'.$this->data['Recebsm']['codigo_alvos_emb'].'";
	var codigo_cliente_tra = "'.$this->data['Recebsm']['codigo_alvos_tra'].'";

	jQuery(document).ready(function() {
		autocomplete_escolta("RecebsmEscolta");
		$("#dado").click(function(event){
           	jQuery("div#dados").slideToggle("slow");

        })

		$(".sm-cancelar").click(function(){
			if(confirm("Deseja realmente sair da inclusão de Solicitação de Monitoramento?")){
				$("form#RecebsmIncluirForm").submit();
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

<?php 
	if(!isset($this->data['Acao']['tipo'])){
		echo $this->Javascript->codeBlock('
		jQuery(document).ready(function() {
			var codigo_modelo = $("#TMviaModeloViagemMviaCodigo").val();
			var codigo_cliente = '.(!empty($this->data['Recebsm']['codigo_cliente']) ? $this->data['Recebsm']['codigo_cliente'] : NULL).';
			if(codigo_modelo)
				carregar_sm_itinerario(codigo_modelo,"#carga-modelo", '.($remonta ? 1 : 0).',codigo_cliente);
		})');
	}
?>

<?php
	if(isset($confirmar_sem_pgr) && isset($this->data['viag_ignorou_pgr']) && $this->data['viag_ignorou_pgr'] && !empty($menssagem)){
		echo $this->Javascript->codeBlock('
			jQuery(document).ready(function(){
				var erros = "'.str_replace('<BR>', '\n', $menssagem).'";
				if(confirm("O PGR encontrou os seguintes erros:\n\n"+erros+"\nContinuar mesmo assim?")){
					$("#RecebsmIncluirForm").append("<input type=\"hidden\" name=\"data[Recebsm][incluir_sem_pgr]\" value=\"1\">");
					$("#GerarSm").click();
				}
			})
		');
	}
?>