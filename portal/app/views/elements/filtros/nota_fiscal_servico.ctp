<div class='well'>
    <h5><?= $this->Html->link((!empty($this->data['NotaFiscalServico']['codigo_cliente']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('NotaFiscalServico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'NotaFiscalServico', 'element_name' => 'nota_fiscal_servico'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->BForm->hidden('periodo_nota')?>

        <?php echo $this->element('nota_fiscal_servico/fields_filtros') ?>

         <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_time();
        setup_mascaras();
        setup_datepicker();

        atualizaLista();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:NotaFiscalServico/element_name:nota_fiscal_servico/" + Math.random())
        });

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "notas_fiscais_servico/listagem/" + Math.random());
        }

        $(".datepickerjs").datepicker({
            dateFormat: "dd/mm/yy",
            showOn : "button",
            buttonImage : baseUrl + "img/calendar.gif",
            buttonImageOnly : true,
            buttonText : "Escolha uma data",
            dayNames : ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sabado"],
            dayNamesShort : ["Dom","Seg","Ter","Qua","Qui","Sex","Sab"],
            dayNamesMin : ["D","S","T","Q","Q","S","S"],
            monthNames : ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
            monthNamesShort : ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"],
            onClose : function() {
            }
        }).mask("99/99/9999");        
    });', false);
?>
<?php if (!empty($this->data['NotaFiscalServico']['codigo_cliente'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>