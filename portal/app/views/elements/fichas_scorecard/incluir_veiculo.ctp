<div class='dados-veiculo'>
	<legend>Veículo</legend>
	<div class="actionbar-right">
		<?= $this->BForm->button('Limpar', array('div' => false, 'class' => 'btn btn-info btn-limpar', 'type'=>'button')); ?>
	</div>
	<div id='veiculo-data-0'>
		<div id="possui_veiculo_0" class="pergunta-possui-veiculo">	
			<h4>Possui Veículo ?</h4>
			<?php echo $this->BForm->input("FichaScorecardVeiculo.possui_veiculo", array('type' => 'radio', 'level' => 0, 'options' => array('S' => 'Sim','N' => 'Não'), 'legend' => false,'label' => array('class' =>'radio inline input-small') )) ?>
		</div>
	    <div class='veiculo-content-0'>
			<?php echo $this->element('fichas_scorecard/incluir_linha_veiculo', array('index'=>0, 'cidades'=>$cidades_veiculo[0], 'modelos'=>$modelos_veiculo[0], 'tipo'=>'veiculo', 'veiculo_descricao'=>'do veiculo'))?>
			<?php echo $this->element('fichas_scorecard/incluir_linha_proprietario', array('index'=>0, 'proprietario_enderecos'=>$proprietario_enderecos[0], 'veiculo_descricao'=>'do veiculo', 'disabled'=> $disabled))?>
			<div id='veiculo-data-1'>
				<div id="possui_veiculo_1" class="row-fluid inline" >
					<h4>Possui carreta?</h4>
					<?php echo $this->BForm->input("FichaScorecardVeiculo.1.Veiculo.veiculo_sn", array('type' => 'radio', 'level' => 1, 'options' => array('S' => 'Sim','N' => 'Não'), 'legend' => false,'label' => array('class' =>'radio inline input-small') )) ?>
				</div>
			    <div class='veiculo-content-1'>
					<?php echo $this->element('fichas_scorecard/incluir_linha_veiculo', array('index'=>1, 'cidades'=>$cidades_veiculo[1], 'modelos'=>$modelos_veiculo[1], 'tipo'=>'carreta', 'veiculo_descricao'=>'da carreta'))?>
					<?php echo $this->element('fichas_scorecard/incluir_linha_proprietario', array('index'=>1, 'proprietario_enderecos'=>$proprietario_enderecos[1], 'veiculo_descricao'=>'da carreta', 'disabled'=> $disabled))?>
					<div id='veiculo-data-2'>
						<div id="possui_veiculo_2" class="row-fluid inline" >
							<h4>Possui bitrem?</h4>
							<?php echo $this->BForm->input("FichaScorecardVeiculo.2.Veiculo.veiculo_sn", array('type' => 'radio', 'level' => 2, 'options' => array('S' => 'Sim','N' => 'Não'), 'legend' => false,'label' => array('class' =>'radio inline input-small') )) ?>
						</div>
					    <div class='veiculo-content-2'>
							<?php echo $this->element('fichas_scorecard/incluir_linha_veiculo', array('index'=>2, 'cidades'=>$cidades_veiculo[2], 'modelos'=>$modelos_veiculo[2], 'tipo'=>'bitrem', 'veiculo_descricao'=>'do bitrem'))?>
							<?php echo $this->element('fichas_scorecard/incluir_linha_proprietario', array('index'=>2, 'proprietario_enderecos'=>$proprietario_enderecos[2], 'veiculo_descricao'=>'do bitrem', 'disabled'=> $disabled ))?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
$(document).ready(function() {
	$(".dados-veiculo :button").click(function(){
		$(".dados-veiculo :input").not(":button, :submit, :reset, :hidden, :radio").val("");
		$(".dados-veiculo").find(".formata-cep").trigger("blur");
	});
});', false);?>