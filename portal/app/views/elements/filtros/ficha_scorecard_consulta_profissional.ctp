<?php
$codigo_cliente = null;
$razao_social   = null;
$cnpj           = null;
if(isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']) && $authUsuario['Usuario']['codigo_cliente'] != '' ) {
	$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
	$razao_social   = $authUsuario['Usuario']['nome'];
	$cnpj           = $authUsuario['Usuario']['codigo_documento'];
}
?>
<div class="well">  
	<?php echo $bajax->form('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecard', 'element_name' => 'ficha_scorecard_consulta_profissional'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_cliente_usuario_cliente($this, $usuarios, null, true, 'Código') ?>
		<?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
	</div>
	<div class="row-fluid inline">
		<?php if( $codigo_cliente ) {?>  
		<?php echo $this->BForm->input("codigo_cliente", array('label' => 'Código', 'class' => 'input-mini just-number', 'readonly'=>true, 'value' => $codigo_cliente)) ?>
		<?php } else { ?>
		<?php echo $this->BForm->input("Usuario.codigo_documento", array('label' => 'CPF/CNPJ', 'class' => 'input-medium', 'readonly'=>true)) ?>
		<?php } ?>
		<?php echo $this->Buonny->input_embarcador_transportador($this, $embarcadores, $transportadores, 'codigo_cliente', 'Cliente', true, 'FichaScorecard', null, false) ?>
	</div>  
	<div class="row-fluid inline">
		<?php if (!empty($cnpj)){
			echo $this->BForm->input("Cliente.codigo_documento", array('label' => 'CNPJ', 'class' => 'input-medium cnpj', 'readonly'=>true, 'value'=>$cnpj)); 

		}?>

		<?php if (!empty($razao_social)){

			echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true, 'value'=>$razao_social)) ;
		}  
		?>  
	</div>  
	<div class="row-fluid inline">
		<?php echo $this->BForm->input("codigo_documento", array('label' => 'CPF', 'class' => 'input-medium cpf', 'id'=>'ProfissionalCodigoDocumento', 'after' => $html->link('...', "javascript:carrega_profissional_por_cpf($codigo_cliente)", array('id' =>'avancar','class' => 'btn btn-search-ellipsis', 'title' => 'Buscar dados')) )) ?>
		<?php echo $this->BForm->input("nome", array('label' => 'Nome do Profissional', 'class' => 'input-large just-letters', 'readonly'=>true,'id'=>'ProfissionalNome')) ?>    
		<?php echo $this->BForm->input('placa_veiculo', 
		array( 'label' =>'Placa do veículo','class' => 'placa-veiculo input-small', 'value' => (isset($this->data['placa']) ? $this->data['placa'] : NULL) ) ) ?>
		<?php echo $this->BForm->input('placa_carreta', 
		array( 'label' =>'Placa da carreta', 'class' => 'placa-veiculo input-small', 'value' => (isset($this->data['placa']) ? $this->data['placa'] : NULL) ) ) ?>
		<?php echo $this->BForm->input('placa_bitrem', 
		array( 'label' =>'Placa do bitrem', 'class' => 'placa-veiculo input-small', 'value' => (isset($this->data['placa']) ? $this->data['placa'] : NULL) ) ) ?>    
	</div>

	<div class="row-fluid inline">
		<?php echo $this->BForm->input("codigo_carga_tipo", array('label' => 'Tipo de carga', 'class' => 'input-large', 'empty' => 'Tipo', 'options'=>$carga_tipos)) ?>
		<?php //echo $this->BForm->input("codigo_carga_valor", array('label' => 'Valor', 'class' => 'input-large', 'empty' => 'Valor', 'options'=>$carga_valores)) ?>
	</div>
	<div style="width:800px;">
		<div style="float:left;width:50%;">
			<h5>Origem</h5>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('cidade_origem', array('class' => 'input-large ui-autocomplete-input', 'empty' => 'Cidade', 'label' => 'Cidade', 'for' =>'FichaScorecardCodigoEnderecoCidadeCargaOrigem')) ?>
				<?php echo $this->BForm->input('codigo_endereco_cidade_carga_origem', array('type' => 'hidden')) ?>
				<?php echo $this->BForm->input('codigo_estado_origem',    array('class' => 'input-large', 'type' => 'hidden', 'empty' => 'Cidade', 'label' => false)) ?>
			</div>
		</div>  
		<div style="float:right;width:50%;">
			<h5>Destino</h5>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('cidade_destino', array('class' => 'input-large ui-autocomplete-input','empty' => 'Cidade', 'label' => 'Cidade', 'for'=>'FichaScorecardCodigoEnderecoCidadeCargaDestino' )) ?>
				<?php echo $this->BForm->input('codigo_endereco_cidade_carga_destino', array('type' => 'hidden')) ?>
				<?php echo $this->BForm->input('codigo_estado_destino',    array('class' => 'input-large', 'type' => 'hidden', 'empty' => 'Cidade', 'label' => false)) ?>
			</div>
		</div>  
	</div>
	<br />

	<?php echo $this->BForm->submit('Consultar', array('div' => false, 'class' => 'btn btn-success', 'id' => 'btnConsulta')); ?>
	<?php echo $html->link('Limpar', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock("
	 $(document).ready(function() {    
		$('#FichaScorecardCodigoUsuario').click(function(event){      
			if ( parseInt( $(this).val() ) > 0) {
				carregar_usuario( $(this) );
			}
		}); 

	$('#print').click(function(){
		window.print();      
	});

	$('#btnConsulta').click(function(){
		var div = jQuery('div.lista');
		//bloquearDiv(div);
		if(!validaAssinaturaCliente(3)){
			$('.alert-error').remove();    
			$(\".form-procurar\").prepend(\"<div class='alert alert-error'>Serviço não disponível para o embarcador e transportador selecionados. Favor entrar em contato com o Departamento Comercial.</div>\");
			return false;
		}else{
			div.load(baseUrl + 'fichas_scorecard/consulta_profissional/' + Math.random()); 
		}
		
	});

	$(function() {
		$('.ui-autocomplete-input').autocomplete({        
			source: baseUrl + 'enderecos/autocompletar/',
			focus: function(){return false;},
			minLength: 3,
			select: function( event, ui ) {
				nome_cidade   = ui.item.label;
				codigo_cidade = ui.item.value;        
				codigo_cidade_hidden = $(this).attr('for');        
				$(this).val( nome_cidade );
				$('#'+codigo_cidade_hidden).val( codigo_cidade );
				return false;
			}});
	});


	setup_mascaras();
	setup_codigo_cliente();
	$('#limpar-filtro').click(function(){
		$('.form-procurar :input').not(':button, :submit, :reset, :hidden').val('');
		//$('.form-procurar form').submit(); 
	});
});", false);?>