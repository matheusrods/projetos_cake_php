<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Médico Especialidades - <?php echo $profissional['Medico']['nome']; ?></h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<div>
				<span style="font-size: 1.2em">
					<b>Especialidades</b>
					<?php echo $this->BForm->input('FornecedoresMedicoEspecialidades.codigo_especialidade', array('label' => false, 'class' => 'input-large especialidade', 'options' => $especialidade, 'default' => 'Selecione','type' => 'select')) ?>
				</span>
			</div>
			<hr>
			<table class="table table-striped">
			    <thead>
			        <th class="input-xlarge">Especialidades</th>
			        <th></th>
			    </thead>
			    <?php if(!empty($medico_especialidade)):?>
			        <tbody>
			            <?php foreach($medico_especialidade as $me):?>
			                <tr>
			                    <td class="input-xlarge"><?php echo $me['Especialidade']['descricao'];?></td>
			                    
			                    <td class='action-icon'>
			                        <?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => 'excluirEspecialidade('.$me[0]['codigo_forn_med_espec'].','.$codigo_fornecedor.','.$codigo_medico.');', 'class' => 'icon-trash ', 'title' => 'Excluir Especialidade')); ?>
			                    </td>
			                </tr>
			            <?php endforeach;?>
			        </tbody>   
			    <?php else:?>
			        <tr>
			            <td colspan="3">
			                <div>Nenhum dado foi encontrado.</div>
			            </td>
			        </tr>
			    <?php endif;?>    
			</table>

		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="especialidades(<?php echo $codigo_fornecedor; ?>,<?php echo $codigo_medico; ?>, 0);"class="btn btn-danger">FECHAR</a>
				<a id="EspecialidadeOk" href="javascript:void(0);" class="btn btn-success">SALVAR</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		setup_mascaras();
		setup_datepicker();
		setup_time();


		$('#EspecialidadeOk').click(function() {
			salvar_especialidade(<?php echo $codigo_fornecedor; ?>,<?php echo $codigo_medico; ?>);
	  	});

	});

	function excluirEspecialidade(codigo, codigo_fornecedor, codigo_medico){
        if (confirm('Deseja realmente excluir ?')){
            $.ajax({
                type: 'POST',        
                url: baseUrl + 'fornecedores_medicos/excluir_fme/' + codigo +  '/' + Math.random(),        
                dataType : 'json',
                success : function(data){ 
                	especialidades(<?php echo $codigo_fornecedor; ?>,<?php echo $codigo_medico; ?>, 0);
                },
                error : function(error){
                    console.log(error);
                }
            }); 
        }
    }



	var mensagem = function(mensagem, tipo, titulo){
		
		this.tipo = tipo || 'warning'
		this.titulo = titulo || 'Atenção'

		
			return swal({
				type: this.tipo,
				title: this.titulo,
				text: mensagem
			});
		
	}



	function salvar_especialidade(codigo_fornecedor,codigo_medico) {

		//pega a data
		var codigo_especialidade = $('#FornecedoresMedicoEspecialidadesCodigoEspecialidade').val();
		
		var div = jQuery('#modal_data');
	    bloquearDiv(div);
		
		//envia via ajax a data de realizacao
		$.ajax({
			url: baseUrl + 'fornecedores_medicos/salvar_especialidade',
			type: 'POST',
			dataType: 'json',
			data: {
				"codigo_fornecedor"   		: codigo_fornecedor,
				"codigo_medico"		   		: codigo_medico,
				"codigo_especialidade"   	: codigo_especialidade,
			}

		})
		.done(function(data) {
			
			if(data.retorno == 'false') {
				swal({
					type: 'warning',
					title: 'Atenção',
					text: data.mensagem,
				});
				
				desbloquearDiv(div);

			} else {
				swal({
					type: 'success',
					title: 'Sucesso',
					text: 'Dados atualizados com sucesso.'
				});

				especialidades(codigo_fornecedor,codigo_medico, 0);
			}
		});


	}//fim function salvar_realizacao

</script>