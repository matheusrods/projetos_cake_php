<?php echo $this->BForm->create('TRefeReferencia', array('url' => array('controller' => 'Referencias','action' => 'incluir',$cliente['Cliente']['codigo'],$new_window)));?>
<?php echo $retorno = $this->BForm->error_menssage($menssagem) ?>

	<div class='row-fluid inline'>
		<div id="cliente" class='well'>
			<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
			<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		</div>
	</div>
	<div id="info" style="width:45%;float:left;">
	<?php echo $this->BForm->hidden('refe_pess_oras_codigo_local', array('value' => $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'])) ?>
	<?php echo $this->BForm->hidden('refe_utilizado_sistema', array('value' => 'N')) ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('refe_descricao', array('label' => 'Descrição', 'type' => 'text','class' => 'input-xlarge')) ?>
		<?php echo $this->BForm->input('refe_cnpj_empresa_terceiro', array('label' => 'CNPJ', 'type' => 'text','class' => 'input-medium')) ?>
	</div>
	<div class='row-fluid inline'>		
		<?php echo $this->BForm->input('refe_cep', array('label' => 'CEP', 'type' => 'text','class' => 'input-small formata-cep')) ?>
		<?php echo $this->BForm->input('refe_endereco_empresa_terceiro', array('label' => 'Endereço', 'type' => 'text','class' => 'input-xlarge endereco')) ?>
	</div>
	<div class='row-fluid inline'>
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
		<?php echo $this->BForm->hidden('refe_poligono') ?>
		
		<div class="control-group input text">
			<label for="RefeReferenciaLongitude">&nbsp</label>
			<?php echo $html->link('Buscar Lat/Long', 'javascript:void(0)', array('class' => 'btn btn-success bt-send-xy')) ;?>
		</div>	
		
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('refe_tempo_alvo', array('label' => 'Tempo para Determinar Status Entregando', 'type' => 'text','class' => 'input-small', 'placeholder' => 'Minutos', 'maxlength' => '2')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('refe_depara', array('label' => 'Codigo Alvo Cliente', 'type' => 'text','class' => 'input-medium')) ?>

		<div class="control-group input text">
			<label for="RefeReferenciaLongitude">&nbsp&nbsp&nbsp&nbsp</label>
		</div>
		<div class="control-group input text">
			<label for="RefeReferenciaLongitude">&nbsp</label>
			<?php echo $this->BForm->input('refe_critico', array('label' => 'Alvo Crítico','class' => 'input-large', 'type' => 'checkbox',)) ?>
		</div>
		<div class="control-group input text">
			<label for="RefeReferenciaLongitude">&nbsp</label>
			<?php echo $this->BForm->input('refe_permanente', array('label' => 'Alvo Permanente','class' => 'input-large', 'type' => 'checkbox',)) ?>
		</div>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('refe_cref_codigo', array('label' => 'Classe','class' => 'input-large', 'empty' => 'Classe', 'options' => $classes)) ?>
		<?php echo $this->BForm->input('refe_band_codigo', array('label' => 'Bandeira','class' => 'input-medium', 'empty' => 'Bandeira', 'options' => $bandeiras)) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('refe_regi_codigo', array('label' => 'Região','class' => 'input-medium', 'empty' => 'Região', 'options' => $regioes)) ?>
		<?php echo $this->BForm->input('tloc_tloc_codigo', array('label' => 'Tipo','class' => 'input-medium', 'empty' => 'Tipo', 'options' => $tipos)) ?>
		<?php echo $this->BForm->input('refe_raio', array('label' => 'Raio (m)', 'type' => 'text','class' => 'input-small')) ?>
	</div>
	</div>
	<?php $mapOptions = array(
		'polygon_input' => 'TRefeReferenciaRefePoligono',
		'latitude_input' => 'TRefeReferenciaRefeLatitude',
		'longitude_input' => 'TRefeReferenciaRefeLongitude',
		'range_input' => 'TRefeReferenciaRefeRaio',
		'polygon_string' => (isset($this->data['TRefeReferencia']['refe_poligono']) ? $this->data['TRefeReferencia']['refe_poligono'] : null),
	) ?>
	<?php echo $this->GoogleMap->mapaEdicaoAlvo($mapOptions) ?>
	<div class="form-actions" style="clear:both;">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		  <?php if(!$new_window): ?>
		  	<?php echo $html->link('Voltar',array('controller' => 'Referencias', 'action' => 'adicionar_referencia'), array('class' => 'btn')) ;?>
		  <?php endif; ?>
	</div>

<?php echo $this->BForm->end(); ?>
<?php $verifica = ($fechar) ? '1' : '0';?>
<?php $verifica_id = ($id) ? $id : '-1';?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		setup_mascaras();

		$("#TRefeReferenciaRefeLatitude").val("-9.00000");
		$("#TRefeReferenciaRefeLongitude").val("-54.00000");
		ver_mapa("#TRefeReferenciaRefeLatitude","#TRefeReferenciaRefeLongitude","#TRefeReferenciaRefeRaio");
		$("#TRefeReferenciaRefeLatitude").val("");
		$("#TRefeReferenciaRefeLongitude").val("");
		
		$("#TRefeReferenciaRefeCep").blur(function(){
			buscar_endereco("#TRefeReferenciaRefeCep");
			return false;
		});

		$(".bt-send-xy").click(function(){
			busca_xy($("#TRefeReferenciaIncluirForm"),$("#TRefeReferenciaRefeLatitude"),$("#TRefeReferenciaRefeLongitude"),$("#TRefeReferenciaRefeRaio"));
			return false;
		});

		if('.$verifica.' == 1){
			campo = window.name;
			qtd = campo.indexOf("/");
			novo_campo = campo.substring(1, qtd); 
			window.opener.document.getElementById(novo_campo+"Visual").value = document.getElementById("TRefeReferenciaRefeDescricao").value;
			window.opener.document.getElementById(novo_campo).value = '.$verifica_id.';
			window.close();
		}

		
	});', false);
?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<style type="text/css">
	img { max-width: none !important; }
</style>
