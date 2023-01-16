<?php if(!$authUsuario["Usuario"]["codigo_cliente"]): ?>
    <div class='well'>
    	<?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'janelas'), 'divupdate' => '.form-procurar')) ?>
            <div class='row-fluid inline'>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', TRUE, 'Cliente') ?>
        </div>
        <div class='row-fluid inline'>
          <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn filtrar')); ?>
          <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>
    	<?php echo $this->BForm->end() ?>
    </div>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
        $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:janelas/" + Math.random(),function(){
                atualizaListaConfiguracaoJanela();
            });
        });

        '.($isPost || $authUsuario["Usuario"]["codigo_cliente"] ? "atualizaListaConfiguracaoJanela()" : "").'

        function atualizaListaConfiguracaoJanela(){
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes/listar_janela/" + Math.random());
        }
        setup_mascaras();
    });', false);?>