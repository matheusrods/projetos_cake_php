<?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post' ,'url' => array('controller' => 'propostas_credenciamento','action' => 'etapa1')));?>

	<!-- HASH RHHEALTH -->
	<?php echo $this->BForm->hidden('MultiEmpresa.hash', array('value' => 'f37e7a04262676700f33e6b3b218cf4e')); ?>
	
	<div class="row center" style="margin-bottom: 50px; background: #FFF; margin-top: -20px; padding: 20px;">
		<h2 class="center">Proposta de Credenciamento</h2>
	
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<div class="form-group">
				<h3 >Dados da Empresa:</h3><hr />
				<div class="input-group">
	    			<span class="input-group-addon">CNPJ Matriz ( * )</span>
	    			<?php echo $this->BForm->input('PropostaCredenciamento.codigo_documento', array('onblur' => 'proposta.validaCNPJ(this, 1, "etapa1", "", 0);', 'class' => 'form-control cnpj', 'label' => false, 'style' => 'width: 55%;')); ?>
	    			<img src="/portal/img/default.gif" id="cnpj_loading_0" style="padding: 0 0 0 10px; display: none;">
	    			<label style="float: left; padding: 10px 0 0 10px; font-size: 10px; display: none;" id="link_auto_completar_cnpj"><a href="javascript:void(0);" onclick="proposta.carregaCNPJ();">COMPLETAR FORMULÁRIO</a></label>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Razão Social ( * )</span>
	    			<?php echo $this->BForm->input('PropostaCredenciamento.razao_social', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'data-required' => true)); ?>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Nome Fantasia ( * )</span>
	    			<?php echo $this->BForm->input('PropostaCredenciamento.nome_fantasia', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">E-mail ( * )</span>
	    			<?php echo $this->BForm->input('PropostaCredenciamento.email', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
				</div>
			</div>
			
			<div class="form-group">
				<h3 >Endereço Matriz:</h3><hr />
				<div class="input-group">
	    			<span class="input-group-addon">Cep ( * )<br /></span>
	    			<?php echo $this->BForm->input('PropostaCredEndereco.0.cep', array('class' => 'form-control formata-cep', 'label' => false, 'style' => 'width: 55%;', 'multiple', 'onchange' => '$("#pesquisa_cep_0").show();')); ?>
	    			<img src="/portal/img/default.gif" id="carregando_0" style="padding: 10px 0 0 10px; display: none;">
	    			<label style="float: left; padding: 10px 0 0 10px; font-size: 10px;" id="pesquisa_cep_0"><a href="javascript:void(0);" onclick="proposta.buscaCEP(0, 'PropostaCredEndereco', 'PropostaCredenciamento');">COMPLETAR ENDEREÇO</a></label>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Logradouro ( * )</span>
	    			<?php echo $this->BForm->input('PropostaCredEndereco.0.logradouro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Número ( * )</span>
	    			<?php echo $this->BForm->input('PropostaCredEndereco.0.numero', array('class' => 'form-control', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Complemento</span>
	    			<?php echo $this->BForm->input('PropostaCredEndereco.0.complemento', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Bairro ( * )</span>
	    			<?php echo $this->BForm->input('PropostaCredEndereco.0.bairro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Estado ( * )</span>
	    			<?php echo $this->BForm->input('PropostaCredEndereco.0.estado', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $estados, 'onchange' => 'proposta.buscaCidade(this, null, null, "PropostaCredEndereco0CodigoCidadeEndereco", null, null, 0)')) ?>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Cidade ( * )</span>
	    			<span id="cidade_combo_0">
	    				<?php echo $this->BForm->input('PropostaCredEndereco.0.cidade', array('label' => false, 'class' => 'form-control', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false)) ?>
	    			</span>
	    			<div id="carregando_cidade_0" style="display: none; text-align: left; border: 1px solid #CCCCCC; padding: 8px;">
	    				<img src="/portal/img/ajax-loader.gif" border="0"/>
	    			</div>
				</div>				
			</div>
			<div class="form-actions center">
				<a href="/" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>
				<button type=submit class="btn btn-success btn-lg"><i class="glyphicon glyphicon-share"></i> Avançar</button>
			</div>
	  	</div>

	  	<div class="col-md-3"></div>
	</div>

<?php echo $this->BForm->end(); ?>

<div class="modal fade" id="modal">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Aguarde...</h4>
			</div>
	    	<div class="modal-body">
	    		Estamos verificando o CNPJ!
	    		<br />
	    		<img src="/portal/img/ajax-loader.gif">
	    		<br />
	    	</div>
	    </div>
	</div>
</div>

<div class="modal fade" id="modal_receita">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Confirmação Humana:</h4>
			</div>
	    	<div class="modal-body">
	    		<img src="/portal/img/ajax-loader-medio.gif" id="carregando_captcha" border="0" style="padding-left: 5px; display: none;"/>
	    		<img src="/portal/propostas_credenciamento/getcaptcha?<?php echo time(); ?>" id="img_captcha" border="0">
	    		<br /><br />
	    		<?php echo $this->BForm->input('texto_captcha', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Digite o texto acima', 'style' => 'width: 100%;', 'data-required' => true)); ?>
	    		<a href="javascript:void(0);" onclick="proposta.trocaCaptcha();" id="troca_imagem">Trocar Imagem!</a>
				<img src="/portal/img/ajax-loader.gif" id="carregando_receita" border="0" style="padding: 10px 0 0 10px; display: none;"/>
	    		<br />
	    		<a href="javascript:void(0);" class="btn btn-success right" onclick="$('#troca_imagem').hide(); proposta.enviaCaptcha(this, 0, 'etapa1');"><i class="icon-white icon-ok-sign"></i> Enviar</a>
	    		<a href="javascript:void(0);" class="btn btn-danger right" onclick="$('#modal_receita').modal('hide');" style="margin-right: 5px;"><i class="icon-white icon-remove-sign"></i> Fechar</a>
	    		<br /><br />
	    	</div>
	    </div>
	</div>
</div>

<div class="modal fade" id="modal_BO">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Houve algum erro!</h4>
			</div>
	    	<div class="modal-body">
				<div id="msg_error"></div>	    		
	    		<br /><br />
	    	</div>
	    </div>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('$(function() { setup_mascaras(); setup_time(); });'); ?>
<?php echo $this->Buonny->link_js('proposta_credenciamento'); ?>