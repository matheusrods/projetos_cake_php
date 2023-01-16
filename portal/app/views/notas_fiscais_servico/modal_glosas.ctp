<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">

		<div class="modal-header"><h3>Incluir Glosa</h3></div>
	
		<div class="modal-body" style="max-height: 400px; font-size: 15px;">
			<div class="row-fluid inline">
				<?php echo $this->BForm->create('NotaFiscalServico',array('url' => array('controller' => 'notas_fiscais_servico', 'action' => 'modal_glosas'), 'enctype' => 'multipart/form-data')); ?>
					<?php echo $this->BForm->input('codigo_glosa', array('type' => 'hidden', 'value' => '')) ?>
					<?php echo $this->BForm->input('fornecedor', array('type' => 'hidden', 'value' => $codigo_fornecedor)) ?>
					<?php echo $this->BForm->input('codigo_nota', array('type' => 'hidden', 'value' => $codigo_nota)) ?>

					<?php echo $this->BForm->input('codigo_fornecedor', array( 'value' =>  $codigo_fornecedor, 'class' => 'input-small', 'label' => 'Código credenciado', 'type' => 'text', 'readonly' => (!empty($codigo_fornecedor) ? 'readonly' : ''))); ?>
					<?php echo $this->BForm->input('numero_nfs', array( 'value' =>  $numero_nfs, 'class' => 'input-small', 'label' => 'Número NFS', 'type' => 'text', 'readonly' => 'readonly')); ?>
			   

					<?php echo $this->BForm->input('codigo_itens_pedidos_exames', array('list'=>"exames", 'label' => 'Exame', 'class' => 'input-xlarge', 'empty' => 'Exames', 'placeholder' => 'Exames')); ?>
					<datalist id="exames">
						<?php foreach ($dados_glosa as $codigo_exame => $dado): ?>
							<option id="<?php echo $codigo_exame ?>" value="<?php echo $dado['descricao'] ?>"><?php echo $dado['nome'] ?></option>						
						<?php endforeach ?>
					</datalist>

			    	<?= $this->BForm->input('valor', array('label' => 'Valor', 'class' => 'input-medium numeric moeda', 'maxlength' => 14)); ?>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('data_glosa', array('label' => 'Data da Glosa', 'type' => 'text', 'class' => 'datepickerjs date input-small form-control', 'multiple')); ?>
				<?php echo $this->BForm->input('data_vencimento', array('value' => (!empty($nfs['NotaFiscalServico']['data_vencimento']) ? $nfs['NotaFiscalServico']['data_vencimento'] : ''), 'label' => 'Data de vencimento', 'type' => 'text', 'class' => 'datepickerjs date input-small form-control', 'multiple')); ?>
				<?php echo $this->BForm->input('data_pagamento', array('label' => 'Data de Pagamento', 'type' => 'text', 'class' => 'datepickerjs date input-small form-control', 'multiple')); ?>
				<?php echo $this->BForm->input('codigo_status_glosa', array('label' => 'Status', 'class' => 'input-medium', 'default' => '','empty' => 'Status', 'options' => $glosas_status)); ?>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('motivo_glosa', array('label' => 'Motivo', 'class' => 'input-xlarge', 'empty' => 'Motivo','options' => $tipos_glosas)); ?>
			</div>
				<?php echo $this->BForm->end(); ?>
		</div>

		<div class="modal-footer">
	    	<div class="right">
	        	<a id="InserirDadosGlosa" onclick="salvar_dados_glosas()" href="javascript:void(0);" class="btn btn-primary">SALVAR</a>
			    <a href="javascript:void(0);" onclick="modal_glosas(0);" class="btn btn-danger">FECHAR</a>
	    	</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();

	var mensagem = function(mensagem, tipo, titulo){
	
		this.tipo = tipo || 'warning'
		this.titulo = titulo || 'Atenção'
		
		return swal({
			type: this.tipo,
			title: this.titulo,
			text: mensagem
		});	
	}

	function isValidDate(d) {
  		return d instanceof Date && !isNaN(d);
	}

	 $('.datepickerjs').datepicker({
        dateFormat: 'dd/mm/yy',
        showOn : 'button',
        buttonImage : baseUrl + 'img/calendar.gif',
        buttonImageOnly : true,
        buttonText : 'Escolha uma data',
        dayNames : ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sabado'],
        dayNamesShort : ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
        dayNamesMin : ['D','S','T','Q','Q','S','S'],
        monthNames : ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort : ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        onClose : function() {
        }
    }).mask('99/99/9999');

	modal_glosas = function(mostra){
        if(mostra == 1){
            $('#modal_glosas').css('z-index', '1050');
            $('#modal_glosas').modal('show');
        } else {
            $('#modal_glosas').css('z-index', '-1');
            $('#modal_glosas').modal('hide');
        }
    }	
    modal_glosas(1);

});

	//metodo para pegar a listagem das glosas
	function atualizaListaModalGlosas(){
        	
    	var codigo_fornecedor = $('#NotaFiscalServicoFornecedor').val();
    	var codigo_nota = $('#NotaFiscalServicoCodigoNota').val();

    	var div = jQuery('#tableGlosas');
    	bloquearDiv(div);

    	$.ajax({
            url: baseUrl + 'notas_fiscais_servico/listagem_glosas',
            type: 'POST',
            dataType: 'html',
            data: {
                'codigo_fornecedor'             : codigo_fornecedor,
                'codigo_nota_fiscal_servico'    : codigo_nota
            }
        })
        .done(function(data) {
        	desbloquearDiv(div);
			$('#tableGlosas').html(data);

        }).fail(function (jqXHR, textStatus){
            alert(textStatus);
        });


    }//fim atualizaListaModaGlosas
	


	function salvar_dados_glosas() {
		var retorno = true;
		var codigo_glosa 		  = $('#NotaFiscalServicoCodigoGlosa').val();
		var exame		  		  = $("#exames option[value='" + $('#NotaFiscalServicoCodigoItensPedidosExames').val() + "']").attr('id');
		var valor 				  = $('#NotaFiscalServicoValor').val();
		var data_glosa  		  = $('#NotaFiscalServicoDataGlosa').val();
		var data_vencimento       = $('#NotaFiscalServicoDataVencimento').val();
		var data_pagamento        = $('#NotaFiscalServicoDataPagamento').val();
		var status                = $('#NotaFiscalServicoCodigoStatusGlosa').val();
		var motivo_glosa          = $('#NotaFiscalServicoMotivoGlosa').val();
		var codigo_fornecedor     = $('#NotaFiscalServicoFornecedor').val();
		var codigo_nota     	  = $('#NotaFiscalServicoCodigoNota').val();

		if(!motivo_glosa){
			swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Selecione um motivo!',
				});
			retorno = false;
		}
		if(!status){
			swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Selecione um status!',
				});
			retorno = false;
		}


		if (retorno == true){
			var div = jQuery('#modal_data');
    		bloquearDiv(div);
			//envia via ajax os dados glosas
			$.ajax({
				url: baseUrl + 'notas_fiscais_servico/salvar_dados_glosas',
				type: 'POST',
				dataType: 'json',
				data: {
					"codigo_glosa"			   		: codigo_glosa,
					"codigo_itens_pedidos_exames"   : exame,
					"valor"							: valor,
					"data_glosa"   					: data_glosa,
					"data_vencimento"   			: data_vencimento,
					"data_pagamento"   				: data_pagamento,
					"codigo_status_glosa"   		: status,
					"motivo_glosa"   				: motivo_glosa,
					"codigo_fornecedor"   			: codigo_fornecedor,
					"codigo_nota_fiscal_servico"	: codigo_nota,
				}
			})		
			.done(function(data) {			
				if(data.return == 0) {
					swal({
						type: 'warning',
						title: 'Atenção',
						text: data.mensagem,
					});
				} else { 	
					swal({
						type: 'success',
						title: 'Sucesso',
						text: 'Glosa cadastrada com sucesso.'
					});
					glosas();
					location.reload();
				}
				desbloquearDiv(div);
			});
		}
	
		
	}
</script>
