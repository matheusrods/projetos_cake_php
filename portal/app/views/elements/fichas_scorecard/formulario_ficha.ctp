<?php $codigo_ficha  	= empty($codigo_ficha) ? '' : $codigo_ficha; ?>
<?php $ficha_em_edicao 	= !empty($this->data['Profissional']['codigo_documento']); ?>
<?php $mostra_ficha 	= ($ficha_em_edicao ? '' : 'display:none') ?>
<?php $disabled 		= (!empty($codigo_ficha));?>
<div  class='row'>
	<?php echo $this->BForm->create('FichaScorecard', array('url' => array('controller' => 'fichas_scorecard', 'action' => $this->action, $codigo_ficha)));?>
	<div class="span3 bs-docs-sidebar">
		<ul class="nav nav-list bs-docs-sidenav">	 
			<li><a href="#cliente" class="cliente-erro"><i class="icon-chevron-right"></i> Cliente</a></li>
			<li><a href="#profissional" class="profissional-erro"><i class="icon-chevron-right"></i> Profissional</a></li>
			<li><a href="#veiculo" class="veiculo-erro"><i class="icon-chevron-right"></i> Veículo</a></li>
			<li><a href="#carga" class="carga-erro"><i class="icon-chevron-right"></i> Carga</a></li>
			<li><a href="#complementares" class="complementares-erro"><i class="icon-chevron-right"></i> Dados Complementares</a></li>	  
			<li class="nav-header"></li>
			<?if(empty($this->data['FichaScorecard']['codigo'])):?>
			<li>
			<div id='btn_salvar' class="btn-group dados-ficha" style='<?= $mostra_ficha ?>'>
				<?= $this->BForm->submit('Salvar Ficha', array('div' => false,'id'=>'BotaoSalvar' ,'class' => 'btn btn-success')); ?>
				<?= $html->link('Cancelar', array('action' => 'incluir'), array('class' => 'btn')); ?>
			</div>
			</li>
			<?endif;?>
		</ul>
  	</div>
  	<div class='span9'>
		<?php if(!empty($menssagem)): ?>
		<section class="form-actions alert-error veiculo-error" >
			<h5>Erros:</h5>
			<?php echo $menssagem ?>
		</section>
		<?php endif; ?>
		<section id='cliente' >
			<?php echo $this->element('fichas_scorecard/incluir_cliente'); ?>
		</section>
		<section id='profissional'>
			<legend>Profissional</legend>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input("FichaScorecard.codigo_profissional_tipo", array('label' => 'Categoria', 'empty' => 'Categoria','class' => 'input-medium', 'options' => $profissional_tipo)) ?>
				<?php if( !$codigo_ficha ): ?>
				<?php echo $this->BForm->input("Profissional.codigo_documento", array('label' => 'CPF', 'class' => 'input-medium cpf cpffichascorecard', 'after' => $html->link('...', "javascript:void(0)", array('id' =>'btn-codigo_documento_profissional','class' => 'btn btn-search-ellipsis', 'title' => 'Buscar dados')) )) ?>
				<?php else: ?>
				<?php echo $this->BForm->input("Profissional.codigo_documento", array('label' => 'CPF')) ; ?>	
				<?php endif; ?>

			</div>  
			<div class="dados-ficha" style='<?= $mostra_ficha ?>'>
				<?php echo $this->element('fichas_scorecard/incluir_profissional', array('disabled'=> $disabled )); ?>
			</div>
		</section>
		<div class="dados-ficha" style='<?= $mostra_ficha ?>'>
			<section id='veiculo'>
			  <?php echo $this->element('fichas_scorecard/incluir_veiculo', array('disabled'=> $disabled )); ?>
			</section>
			<section id='carga'>
			  <?php echo $this->element('fichas_scorecard/incluir_carga'); ?>
			</section>
			<section id='complementares'>
			  <?php echo $this->element('fichas_scorecard/incluir_dados_complementares'); ?>
			</section>
		</div>
		<?php echo $this->BForm->end(); ?>
	</div>
</div>
<?php 
	if( !empty($this->data['FichaScorecardVeiculo'][0]['Veiculo']['veiculo_sn']) && $this->data['FichaScorecardVeiculo'][0]['Veiculo']['veiculo_sn'] == 'N'):
		$forcar = TRUE;
	else:
		$forcar = FALSE;
	endif;
?>
<?php echo $this->Buonny->link_js(array('fichas_scorecard')) ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){		
		bloqueia_campos_ficha();
		setup_codigo_documento_profissional();
		setup_codigo_seguranca();

		'.($ficha_em_edicao ? 'setup_formulario()' : '').'
		if( $("#cliente").find(".form-error").length || $("#cliente").find(".error").length ){
			$(".cliente-erro").addClass("validation-error");
		}
		if( $("#profissional").find(".form-error").length || $("#profissional").find(".error").length ){
			$(".profissional-erro").addClass("validation-error");
		}
		'.($forcar ? '$("#FichaScorecardVeiculoPossuiVeiculoN").prop("checked", true);$(".veiculo-content-0").hide();$(".veiculo-content-1").hide();$(".veiculo-content-2").hide();' : '').'
		if( $("#veiculo").find(".form-error").length  || $("#veiculo").find(".error").length ){
			$(".veiculo-erro").addClass("validation-error");
		}
		if( $("#carga").find(".form-error").length || $("#carga").find(".error").length  ){
			$(".carga-erro").addClass("validation-error");
		}
		if( $("#complementares").find(".form-error").length || $("#complementares").find(".error").length  ){
			$(".complementares-erro").addClass("validation-error");
		}
		$("#FichaScorecardIncluirForm").submit(function(){
			if(!$("#btn_salvar").is(":visible")){
				return false;
			}
		})
	});', false);?>