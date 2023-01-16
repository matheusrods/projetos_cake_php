<meta http-equiv="X-UA-Compatible" content="IE=7" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<legend>Cliente</legend>
<div class="dados_cliente"> 
	<div class="actionbar-right">
		<?= $this->BForm->button('Limpar', array('div' => false, 'class' => 'btn btn-info btn-limpar','id'=>'btn-limpar','type'=>'button')); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->Buonny->input_cliente_usuario_cliente($this, $usuarios, null, true, 'Código') ?>
		<?php echo $this->Buonny->input_embarcador_transportador($this, $embarcadores, $transportadores, 'codigo_cliente', 'Cliente', true, 'FichaScorecard', null, false) ?>
		<?php echo $this->BForm->input("Cliente.codigo_documento", array('label' => 'CNPJ', 'class' => 'input-medium cnpj', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
	</div>
</div>
<div id="lista_contatos">
<?php echo $this->element('fichas_scorecard/lista_contatos', array(
	'titulo'=>'Dados do retorno', 
	'listaContatos'=>isset($this->data['FichaScorecardRetorno']) ? $this->data['FichaScorecardRetorno'] : array(), 
	'tipo'=>'retorno',
	'model'=>'FichaScorecardRetorno',
	'tipo_retorno'=>$tipo_retorno_cliente,
	'disabled' => (!empty($this->data['FichaScorecard']['codigo'])),
	'tipos_retorno_fixo' => array(TipoRetorno::TIPO_RETORNO_TELEFONE, TipoRetorno::TIPO_RETORNO_EMAIL)
))?>
</div>

<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        jQuery('#btn-limpar').click(function(){	
			$('.dados_cliente input').each(function(){ $(this).val(''); });
			$('#FichaScorecardCodigoUsuario').html('<option value=\'\'>Usuário</option>');
			$('#FichaScorecardCodigoEmbarcador').html('<option value=\'\'>Embarcador</option>');
			$('#FichaScorecardCodigoTransportador').html('<option value=\'\'>Transportador</option>');
			$('.dados-ficha').hide();
        });        
    });", false);
?>