<style>
	legend {font-size: 13px; margin-bottom: 0;}
	.control-group {padding:0; margin: 0}
	.nav-tabs > .active { background: 3E75A5; }
	.pendente { background: #EACCCC; color: #A34646; font-weight: bold; text-decoration: blink; border: 2px solid #A34646; }
	.radio input[type="radio"], .checkbox input[type="checkbox"] {margin: 5px;}
</style>

<?php if(($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES)) : ?>
	<div class="alert alert-success">
		<h5 style="color: #888;">AGUARDANDO A RHHEALTH ANALISAR OS DADOS E SERVIÇOS PROPOSTOS.</h5>
		<label>Seus dados foram enviados porém ainda não avaliamos os serviços e valores propostos, aguarde em breve entraremos em contato.</label>
	</div>
<?php endif; ?>

<?php if(($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::APROVADO)) : ?>
	<div class="form-actions">
		<a class="btn btn-danger" target="_blank" href="/portal/propostas_credenciamento/contrato/<?php echo $codigo_proposta_credenciamento; ?>">Imprimir Contrato</a>
		(Imprimir este contrato, scannear e enviar na tela Envio de Documentação Solicitada!)
	</div>
<?php endif; ?>

<?php if(($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::PROPOSTA_ACEITA)) : ?>
	<div class="alert alert-success">
		<h5 style="color: #888;">SUA PROPOSTA FOI ENVIADA, AGUARDE BREVE ENTRAREMOS EM CONTATO.</h5>
		<label>Seus dados foram enviados porém ainda não foi aprovada por nossos operadores, aguarde em breve entraremos em contato.</label>
	</div>
<?php endif; ?>

<?php if(($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::TERMO_RECUSADO)) : ?>
	<div class="alert alert-danger">
		<h5 style="color: #888;">PROPOSTA DE CREDENCIAMENTO RECUSADA.</h5>
		<label>Você recusou a proposta, iremos analisar o motivo e entraremos em contato.</label>
	</div>
<?php endif; ?>

<?php if(($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_ENVIO_TERMO)) : ?>
	<div class="alert alert-danger">
		<h5 style="color: #888;">AGUARDANDO ENVIO DA PROPOSTA DE CREDENCIAMENTO DIGITALIZADA.</h5>
		<label>Imprimir e enviar a proposta de credenciamento pelo sistema na aba "Documento".</label>
	</div>
	<div class="well">
		<label>(Atenção: A proposta digitalizada é válida por 15 dias, pedimos que envie a proposta pelos correios devidamente preenchida e assinada à sede da RHHealth.)</label>
		<a id="botao-imprimir" class="btn btn-success" href="/portal/propostas_credenciamento/termo/<?php echo $codigo_proposta_credenciamento; ?>" target="_blank">Imprimir Proposta</a>
		<a id="botao-recusar" class="btn btn-danger" href="javascript:void(0);" onclick="$('#botao-recusar').attr('class', 'btn btn-inverte'); $('#recusa').show();">Recusar Proposta</a>
		
		<?php echo $this->BForm->create('Termo', array('type' => 'post' ,'url' => array('controller' => 'propostas_credenciamento', 'action' => 'aceita_termo', $this->passedArgs[0])));?>
		<?php echo $this->BForm->hidden('aprovado'); ?>
							
			<div id="recusa" style="display: none;">
				<?php echo $this->BForm->input('PropostaCredenciamento.codigo_motivo_recusa', array('div' => true, 'legend' => 'Qual o motivo da recusa?', 'options' => $motivos, 'type' => 'radio')) ?>
				<a href="javascript:void(0);" class="btn btn-danger" onclick="$('#TermoAprovado').val(0); $('#TermoMinhaPropostaForm').submit();" id="botao">Confirmar Recusa</a>						
			</div>
		<?php echo $this->BForm->end(); ?>	
	</div>
<?php endif; ?>

<?php if(($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_ANALISE_DOCUMENTOS)) : ?>
	<div class="alert alert-success">
		<h5 style="color: #888;">DOCUMENTAÇÃO OBRIGATÓRIA ENVIADA.</h5>
		<label>Entraremos em contato assim que seus documentos forem analisados, na sequência lhe enviaremos o link para gerar nosso contrato de serviços.</label>
	</div>
<?php endif; ?>

<?php if(($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA)) : ?>
	<div class="alert alert-success">
		<h5>SUA CONTRA PROPOSTA FOI ENVIADA, BREVE RETORNAREMOS.</h5>
		<label>Assim que sua contra proposta for analisada por nossa equipe, você receberá um e-mail lhe informando dos proximos passos.</label>
	</div>
<?php endif; ?>

<?php if(($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::DOCUMENTACAO_SOLICITADA)) : ?>
	<div class="alert alert-danger">
		<h5 style="color: #888;">VOCÊ TEM DOCUMENTOS PENDENTES PARA ENVIAR.</h5>
		<label>Por favor, acesse a aba de envio de documentos e envie os documentos obrigatórios.</label>
	</div>
<?php endif; ?>

<?php if(($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::CONTRATO_ASSINADO_ENVIADO)) : ?>
	<div class="alert alert-success">
		<h5 style="color: #888;">PARABÉNS, VOCÊ JÁ ENVIOU SEU CONTRATO ASSINADO.</h5>
		<label>Não esqueça de enviado fisicamente ao nosso endereço.</label>
	</div>
<?php endif; ?>

<ul class="nav nav-tabs">
	<li class="<?php echo ($aba == 'visualizar') ? 'active' : '' ?>"><a href="#visualizar" data-toggle="tab" onclick="atualiza_url('visualizar', '<?php echo $codigo_proposta_credenciamento; ?>');">Meus Dados</a></li>
	<?php if($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) : ?>
		<li class="<?php echo ($aba == 'valores_exames' || is_null($aba)) ? 'active' : '' ?>"><a href="#valores_exames" data-toggle="tab" onclick="atualiza_url('valores_exames', <?php echo $codigo_proposta_credenciamento; ?>);">Serviços e Valores</a></li>
	<?php endif; ?>
	
	<?php if($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] > 5) : ?>
		<li class="<?php echo ($aba == 'documentacao') ? 'active' : '' ?>"><a href="#documentacao" data-toggle="tab" onclick="atualiza_url('documentacao', '<?php echo $codigo_proposta_credenciamento; ?>');" class="<?php echo (($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_ENVIO_TERMO) || ($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::DOCUMENTACAO_SOLICITADA) || ($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::APROVADO)) ? 'pendente' : ''; ?>">Documentação Solicitada</a></li>	
	<?php endif; ?>
	
	<li class="<?php echo ($aba == 'fotos') ? 'active' : '' ?>"><a href="#fotos" data-toggle="tab" onclick="atualiza_url('fotos', <?php echo $codigo_proposta_credenciamento; ?>);">Envio de Fotos</a></li>
</ul>

<div class="tab-content">
	<div class="tab-pane <?php echo ($aba == 'visualizar') ? 'active' : '' ?>" id="visualizar">
		<?php echo $this->requestAction('/propostas_credenciamento/visualizar/' . $codigo_proposta_credenciamento, array('return')); ?>
	</div>
    <?php if($authUsuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) : ?>
    	<div class="tab-pane <?php echo ($aba == 'valores_exames' || is_null($aba)) ? 'active' : '' ?>" id="valores_exames">
			<?php echo $this->requestAction('/propostas_credenciamento/contraproposta/' . $codigo_proposta_credenciamento . '/' . time(), array('return')); ?>
	    </div>
	<?php endif; ?>
	
	<?php if($dados['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] > 5) : ?>
	    <div class="tab-pane <?php echo ($aba == 'documentacao') ? 'active' : '' ?>" id="documentacao">
	    	<?php echo $this->requestAction('/tipos_documentos/listagem/' . $codigo_proposta_credenciamento, array('return')); ?>
	    </div>
	<?php endif; ?>
    
    <div class="tab-pane <?php echo ($aba == 'fotos') ? 'active' : '' ?>" id="fotos">
	    <?php echo $this->requestAction('fotos/listagem/' . $codigo_proposta_credenciamento, array('return')); ?>
    </div>
</div>

<?php echo $this->Javascript->codeBlock('
	function atualiza_url(parametro, codigo) {
		var newurl = window.location.protocol + "//" + window.location.host + "/portal/propostas_credenciamento/minha_proposta/" + codigo + "/" + parametro;
		window.history.pushState({path:newurl},"Title", newurl);
	}		
'); ?>
			
			