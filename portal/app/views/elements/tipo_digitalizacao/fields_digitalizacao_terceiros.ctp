<div class="well">
	<div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente_com_label($this); ?>
            <?php echo $this->Buonny->input_unidades_tela_digitalizacao($this,"AnexoDigitalizacao",$unidades); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('nome', array('label' => 'Nome documento', 'class' => 'input-xlarge', 'type' => 'text'));?>
		<?php echo $this->BForm->input('codigo_tipo_digitalizacao', array('label' => 'Tipo de digitalização', 'placeholder' => 'Tipo de digitalização', 'class' => 'input-small', 'options' => $tipos_digitalizacao, 'empty' => 'Todos')); ?>
	</div>
	<div class='row-fluid inline'>
	    <?php echo $this->BForm->input('validade', array('label' => 'Data de validade', 'placeholder' => false,'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
	</div>
</div>
<div class="well">
	<b>Upload do documento</b>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('caminho_arquivo', array('type'=>'file', 'label' => false)); ?>
    	<?php echo $this->BForm->button('Limpar campo do anexo', array('type'=>'button', 'label' => 'Limpar', 'id' => 'LimparArquivoDigitalizacao', 'class' => 'btn btn-anexos')); ?>
	</div>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'id' => 'submit_digitalizacao')); ?>
	<?= $html->link('Voltar', array('controller' => 'tipo_digitalizacao', 'action' => 'operacao_digitalizacao_terceiros'), array('class' => 'btn')); ?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		setup_mascaras(); 
		setup_datepicker();

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

	$("#LimparArquivoDigitalizacao").click(function(){
        $("#AnexoDigitalizacaoCaminhoArquivo").val("");                
    });

    $('#AnexoDigitalizacaoCaminhoArquivo').bind('change', function() {
    	var filesize = this.files[0].size / 1024 / 1024; //obter o tamanho do arquivo
    	if (filesize > 5) { //se arquivo for maior que 5MB, barrar
    		swal("Importante","Tamanho máximo excedido! Só é permitido arquivos de até 5MB", "error");
    		$("#AnexoDigitalizacaoCaminhoArquivo").val("");
            return false;
    	}
    });

    var validos = /(\.jpg|\.png|\.jpeg|\.pdf)$/i;

	$("#AnexoDigitalizacaoCaminhoArquivo").change(function() {
		var fileInput = $(this);
		var nome = fileInput.get(0).files["0"].name;
		if (!validos.test(nome)) {
			swal("Importante","Arquivo inválido! É aceito extensões pdf, jpg, jpeg ou png. Por favor tente novamente.", "error");
			$("#AnexoDigitalizacaoCaminhoArquivo").val("");
			return false;
		}
	});
</script>