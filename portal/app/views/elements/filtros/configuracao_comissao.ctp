<div class='well'>
    <?php echo $bajax->form('ConfiguracaoComissao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ConfiguracaoComissao', 'element_name' => 'configuracao_comissao'), 'divupdate' => '.form-configuracao_comissao')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_endereco_regiao', array('class' => 'input-medium', 'empty' => 'Filial', 'label' => false, 'options' => $filiais)) ?>
            <?php echo $this->BForm->input('codigo_produto_naveg', array('class' => 'input-large', 'empty' => 'Produto', 'label' => false, 'options' => $produtos)) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaConfiguracaoComissao("#lista");

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-configuracao_comissao"));
            jQuery(".form-configuracao_comissao").load(baseUrl + "/filtros/limpar/model:ConfiguracaoComissao/element_name:configuracao_comissao/" + Math.random())
        });
    });', false);

?>
