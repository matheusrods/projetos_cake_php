<style>
    input[type="radio"], input[type="checkbox"] {
        float: left; margin: 0 5px;
    }
</style>

<div class="usuarios_fields">
    <div class="row-fluid inline">
        <?php echo $this->BForm->hidden('codigo'); ?>
        <?php echo $this->BForm->hidden('codigo_cliente'); ?>
        <?php echo $this->BForm->input('apelido', array('class' => 'input-small', 'label' => 'Login (*)')); ?>
        <?php echo $this->BForm->input('nome', array('class' => 'input-large', 'label' => 'Nome (*)')); ?>
        <?php echo $this->BForm->input('codigo_departamento', array('class' => 'input-medium', 'label' => 'Departamento (*)', 'options' => $departamentos, 'empty' => 'Selecione')); ?>
        <?php if (!isset($barrar_perfil) || $barrar_perfil == 0): ?>
            <?php echo $this->BForm->input('codigo_uperfil', array('class' => 'input-medium', 'label' => 'Perfil (*)', 'options' => $u_perfis, 'empty' => 'Selecione')); ?>
            <div class="row-fluid filial" style="display:none;">
                <?php if(isset($filiais)):?>
                    <?php echo $this->BForm->input('codigo_filial', array('class' => 'input-medium', 'label' => 'Filial', 'options' => $filiais, 'empty' => 'Selecione')); ?>
                <?php endif;?>
            </div>
        <?php endif; ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('email', array('class' => 'input-large', 'label' => 'E-mail (*)')); ?>
        <?php echo $this->BForm->input('celular', array('class' => 'input-large celular', 'maxlength'=>'14','label' => 'Celular')); ?>
    </div>

    <div class="row-fluid inline">
        <?php if( empty( $authUsuario['Usuario']['codigo_cliente'] ) ): ?>
            <?php $_SESSION['Last']['codigo_cliente'] = ''; ?>
            <?php echo $this->Buonny->input_codigo_cliente( $this, 'codigo_cliente', 'Cliente', 'Cliente', 'Usuario', $this->data['Usuario']['codigo_cliente']); ?>
        <?php else: ?>
            <?= $this->BForm->hidden('codigo_cliente'); ?>
        <?php endif; ?>
        <?php if ((isset($authUsuario['Usuario']['codigo_uperfil']) && $authUsuario['Usuario']['codigo_uperfil'] === Uperfil::ADMIN) ): ?>
            <?php echo $this->BForm->input('codigo_fornecedor', array('class' => 'input-mini', 'label' => 'Fornecedor')); ?>
        <?php endif; ?>
    </div>

    <?php echo $this->element('usuarios/fields_configurar_tipos_alerta'); ?>

    <?php 
    if( !empty( $authUsuario['Usuario']['codigo_cliente'] ) ){
    	echo $this->element('usuarios/fields_gestao_risco'); 
    }
    ?>

    <?php if ( (isset($authUsuario['Usuario']['admin']) && $authUsuario['Usuario']['admin'] == 1)  || (isset($authUsuario['Usuario']['codigo_uperfil']) && $authUsuario['Usuario']['codigo_uperfil'] === Uperfil::ADMIN) ): ?>

        <?php if((empty($this->data['Usuario']['codigo_cliente']) && empty($this->data['Usuario']['codigo_fornecedor']) && (isset($this->data['Usuario']['codigo_uperfil']) && ($this->data['Usuario']['codigo_uperfil'] != Uperfil::ADMIN))) && isset($this->passedArgs[0])) : ?>
            <h4> Tem acesso Multi Empresas? </h4>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('usuario_multi_empresa', array('div' => false, array('class' => 'inline_labels', 'style' => 'float: left;'), 'legend' => false, 'options' => array('0' => 'Não', '1' => 'Sim'), 'type' => 'radio', 'onchange' => 'listar_multiempresa(this, '.$this->passedArgs[0].', 0);')) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if($interno == 'S') : ?>
        <h4> Gestor de Operação </h4>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('flag_notas_fiscais_servicos_acrescimo_desconto', array(
                    'div' => false, array(
                        'class' => 'inline_labels',
                        'style' => 'float: left;'
                    ),
                    'legend' => false,
                    'label'=> 'Ativo',
                    'options' => array('0' => 'Não', '1' => 'Sim'),
                    'type' => 'checkbox')
            ) ?>
        </div>
    <?php endif; ?>

</div>

<?php $this->addScript($this->Buonny->link_js('usuarios_contatos.js')); ?>
<?php echo $javascript->codeblock('
		// Verifica alertas por Perfil
        $(document).on("change","#UsuarioCodigoUperfil",function(){
            if($(this).val() != ""){
                var div = $(".usuarios_fields");
                bloquearDiv(div);
                $.get(baseUrl + "Uperfis/busca_tipo_perfil_json/" + $(this).val(),function(data){
                    data = $.parseJSON(data);
                    div.unblock();
                });
            }else{
                $(".filial").hide();
            }
        });
		
        jQuery(document).ready(function() {
			define_multi_empresa("' . (isset($this->data['Usuario']['usuario_multi_empresa']) ? $this->data['Usuario']['usuario_multi_empresa'] : 0) . '");
			
	        setup_mascaras();
	        showAlertasTipos();       
			
	        $(".checkbox-alerta").change(function(){
	            showAlertasTipos();
	        });
		
        });
		
		
	function define_multi_empresa(flag) {
		var codigo_usuario = "' . (isset($this->passedArgs[0]) ? htmlspecialchars($this->passedArgs[0]) : 0) . '";
		
		if((flag == "1") && codigo_usuario) {
			var codigo_cliente = "' . $this->data['Usuario']['codigo_cliente'] . '";
		
			if(!codigo_cliente) {
				listar_multiempresa(null, codigo_usuario, 1);
			}
		}
	}
		
	function showAlertasTipos(){
		
		var checked = false;
		$(".checkbox-alerta").each(function(){
			if($(this).is(":checked")) {
				checked = true;
			}
		})

		if(checked){
			$(".alertas-tipos").show();
			$(".veiculos-alertas").show();
		} else {
			$(".alertas-tipos").hide();
			$(".veiculos-alertas").hide();
		}
	}
		
	function listar_multiempresa( element, codigo_usuario, forca_aba ) {
		mostra_aba = (forca_aba == "0") ? $(element).val() : 1;

		if(mostra_aba == "1") {
			$("#li-multiempresa").show();
		
			var div = $("#multiempresa");
			$.ajax({
				type: "post",
				url: baseUrl + "usuarios_multi_empresa/listar/" + codigo_usuario +"/"+ Math.random(),
				cache: false,
				data: {"dados":codigo_usuario },
				beforeSend : function(){
					div.html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> Carregando Empresas");
				},
				success: function(data) {
					if(data) {
						div.html(data);
					} else {
						div.html("<b>Nenhuma Empresa Encontrada.</b>");
					}
				}
			});

		} else {
			$("#li-multiempresa").hide();
		}		
	}
		
', false);?>

<?php
if(isset($this->data['Usuario']['codigo_uperfil']) && $this->data['Usuario']['codigo_uperfil']){
    echo $javascript->codeblock('
            jQuery(document).ready(function() {
                $("#UsuarioCodigoUperfil").change();
            });
        ');
}
if(isset($this->data['Usuario']['codigo']) && $this->data['Usuario']['codigo']){
    echo $this->Javascript->codeBlock('
            jQuery(document).ready(function(){
                atualizaListaUsuarioVeiculoAlerta('.$this->data['Usuario']['codigo'].');
            });
        ');
}
?>

<script>
    $(function(){

        $('.celular').mask('(99)99999-9999').addClass('format-phone');
    })
</script>
