<?php $filtrado = (isset($this->data['PropostaLimiteDesconto']) ? true : false)?>
<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('PropostaLimiteDesconto', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PropostaLimiteDesconto', 'element_name' => 'proposta_limites'), 'divupdate' => '.form-procurar')) ?>
        <div class='row-fluid inline'>    
            <?php echo $this->BForm->input("codigo_produto", array('class'=>'input-large','options' => $produtos,'label' => 'Produto','empty'=>'Produto')) ?>
            <?php echo $this->BForm->input("codigo_servico", array('class'=>'input-large','options' => $servicos,'label' => 'Serviço','empty'=>'Serviço')) ?>
        </div>
        <div class='row-fluid inline'>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>
        <?php echo $this->BForm->end() ?>
    </div>    
</div>
<?php echo $this->Javascript->codeBlock('


    $(document).ready(function(){
        setup_mascaras();   
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "proposta_limites/listagem/" + Math.random());':'').'

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PropostaLimiteDesconto/element_name:proposta_limites/" + Math.random())
        });

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });   
        
        jQuery("#PropostaLimiteDescontoCodigoProduto").change(function(){
            var codigo_produto = $(this).val();
            lista_servicos_produto("PropostaLimiteDescontoCodigoServico",codigo_produto);
        });   

    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>