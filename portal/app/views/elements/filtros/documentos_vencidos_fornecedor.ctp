<div class='well'>
    <?php echo $bajax->form('Consulta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Consulta', 'element_name' => 'documentos_vencidos_fornecedor',), 'divupdate' => '.form-procurar')) ?>
    <h5><?= $this->Html->link((!empty($this->data['Consulta']['codigo_fornecedor']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		<?php echo $this->element('consultas/filtros_documentos_vencidos_fornecedor') ?>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	</div>
    <?php echo $this->BForm->end() ?>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
	function buscaCidade(element) {
	    var idEstado = $(element).val();
	    $.ajax({
	        type: "POST",
	        url: "/portal/enderecos/carrega_combo_cidade/" + idEstado,
	        dataType: "html",
	        beforeSend: function() { 
	            $("#cidade_combo").hide();
	            $("#carregando_cidade").show();
	        },
	        success: function(retorno) {
	            $("#ConsultaCidade").html(retorno);
	        },
	        complete: function() { 
	            $("#carregando_cidade").hide();
	            $("#cidade_combo").show();
	        }
	    });
	}

	jQuery(document).ready(function(){
	    atualizaLista();
	    jQuery("#limpar-filtro").click(function(){
	        bloquearDiv(jQuery(".form-procurar"));
	        jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Consulta/element_name:documentos_vencidos_fornecedor/" + Math.random())
	         jQuery("div#data_periodo").hide();
	    });

	    function atualizaLista() {
	        var div = jQuery("div.lista");
	        bloquearDiv(div);
	        div.load(baseUrl + "consultas/listagem_documentos_vencidos_fornecedor/" + Math.random());
	    }

	    jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

        jQuery(\'#ConsultaSituacaoAV\').on("click", function(){
	        if(jQuery(this).is(\':checked\')){
	            jQuery(\'div#data_periodo\').show();
	        }else{
	            jQuery(\'div#data_periodo\').hide();
	        }
    	});
        
	    jQuery(\'#ConsultaDataInicio\').on("change", function(){
	        if(jQuery(\'#ConsultaSituacaoAV\').is(\':checked\')){
	            if(moment().diff(moment(this.value, [\'DD/MM/YYYY\', \'YYYY-MM-DD\'], true), "days") > 0){
	                swal(\'ATENÇÃO!\', \'A data de inicio do à vencer, não pode ser menor que a data atual!\', \'warning\');
	                this.value = jQuery(this).attr(\'oldvalue\');
	            }else{
	                jQuery(this).attr(\'oldvalue\', this.value);
	            }
	        }
	    })

	    $("#ConsultaSituacaoAV").on("change", function() {
	    	if(jQuery(this).is(\':checked\')){
	            jQuery(\'div#data_periodo\').show();
	        }else{
	            jQuery(\'div#data_periodo\').hide();
	        }
	    });

	    //fica em background
	    jQuery(\'div#data_periodo\').hide();

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
	});
', false); ?>

<?php if (!empty($this->data['Consulta']['codigo_fornecedor'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})'); ?>
<?php endif; ?>
<?php if(!empty($this->data['Consulta']['situacao']) && is_array($this->data['Consulta']['situacao']) && in_array('AV', $this->data['Consulta']['situacao'])): ?>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#data_periodo").show()})'); ?>
<?php endif; ?>