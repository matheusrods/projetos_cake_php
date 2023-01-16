<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('AnexoDigitalizacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AnexoDigitalizacao', 'element_name' => 'digitalizacao_terceiros'), 'divupdate' => '.form-procurar')) ?>

      <?php echo $this->element('tipo_digitalizacao/fields_filtros_digitalizacao_terceiros') ?>
      
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-digitalizacao', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
   
        setup_datepicker();

        atualizaListaDigitalizacao();

        jQuery("#limpar-filtro-digitalizacao").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AnexoDigitalizacao/element_name:digitalizacao_terceiros/" + Math.random())
        });

        function atualizaListaDigitalizacao(){
            //verifica se existe algum codigo para pesquisar
            if($("#AnexoDigitalizacaoCodigoCliente").val() != "") {
                var div = jQuery("div.lista");
                bloquearDiv(div);
                div.load(baseUrl + "tipo_digitalizacao/lista_digitalizacao_terceiros/" + Math.random());
            }
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