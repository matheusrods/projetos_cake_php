<div class="content">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#dados" data-toggle="tab">Dados do usuário</a>
        </li>
        <li>
            <a href="#logs" data-toggle="tab">Logs de alteração</a>
        </li>
		<?php if($this->data['Usuario']['codigo_cliente']) : ?>
			<li id="li-multicliente">
				<a href="#multicliente" data-toggle="tab" class="aba-multiempresa">Multi Cliente</a>
			</li>
            <li id="li-usuariounidade">
                <a href="#usuariounidade" data-toggle="tab" class="aba-multiempresa">Usuário/Unidades</a>
            </li>		
		<?php endif; ?>
        <li>
            <a href="#multiconselho" data-toggle="tab">Multi Conselho</a>
        </li>
    </ul>
    <?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?>
    <div class="tab-content">
        <div class="tab-pane active" id="dados">
            <?php echo $this->BForm->create('Usuario', array('action' => 'editar_por_cliente', $this->passedArgs[0] )); ?>
            <?php echo $this->element('usuarios/fields_por_cliente'); ?>
        </div>
        <div class="tab-pane" id="logs">&nbsp;</div>
        
		<div class="tab-pane" id="multicliente">
			<?php echo $this->element('usuarios_multi_cliente/clientes_por_usuario'); ?>
		</div>        
        <div class="tab-pane" id="usuariounidade">
            <?php echo $this->element('usuarios/usuario_unidades'); ?>
        </div>
        <div class="tab-pane" id="multiconselho">
			<?php echo $this->element('usuarios/usuario_multi_conselho'); ?>
		</div>
    </div>
</div>
<?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php $this->addScript($this->Buonny->link_js('autocomplete')) ?>

<?php $this->addScript($this->Javascript->codeBlock("jQuery(document).ready(function() {
        atualizaListaIps('".$this->passedArgs[0]."');
        listar_listar_logs('".$this->passedArgs[0]."');
		
        function listar_listar_logs( codigo_usuario ){
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
                    alert(erro+' - '+objeto+' - '+qualquercoisa);
                    div.unblock();
                }
            });
        }
    })"))
?>