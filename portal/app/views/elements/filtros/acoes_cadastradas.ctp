<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'acoes_cadastradas'), 'divupdate' => '.form-procurar')) ?>
        
            <div class="row-fluid inline">           
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
            </div>

            <div class="row-fluid inline">
                <?php
                    echo $this->BForm->input('razao_social', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Razão social'));
                    echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Nome fantasia'));
                    echo $this->BForm->input('codigo', array('type' => 'text', 'class' => 'input-mini',  'label' => 'ID da ação'));
					echo $this->BForm->hidden('is_admin', array('value' => $is_admin));
					
					if(isset($codigo_cliente_vinculado)){
						echo $this->BForm->hidden('codigo_cliente_vinculado', array('value' => $codigo_cliente_vinculado));
					}
                ?>
            </div>

            <div class="row-fluid inline">
                <?php
                    echo $this->BForm->input('codigo_acao_melhoria_status', array('empty' => 'Selecione ', 'class' => 'input-medium', 'options' => $acoes_melhorias_status, 'label' => 'Status da ação'));
                    echo $this->BForm->input('codigo_acao_melhoria_tipo', array('empty' => 'Selecione ', 'class' => 'input-medium acao_melhoria_tipo', 'options' => $acoes_melhorias_tipo, 'label' => 'Tipo ação'));
                    echo $this->BForm->input('codigo_pos_criticidade', array('empty' => 'Selecione ', 'class' => 'input-medium pos_criticidade', 'options' => $pos_criticidade, 'label' => 'Criticidade'));
                    echo $this->BForm->input('codigo_origem_ferramenta', array('empty' => 'Selecione ', 'class' => 'input-medium origem_ferramenta', 'options' => $origem_ferramenta, 'label' => 'Origem'));
                    if ($is_admin) {
                        echo $this->BForm->input('codigo_usuario_responsavel', array('empty' => 'Selecione ', 'class' => 'input-medium usuario_responsavel', 'options' => $usuarios_responsaveis, 'label' => 'Responsável'));
                    }
                ?>
            </div>

            <?php if ($is_admin || $this->Buonny->seUsuarioForMulticliente()) :?>
                <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
                <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
            <?php endif; ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<!-- ESPAÇO JS -->
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<script>

jQuery(document).ready(function(){
    atualizaListaCliente();
    
    jQuery("#limpar-filtro").click(function(){
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:acoes_cadastradas/" + Math.random())
    });
        
    function atualizaListaCliente() {     
        var div = jQuery("div.lista");
        bloquearDiv(div);
        
        var is_admin = $('#ClienteIsAdmin').val();

        div.load(baseUrl + 'clientes/listagem_acoes_cadastradas_visualizar/' + is_admin + '/' + Math.random());
    }

    $(document).on('change', '#ClienteCodigoCliente', function(e) {
        e.preventDefault();

        var codigo_cliente = $('#ClienteCodigoCliente').val();

        updateTipoAcao(codigo_cliente);//atualiza o combo de tipo de ação
        updateCriticidade(codigo_cliente);//atualiza o combo de criticidade
        updateOrigemFerramenta(codigo_cliente);//atualiza o combo de origem da ferramenta
        updateUsuariosResponsaveis(codigo_cliente);//atualiza o combo de usuários responsáveis
    });

    function updateTipoAcao(codigo_cliente){
        var input = $("#ClienteCodigoAcaoMelhoriaTipo");
        bloquearDiv(input.parent());

        $("#ClienteCodigoAcaoMelhoriaTipo option").remove();

        jQuery.ajax({
            'url': baseUrl + 'clientes/lista_acoes_tipo/' + codigo_cliente + '/' + Math.random(),
            'dataType': 'json',
            'success': function(data) {
                input.parent().unblock();												
                var options = "";					
                options += "<option>Selecione</option>";

                $.map( data, function( val, i ) {				
                    options += "<option value='"+i+"'>"+ val +"</option>";
                });

                jQuery('.acao_melhoria_tipo').append(options);
            }
        });
    }

    function updateCriticidade(codigo_cliente){
        var input = $("#ClienteCodigoPosCriticidade");
        bloquearDiv(input.parent());

        $("#ClienteCodigoPosCriticidade option").remove();

        jQuery.ajax({
            'url': baseUrl + 'clientes/lista_criticidades/' + codigo_cliente + '/' + Math.random(),
            'dataType': 'json',
            'success': function(data) {
                input.parent().unblock();												
                var options = "";					
                options += "<option>Selecione</option>";

                $.map( data, function( val, i ) {				
                    options += "<option value='"+i+"'>"+ val +"</option>";
                });

                jQuery('.pos_criticidade').append(options);
            }
        });
    }

    function updateOrigemFerramenta(codigo_cliente){
        var input = $("#ClienteCodigoOrigemFerramenta");
        bloquearDiv(input.parent());

        $("#ClienteCodigoOrigemFerramenta option").remove();

        jQuery.ajax({
            'url': baseUrl + 'clientes/lista_origem_ferramenta/' + codigo_cliente + '/' + Math.random(),
            'dataType': 'json',
            'success': function(data) {
                input.parent().unblock();												
                var options = "";					
                options += "<option>Selecione</option>";

                $.map( data, function( val, i ) {				
                    options += "<option value='"+i+"'>"+ val +"</option>";
                });

                jQuery('.origem_ferramenta').append(options);
            }
        });
    }

    function updateUsuariosResponsaveis(codigo_cliente){
        var input = $("#ClienteCodigoUsuarioResponsavel");
        bloquearDiv(input.parent());

        $("#ClienteCodigoUsuarioResponsavel option").remove();

        jQuery.ajax({
            'url': baseUrl + 'clientes/lista_usuarios_responsaveis/' + codigo_cliente + '/' + Math.random(),
            'dataType': 'json',
            'success': function(data) {
                input.parent().unblock();												
                var options = "";					
                options += "<option>Selecione</option>";

                $.map( data, function( val, i ) {				
                    options += "<option value='"+i+"'>"+ val +"</option>";
                });

                jQuery('.usuario_responsavel').append(options);
            }
        });
    }
});

</script>
