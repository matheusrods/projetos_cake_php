<div>
    <?php echo $bajax->form('Usuario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Usuario', 'element_name' => 'buscar_usuario_cliente_acao', $codigo_cliente), 'divupdate' => '.form-procurar-user')) ?>
    <?php echo $this->element('sql_dump'); ?>
    <?php echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => $codigo_cliente))   ?>
    <div class="row-fluid inline">
        <?php //echo $this->BForm->input('codigo_cliente', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
        <?php echo $this->BForm->input('razao_social', array('class' => 'input-large', 'placeholder' => 'Razão social', 'label' => "Razão social")) ?>
        <?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-large', 'placeholder' => 'Nome fantasia', 'label' => "Nome fantasia")) ?>
        <?php echo $this->BForm->input('nome', array('class' => 'input-large', 'placeholder' => 'Nome', 'label' => "Nome")) ?>
        <?php echo $this->BForm->input('perfil', array('empty' => 'Selecione ', 'class' => 'input-large', 'options' => $perfis, 'label' => "Perfil")) ?>
        <?php echo $this->BForm->input('codigo_area_atuacao', array('empty' => 'Selecione ', 'class' => 'input-large', 'options' => $combo_area_atuacao, 'label' => 'Área atuação')) ?>
    </div>

    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-usuario', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php

echo $this->Javascript->codeBlock(" ");
?>
<script>

    $(function(){

        var codigo_cliente = $('#UsuarioCodigoCliente').val();

        <?php if ($this->Buonny->seUsuarioForMulticliente()) :?>
            codigo_cliente = codigo_cliente.split(",");//transforma em array
            codigo_cliente = JSON.stringify(codigo_cliente);//transforma em json
        <?php endif; ?>
        
        desbloquearDiv($("#filtros2"));
        atualizaLista(codigo_cliente)

        $("#limpar-filtro-usuario").on("click", function(){

            bloquearDiv($(".form-procurar-user"));
            $(".form-procurar-user").load(baseUrl + "/filtros/limpar/model:Usuario/element_name:buscar_usuario_cliente_acao/"+codigo_cliente+"/" + Math.random())
        })
    })

    function atualizaLista(codigo_cliente) {
        console.log("Estou atualizando a listagem de usuarios2");
        var div = jQuery("div#busca-lista-usuario-cliente");
        bloquearDiv(div);
        div.load(baseUrl + "usuarios/buscar_listagem_usuario_cliente_acao/" + codigo_cliente + "/" + Math.random());
    }
</script>

