	<style type="text/css">
	    td{
	        padding: 0px !important;    
	        vertical-align: middle !important; 
	        text-align: center !important;   
	    }
	    th{
	        text-align: center !important;   
	        padding: 0px !important;    
	    }
	    .control-group{
	        padding: 0px !important;
	        margin: 0px !important;
	    }
	    .input-hora{
	        width: 40px !important;
	        text-align: center !important;
	        border: 0px !important;
	    }
	</style>

	<h4>Grade de Capacidade de Atendimento</h4>
	<?php echo $this->BForm->create('FornecedorGradeAgenda', array('type' => 'post', 'onsubmit' => 'return false;'));?>
	
		<?php echo $this->BForm->input('ListaDePrecoProdutoServico.codigo', array('type' => 'hidden', 'value' => $codigo_lista_de_preco_produto_servico)); ?>
		<?php echo $this->BForm->input('Fornecedor.codigo', array('type' => 'hidden', 'value' => $codigo_fornecedor)); ?>
	
        <div class="row-fluid inline">
            <table class="table table-bordered" style="width: 460px;">
                <thead>
                    <tr>
                    <th></th>
                        <?php for($hora = 0; $hora<=23; $hora++): ?>
                            <th style="width: 30px !important;">
                            <?php echo str_pad($hora, 2, 0, STR_PAD_LEFT) ?>:00
                            </th>
                        <?php endfor;?>
                    </tr>
                <tbody>
                    <?php for($dia_semana = 0; $dia_semana <= 6; $dia_semana++): ?>
                    <tr>
                        <td><b><?php echo $dias_semana[$dia_semana] ?></b></td>
                        <?php for($hora=0; $hora <= 23; $hora++): ?>
	                        <?php if(isset($horas_disponiveis[$dia_semana]) && (array_key_exists($hora, $horas_disponiveis[$dia_semana]))) : ?>
		                        <td><?php echo $this->BForm->input('FornecedorCapacidadeAgenda.'.$dia_semana.'.'.$hora.'.capacidade', array('value' => $horas_disponiveis[$dia_semana][$hora], 'label' => false, 'type' => 'text', 'class' => 'input-mini just-number input-hora', 'maxlength'=> "5", 'style' => 'background: #38D159;')); ?></td>
							<?php else : ?>
								<td><?php echo $this->BForm->input( 'FornecedorCapacidadeAgenda.'.$dia_semana.'.'.$hora.'.capacidade', array('label' => false, 'type' => 'text', 'class' => 'input-mini just-number input-hora', 'style' => 'background: #C49191;', 'disabled' => 'disabled', 'maxlength'=> "5")); ?></td>
	                        <?php endif; ?>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor?>
                </tbody>
            </table>
	        <div class="form-actions">
	        	<?php echo $this->BForm->submit('Montar Agenda com os Horários!', array('div' => false, 'class' => 'btn btn-success')); ?>
	        </div>    	            
        </div>
    <?php echo $this->BForm->end() ?>
	
	<div id="quadro_agenda">
	
	</div>
 
	 <?php echo $this->Javascript->codeBlock('
		jQuery(document).ready(function(){
			$("#FornecedorGradeAgendaGeraGradeForm").submit(function(event) {
				FornecedoresCapacidadeAgenda.enviaFormGradeAjax(this);
			    event.preventDefault();
			});
			
			setup_mascaras();
		});
	'); ?>   

<?php echo $this->Buonny->link_js('fornecedores_capacidade_agenda'); ?>