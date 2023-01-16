<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'acoes_cadastradas_visualizar', $codigo_cliente), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php
            echo $this->BForm->input('is_admin', array('type' => 'hidden', 'value' => "{$is_admin}"));
            echo $this->BForm->input('codigo_cliente', array('type' => 'text', 'label' => 'Código', 'class' => 'input-mini', 'readonly' => 'readonly', 'value' => "{$codigo_cliente}"));
            echo $this->BForm->input('nome_fantasia1', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Nome fantasia', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));
            ?>
        </div>

        <div class="row-fluid inline">
            <?php
            echo $this->BForm->input('razao_social', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Razão social'));
            echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Nome fantasia'));
            echo $this->BForm->input('codigo', array('type' => 'text', 'class' => 'input-mini',  'label' => 'ID da ação'));
            ?>
        </div>

        <div class="row-fluid inline">
            <?php
            echo $this->BForm->input('codigo_acao_melhoria_status', array('empty' => 'Selecione ', 'class' => 'input-medium', 'options' => $acoes_melhorias_status, 'label' => 'Status da ação'));
            echo $this->BForm->input('codigo_acao_melhoria_tipo', array('empty' => 'Selecione ', 'class' => 'input-medium', 'options' => $acoes_melhorias_tipo, 'label' => 'Tipo ação'));
            echo $this->BForm->input('codigo_pos_criticidade', array('empty' => 'Selecione ', 'class' => 'input-medium', 'options' => $pos_criticidade, 'label' => 'Criticidade'));
            echo $this->BForm->input('codigo_origem_ferramenta', array('empty' => 'Selecione ', 'class' => 'input-medium', 'options' => $origem_ferramenta, 'label' => 'Origem'));
            if ($is_admin) {
                echo $this->BForm->input('codigo_usuario_responsavel', array('empty' => 'Selecione ', 'class' => 'input-medium', 'options' => $usuarios_responsaveis, 'label' => 'Responsável'));
            }
//            echo $this->BForm->input('status_solicitacao', array('empty' => 'Selecione ', 'class' => 'input-medium', 'options' => array('1' => 'Em analise', '2' => 'Aceita', '3' => 'Recusada'), 'label' => 'Status da solicitação'));
            ?>
        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>

        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaCliente();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:acoes_cadastradas_visualizar/'.$codigo_cliente.'/" + Math.random())
        });
            
        function atualizaListaCliente() {     
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes/listagem_acoes_cadastradas_visualizar/'.$codigo_cliente.'/'.$is_admin.'/" + Math.random()); 
        }
    });', false);
?>
