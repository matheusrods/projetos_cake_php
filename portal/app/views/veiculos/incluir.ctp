
	<div class='row-fluid inline'>
		<div id="cliente" class='well'>
			<strong>Código: </strong><?= $cliente['Cliente']['codigo']; ?>
			<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		</div>
	</div>
	
	<div id="form-pai" class='row-fluid inline'>
		<?php echo $this->BForm->input('codigo_cliente', array('label' => false, 'type' => 'hidden','value' => $cliente['Cliente']['codigo'])) ?>
		<?php if(isset($placa)): ?>
			<?php echo $this->BForm->input('placa', array('label' => false,'placeholder' => 'Placa','type' => 'text','class' => 'input-small placa-formulario', 'readonly' => true, 'value' => $placa)) ?>
		<?php else: ?>
			<?php echo $this->BForm->input('placa', array('label' => false,'placeholder' => 'Placa','type' => 'text','class' => 'input-small placa-formulario')) ?>
			<?php echo $html->link('Cancelar', 'adicionar_veiculo', array('id' => 'sair', 'class' => 'btn btn-danger')) ;?>
		<?php endif; ?>
		
	</div>

	<div id="form-filho"></div>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
	
		load_formulario($("#codigo_cliente"),$("#placa"));

		function load_formulario(cliente,veiculo){
			if( veiculo.val() ){
				$("#form-filho").load(baseUrl+"Veiculos/formulario_veiculo/" + cliente.val() + "/" + veiculo.val() + "/" + Math.random());
			}
			return false;
		}

		$(".placa-formulario").each( function(){
			if(!$(this).hasClass("format-plate")){
				$(this).mask("aaa-999?9",{
					completed: function(){
						load_formulario($("#codigo_cliente"),$("#placa"));
					}
				}).addClass("uppercase").addClass("format-plate");
			}
		});
		
	});', false);
?>