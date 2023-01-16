<?php echo $this->BForm->create('TRefeReferencia', array('url' => array('controller' => 'Referencias','action' => 'incluir_referencia_compartilhada')));?>
	<div id="info" style="width:50%;float:left;">
	<?php echo $this->BForm->hidden('refe_utilizado_sistema', array('value' => 'N')) ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('refe_descricao', array('label' => 'Descrição', 'type' => 'text','class' => 'input-xlarge')) ?>
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
		<div class="control-group input text">
			<label for="RefeReferenciaLongitude">&nbsp</label>
			<?php echo $html->link('Buscar Lat/Long', 'javascript:void(0)', array('class' => 'btn btn-success bt-send-xy')) ;?>
		</div>		
	</div>	
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('refe_cref_codigo', array('label' => 'Classe','class' => 'input-large', 'empty' => 'Classe', 'options' => $classes)) ?>
		<?php echo $this->BForm->input('tloc_tloc_codigo', array('label' => 'Tipo','class' => 'input-medium', 'empty' => 'Tipo', 'options' => $tipos)) ?>
	</div>
	</div>
	<div id="canvas_mapa" style="width:48%;height:580px;float:right;background-color:#F5F5F5;"></div>
	<div class="form-actions" style="clear:both;">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		  <?php if(!$isAjax): ?>
		  	<?php echo $html->link('Voltar',array('controller' => 'Referencias', 'action' => 'adicionar_referencia'), array('class' => 'btn')) ;?>
		  <?php endif; ?>
	</div>

<?php echo $this->BForm->end(); ?>
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

		
	});', false);
?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<style type="text/css">
	img { max-width: none !important; }
</style>
