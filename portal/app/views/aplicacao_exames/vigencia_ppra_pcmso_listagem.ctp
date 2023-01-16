<?php if(!empty($dados)): ?>
	<?php //echo $paginator->options(array('update' => 'div.lista')); ?>
	
	<div class='well'>
	    <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => 'aplicacao_exames', 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
	</div>
	<table class="table" style='max-width:none;'>
	    <thead>
	        <tr>
				<th>Código Cliente</th>
				<th>Unidade</th>
				<th>Produto</th>			
				<th>Início Vigência</th>
				<th>Periodo Vigência(meses)</th>
				<th>Vencimento</th>
				<th>Ação</th>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0 ?>
			<?php foreach($dados as $key => $value) : ?>
				<?php $total += 1 ?>
                <?php
                    $cor_background = '#ccc';
                    $cor_background = ($value[0]['status'] == 'VIGENTE' ? '#99ff99' : $cor_background);
                    $cor_background = ($value[0]['status'] == 'A VENCER' ? '#ffff99' : $cor_background);
                    $cor_background = ($value[0]['status'] == 'VENCIDO' ? '#ff9999' : $cor_background);
                    $texto = ' - ';
                ?>
				<tr style="background-color: <?=$cor_background?>" title="<?=$value[0]['status']?>">
					<td><?= $value['Cliente']['codigo'] ?></td>
					<td><?= $value['Cliente']['nome_fantasia'] ?></td>
					<td><?= utf8_encode($value['Servico']['descricao']) ?></td>
					<td> 
						<?php if (empty($value['OrdemServico']['inicio_vigencia_pcmso'])): ?>
							<?php echo $texto; ?>
						<?php else: ?>
						<?= AppModel::dbDateToDate($value['OrdemServico']['inicio_vigencia_pcmso']) ?>
						<?php endif; ?>	
					</td>
					<td>
						<?php if (empty($value['OrdemServico']['vigencia_em_meses'])): ?>
							<?php echo $texto; ?>
						<?php else: ?>
						<?= $value['OrdemServico']['vigencia_em_meses'] ?>	
						<?php endif; ?>
					</td>
					<td>
						<?php if (empty($value[0]['final_vigencia'])): ?>
							<?php echo $texto; ?>
						<?php else: ?>
							<?= AppModel::dbDateToDate($value[0]['final_vigencia']) ?>
						<?php endif; ?>							
					</td>
					<td>
						<?php //debug($value[0]['codigo_servico']); ?>
						<?php if ($value['Servico']['codigo'] == $codigo_servico_pcmso): ?>
							<?php //debug('pcmso'); ?>
							<?php  echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-print', 'data-toggle' => 'tooltip', 'title' => 'Imprimir relatório PCMSO','onclick' => 'verifica_scf_pcmso('.$value['Cliente']['codigo'].')')); ?> &nbsp;
						<?php elseif($value['Servico']['codigo'] == $codigo_servico_ppra): ?>							
							<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-print', 'data-toggle' => 'tooltip', 'title' => 'Imprimir relatório PGR','onclick' => 'alertDataVigenciaPpra('.$value['Cliente']['codigo'].','.$value['OrdemServico']['inicio_vigencia_pcmso'].')')); ?> &nbsp;
						<?php else: ?>
							<span class="icon-print opacity" data-toggle="tooltip" title="Opção indisponível">&nbsp;</span> &nbsp;
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td><?= $total ?></td>
				<td colspan="15"></td>
			</tr>
		</tfoot>
	</table>
<?php else: ?>
	<div class="alert">
        Nenhum dado encontrado.
    </div>
<?php endif;?>

<script type="text/javascript">
    $(document).ready(function() {
                
        alertDataVigenciaPpra = function(codigo_unidade, data_vigencia){
            // console.log(data_vigencia);
            if(data_vigencia == 0) {
                swal("Importante","Favor inserir a data de vigência!", "error");
                return false;
            }

            //funcao para imprimir o relatorio com setor+cargo quando não tiver funcionário o mesmo ou imprimir o seto+cargo mesmo não tendo funcionários alocados.            
            verifica_setor_cargo_funcionario(codigo_unidade);
            
            return false;


            // var url = baseUrl + "/grupos_exposicao/imprimir_relatorio/"+codigo_unidade;
            // window.location.href = url;            
        };

        /**
         * [verifica_seto_cargo_funcionario description]
         *
         * funcao para apresentar o swall com a opcao de sim/nao
         * @param  {[type]} codigo_unidade [description]
         * @return {[type]}                [description]
         */
        verifica_setor_cargo_funcionario = function(codigo_unidade) {
            
            swal({
              title: "Imprimir documento com Setor e Cargo sem Funcionários?",
              text: "Setores e Cargos sem Funcionários sair no relatório!",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-danger",
              confirmButtonText: "Sim",
              cancelButtonText: "Não",
              closeOnConfirm: true,
              closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    //sim para imprimir os setores e cargos
                    var url = baseUrl + "/grupos_exposicao/imprimir_relatorio/"+codigo_unidade+"/0";
                    window.location.href = url;                    
                } 
                else {
                    //não para não imprimir setores e cargos que não tenha funcionarios
                    var url = baseUrl + "/grupos_exposicao/imprimir_relatorio/"+codigo_unidade+"/1";
                    window.location.href = url;
                }
            });

        }//fim verifica_seto_cargo_funcionario(codigo_unidade)

        /**
         * [verifica_seto_cargo_funcionario description]
         *
         * funcao para apresentar o swall com a opcao de sim/nao
         * @param  {[type]} codigo_unidade [description]
         * @return {[type]}                [description]
         */
        verifica_scf_pcmso = function(codigo_unidade) {
            
            swal({
              title: "Imprimir documento com Setor e Cargo sem Funcionários?",
              text: "Setores e Cargos sem Funcionários sair no relatório!",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-danger",
              confirmButtonText: "Sim",
              cancelButtonText: "Não",
              closeOnConfirm: true,
              closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    //sim para imprimir os setores e cargos
                    var url = baseUrl + "/clientes_implantacao/imprimir_relatorio/"+codigo_unidade+"/0";
                    window.location.href = url;                    
                } 
                else {
                    //não para não imprimir setores e cargos que não tenha funcionarios
                    var url = baseUrl + "/clientes_implantacao/imprimir_relatorio/"+codigo_unidade+"/1";
                    window.location.href = url;
                }
            });

        }//fim verifica_seto_cargo_funcionario(codigo_unidade)

    });
</script>