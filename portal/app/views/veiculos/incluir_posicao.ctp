<?php if(!$this->data || !isset($telemonitorado) || empty($telemonitorado)): ?>
<?php echo $this->BForm->create('TUposUltimaPosicao', array('autocomplete' => 'off', 'url' => array('controller' => 'Veiculos','action' => 'incluir_posicao')));?>
<div class='form-procurar'>	
	<div class='well'>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'TUposUltimaPosicao'); ?>
			<?php echo $this->BForm->input('TUposUltimaPosicao.placa', array('label' => false, 'class' => 'placa-veiculo input-small','placeholder' => 'Placa' , 'name' => "data[TUposUltimaPosicao][placa]")); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'filtrar')) ?>
		<?php echo $this->BForm->end();?>
	</div>
</div>
<?php else: ?>
<?php echo $this->BForm->create('TRefeReferencia', array('autocomplete' => 'off', 'url' => array('controller' => 'Veiculos','action' => 'incluir_posicao',$this->data['TUposUltimaPosicao']['codigo_cliente'])));?>
	<div id="info" style="width:50%;float:left;">
	<div class='row-fluid inline'>		
		<?php echo $this->BForm->input('refe_cep', array('label' => 'CEP', 'type' => 'text','class' => 'input-small formata-cep')) ?>
		<?php echo $this->BForm->input('refe_endereco_empresa_terceiro', array('label' => 'Endereço', 'type' => 'text','class' => 'input-xlarge endereco')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('TUposUltimaPosicao.placa', array('value'=> $this->data['TUposUltimaPosicao']['placa'])); ?>
		<?php echo $this->BForm->hidden('TUposUltimaPosicao.codigo_cliente', array('value'=> $this->data['TUposUltimaPosicao']['codigo_cliente'])); ?>
		<?php echo $this->BForm->hidden('TTermTerminal.term_numero_terminal', array('value'=> $telemonitorado[0]['TTermTerminal']['term_numero_terminal'])); ?>
		<?php echo $this->BForm->hidden('TTermTerminal.term_data_cadastro', array('value'=> $telemonitorado[0]['TTermTerminal']['term_data_cadastro'])); ?>
		<?php echo $this->BForm->hidden('TTermTerminal.oras_codigo', array('value'=> $oras_codigo[0])); ?>
		<?php echo $this->BForm->input('refe_bairro_empresa_terceiro', array('label' => 'Bairro', 'type' => 'text','class' => 'input-medium bairro')) ?>
		<?php echo $this->BForm->input('refe_estado', array('label' => 'Estado', 'type' => 'text','class' => 'input-small estado', 'maxlength' => 2)) ?>
		<?php echo $this->BForm->input('refe_cidade', array('label' => 'Cidade', 'type' => 'text','class' => 'input-medium cidade')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('refe_numero', array('label' => 'Numero', 'type' => 'text','class' => 'input-small')) ?>
	</div>
	<div class='row-fluid inline'>
		
		<?php echo $this->BForm->input('refe_latitude', array('label' => 'Latitude <a title="Utilize o formato decimal. (Ex: -9.000000 )" href="javascript:void(0)" class="icon-question-sign"></a>', 'type' => 'text','class' => 'input-small')) ?>
		<?php echo $this->BForm->input('refe_longitude', array('label' => 'Longitude <a title="Utilize o formato decimal. (Ex: -54.000000 )" href="javascript:void(0)" class="icon-question-sign"></a>', 'type' => 'text','class' => 'input-small')) ?>
		
		<div class="control-group input text">
			<label for="RefeReferenciaLongitude">&nbsp</label>
			<?php echo $html->link('Buscar Lat/Long', 'javascript:void(0)', array('class' => 'btn btn-success bt-send-xy')) ;?>
		</div>	
	</div>
	<div class='row-fluid inline'> 
		<?php echo $this->BForm->input('tmpo_codigo', array('label' => 'Evento','empty' => 'Selecione um evento' ,'options' => $eventos ,'class' => 'input-large')); ?>
	</div>
	</div>
	<div id="canvas_mapa" style="width:48%;height:580px;float:right;background-color:#F5F5F5;"></div>
	<div class="form-actions" style="clear:both;">
		  <?php echo $this->BForm->submit('Incluir Posição', array('div' => false, 'class' => 'btn btn-primary')); ?>
		  <?= $html->link('Cancelar', array('action' => 'incluir_posicao'), array('class' => 'btn')); ?>
	</div>
<?php  ?>


<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){

		$("#TRefeReferenciaRefeLatitude").val("-9.00000");
		$("#TRefeReferenciaRefeLongitude").val("-54.00000");
		ver_mapa("#TRefeReferenciaRefeLatitude","#TRefeReferenciaRefeLongitude");
		$("#TRefeReferenciaRefeLatitude").val("");
		$("#TRefeReferenciaRefeLongitude").val("");
		
		$("#TRefeReferenciaRefeCep").blur(function(){
			buscar_endereco("#TRefeReferenciaRefeCep");
			return false;
		});

		$(".bt-send-xy").click(function(){
			busca_xy($("#TRefeReferenciaIncluirPosicaoForm"),$("#TRefeReferenciaRefeLatitude"),$("#TRefeReferenciaRefeLongitude"));
			return false;
		});

		
	});', false);
?>
<?php endif; ?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<style type="text/css">
	img { max-width: none !important; }
</style>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		setup_mascaras();
	});', false);
?>