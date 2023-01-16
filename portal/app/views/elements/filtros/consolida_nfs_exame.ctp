<div class='well'>
     <h5><?= $this->Html->link((!empty($this->data['NotaFiscalServico']['codigo_fornecedor']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('NotaFiscalServico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'NotaFiscalServico', 'element_name' => 'consolida_nfs_exame'), 'divupdate' => '.form-procurar')) ?>

        <?php echo $this->element('nota_fiscal_servico/fields_filtros_auditoria') ?>
       
        <?php echo $this->BForm->submit('Buscar', array('id' => 'buscar','div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
    </div>
</div>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<script type="text/javascript">

    jQuery(document).ready(function(){   
        setup_time();
        setup_mascaras();
        setup_datepicker();
        atualizaListaNotaFiscalServico();

        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:NotaFiscalServico/element_name:consolida_nfs_exame/" + Math.random())
        });

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

        jQuery("#buscar").click(function(){
            atualizaListaNotaFiscalServico();
        });

        function atualizaListaNotaFiscalServico() {
                var div = jQuery("div.lista");
                bloquearDiv(div);
                div.load(baseUrl + "notas_fiscais_servico/consolida_nfs_exame_listagem/" + Math.random());
        }
    });

</script>
<?php if (!empty($this->data['NotaFiscalServico']['codigo_fornecedor'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?> 