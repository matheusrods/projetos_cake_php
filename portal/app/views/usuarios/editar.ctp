<div class="content">

    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#dados" data-toggle="tab">Dados do usuário</a>
        </li>
        <li>
            <a href="#logs" data-toggle="tab">Logs de alteração</a>
        </li>
        <?php if($this->data['Usuario']['codigo_uperfil'] <> Uperfil::ADMIN): ?>
            <?php if($this->data['Usuario']['codigo_cliente']) : ?>
                <li id="li-multicliente">
                    <a href="#multicliente" data-toggle="tab" class="aba-multiempresa">Multi Cliente</a>
                </li>
                <li id="li-usuariounidade">
                    <a href="#usuariounidade" data-toggle="tab" class="aba-multiempresa">Usuário/Unidades</a>
                </li>
            <?php else: ?>
                <li id="li-multiempresa">
                    <a href="#multiempresa" data-toggle="tab" class="aba-multiempresa">Multi Empresa</a>
                </li>
            <?php endif; ?>
        <?php endif ?>
        <li>
            <a href="#multiconselho" data-toggle="tab">Multi Conselho</a>
        </li>
    </ul>
    <?php echo $this->BForm->create('Usuario', array('action' => 'editar', $this->passedArgs[0])); ?>
    <div class="tab-content">
        <div class="tab-pane active" id="dados">
            <?php echo $this->element('usuarios/fields'); ?>
        </div>
        <div class="tab-pane" id="logs">&nbsp;</div>
        <?php if($this->data['Usuario']['codigo_cliente']) : ?>
            <div class="tab-pane" id="multicliente">
                <?php echo $this->element('usuarios_multi_cliente/clientes_por_usuario'); ?>
            </div>

            <div class="tab-pane" id="usuariounidade">
                <?php echo $this->element('usuarios/usuario_unidades'); ?>
            </div>

        <?php else: ?>
            <div class="tab-pane" id="multiempresa">
            </div>
        <?php endif; ?>
        <div class="tab-pane" id="multiconselho">
            <?php echo $this->element('usuarios/usuario_multi_conselho'); ?>
        </div>
    </div>

    <div class="form-actions">
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
    </div>
    <?php echo $this->BForm->end(); ?>
</div>
<?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php $this->addScript($this->Buonny->link_js('autocomplete')) ?>

<?php $this->addScript($this->Javascript->codeBlock("
	jQuery(document).ready(function() {
		setup_mascaras();
		atualizaListaIps('".$this->passedArgs[0]."');
		listar_listar_logs('".$this->passedArgs[0]."');
		
		$('#UsuarioEditarForm').submit(function(elemento) {
			if($('#UsuarioUsuarioMultiEmpresa1').is(':checked')) {
				var empresas_liberadas = 0;
		
				$('.checkbox-multiempresa').each(function(i, elemento) {
					if(elemento.checked) {
						empresas_liberadas++;
					}
				});
			}
		
			if(empresas_liberadas == 1) {
				$('.aba-multiempresa').css('background', '#cc4f16');
				$('.aba-multiempresa').css('color', '#fff');
				
				$('#multiempresa').prepend('<label style=\"color: #cc4f16;\">Você deve selecionar mais de uma empresa, para acesso multi empresa!</label>');
				$('.aba-multiempresa').click();		
		
				$('.checkbox-multiempresa').change(function() {
					$('.aba-multiempresa').removeAttr('background').removeAttr('style');
					$('#multiempresa label').remove();
				});
		
				return false;
			}
		});
		
		$('#UsuarioUsuarioMultiEmpresa1').click(function() {
			$('.aba-multiempresa').css('background', '#5bb75b');
			$('.aba-multiempresa').css('color', '#fff');
		});
	});
		
	function listar_listar_logs( codigo_usuario ) {
		var div = $('#logs');
		$.ajax({
			type: 'post',
			url: baseUrl + 'usuarios_logs/listar/' + codigo_usuario +'/'+ Math.random(),
			cache: false,
			data: {'dados':codigo_usuario },
			beforeSend : function(){
				bloquearDiv(div);
			},
			success: function(data){
				div.html(data);
			},
			error: function(erro,objeto,qualquercoisa){
				console.log(erro+' - '+objeto+' - '+qualquercoisa);
				div.unblock();
			}
		});
	}
		
	")) ;
?>
