<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('FichaClinica', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaClinica', 'element_name' => 'fichas_clinicas_terceiros'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('fichas_clinicas/fields_filtros_terceiros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){

        setup_datepicker();

        $("#buscar").click(function(){  
            if($("#FichaClinicaCodigoCliente").val() == 0){              
                $("#FichaClinicaCodigoCliente").css({borderColor: "red"});    
                return false; 
            }else{
                $("form").submit(function(){                
                    atualizaListaFichasClinicas();             
                });
            }
        });
        

        if( $("#FichaClinicaCodigoCliente").val() != "" ){
            atualizaListaFichasClinicas();
        }
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaClinica/element_name:fichas_clinicas_terceiros/" + Math.random())

            atualizaListaFichasClinicas();
        });

        function atualizaListaFichasClinicas() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
			div.load(baseUrl + "fichas_clinicas/lista_fichas_clinicas_terceiros/" + Math.random());
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