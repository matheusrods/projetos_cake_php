<div class='well'>
    <?php echo $bajax->form('ConfiguracaoComissaoCorre', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ConfiguracaoComissaoCorre', 'element_name' => 'configuracao_comissao_corretora'), 'divupdate' => '.form-configuracao_comissao_corretora')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_corretora($this,'codigo_corretora','Corretora',true,'ConfiguracaoComissaoCorre') ?>
            <?php echo $this->Buonny->input_produto_servico($this,$produtos,$servicos); ?>
            <?php echo $this->BForm->input('verificar_preco_unitario', array('label' => 'Verificar Preço Unitário','class' => 'input-small','options' => array('1' => 'Sim','2' => 'Não'),'empty' => 'Todos')) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaConfiguracaoComissaoCorretora("#lista");

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-configuracao_comissao_corretora"));
            jQuery(".form-configuracao_comissao_corretora").load(baseUrl + "/filtros/limpar/model:ConfiguracaoComissaoCorre/element_name:configuracao_comissao_corretora/" + Math.random())
        });
    });', false);

?>
