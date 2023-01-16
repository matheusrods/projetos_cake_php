<div style="padding-left: 200px">
	<h4>INSTRUMENTO PARTICULAR DE ADITAMENTO AOS SERVIÇOS DE TELECONSULT</h4>
	<div style="width:720px; text-align:justify; text-indent: 1.5em;">
		<p style="padding: 1em 0em">Pelo presente instrumento de contrato de prestação de serviço de informações cadastrais, de um lado a empresa Buonny Projetos e Serviços de Riscos Securitários Ltda., com sede na Alamedas dos Guatás, nº 191, Saúde, Município de São Paulo, Estado de São Paulo, inscrita no Cadastro Nacional de Pessoas Jurídicas do Ministério da Fazenda CNPJ sob o n° 06.326.025/00001-66, doravante neste instrumento de contrato denominada simplesmente BUONNY e de outro lado a empresa qualificada no Aceite da Proposta Comercial, parte integrante deste instrumento, doravante denominada simplesmente SOLICITANTE, ambas representadas na forma da lei, contratam entre si INCLUIR na prestação dos serviços o Serviço de RENOVAÇÃO AUTOMÁTICA dos cadastros de funcionários e /ou agregados, obedecendo a vigência de cada categoria.</p>
		<p style="padding: 1em 0em">Ficam mantidas e continuam em pleno vigor todas as demais Cláusulas e Condições da Prestação de Serviços que não foram objeto do presente instrumento.</p>
		<p style="padding: 1em 0em">E, por estarem assim, justas e contratadas, a SOLICITANTE declara ter conhecimento integral dos serviços ao qual adere, concordando integralmente com os termos e condições do serviço, solicitando a Buonny seu cadastramento como usuária deste serviço.</p>
	</div>
	<p style="padding: 5em 0em">São Paulo, <?php echo $data_contrato ?>.</p>
	<div>
		<?php echo $this->Html->link('Aceito os termos do aditamento', 'javascript:void(0)', array('class' => 'btn btn-medium btn-success', 'id' => 'bt-confirmar' ,'escape' => false, 'title'=>'Confirmar aceite de aditamento')); ?>
	</div>
</div>

<div id="dialog-confirm" title="Confirmação de adesão" style="display:none">
	<h5><i>Prezado Cliente,</i></h5>
	<p style="font-size:12px;text-align:justify;">
		Confirma a adesão ao serviço de renovação automática?
	</p>
	<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
		<div class="ui-dialog-buttonset">
			<?php echo $this->BForm->button('Sim', array('div' => false, 'class' => 'btn btn-success confirmar')) ?>
			<?php echo $html->link('Não', 'javascript:void(0)', array('class' => 'btn cancelar')) ?>
		</div>
	</div>
</div>

<div id="box" style="display:none">Confirmar aceitação dos termos do contrato?</div>
<?php $this->addScript($this->Html->scriptBlock('var baseUrl = "'.$this->webroot.'";')); ?>
<?php $this->addScript($this->Javascript->codeBlock('
	$(function(){
		
		$(document).on("click", "#dialog-confirm .cancelar", function() {
			$( "#dialog-confirm" ).dialog( "close" );
			return false;
		});

		$(document).on("click", "#dialog-confirm .confirmar", function() {
			$.ajax({
			type: "post",
				url: baseUrl + "clientes_produtos_servicos2/salvar_adendo_contrato/",
				success: function(data){
					$( "#dialog-confirm" ).dialog( "close" );
					
					if(data){
						alert(data);
					} else {
						alert( "Parabéns, você acaba de confirmar sua adesão ao serviço de renovação automática. Você receberá via email esta confirmação. Obrigado, Buonny Projetos e Serviços" );
						window.location = baseUrl;
					}
				}
			});
		});

		$("#bt-confirmar").click(function() {
			$("html, body").animate({ scrollTop: 0 });
			$( "#dialog-confirm" ).dialog();
			return false;
		});
		
	});'));
?>