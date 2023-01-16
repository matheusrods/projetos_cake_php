<?php if(empty($filtros['RelatorioSm'])): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php elseif(empty($relatorio)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
<?php if ($this->passedArgs[0] == 'export'): 
	    header(sprintf('Content-Disposition: attachment; filename="%s"', basename('ocorrencias_de_viagens.csv')));
	    header('Pragma: no-cache');
		echo iconv('UTF-8', 'ISO-8859-1', 'SM;"Placa";"Ultima Ocorrencia";"Usuario Ocorrencia";"Data Ocorrencia"');
	    foreach($relatorio as $registro):
	        $registro = $registro[0];
	        $inicioReal = AppModel::dbDateToDate($registro['InicioReal']);
	        $fimReal = AppModel::dbDateToDate($registro['FimReal']);
	        $linha = "";
	        $linha .= '"'. $registro['SM'] . '";';
	        $linha .= '"'. (isset($registro['Placa'][0]) && ctype_alpha($registro['Placa'][0]) ? preg_replace('/(\w{3})(\d+)/i', "$1-$2", $registro['Placa']) : $registro['Chassi']) . '";';
	        $linha .= '"'. $registro['TVocoDescricao'] . '";';
	        $linha .= '"'. $registro['TVocoUsuarioAdicionou'] . '";';
	        $linha .= '"'. AppModel::dbDateToDate($registro['TVocoDataCadastro']) . '";';
			echo "\n".$linha;
        endforeach;    
?>
	<?php else: ?>
		<div class="well">
			<?php if(!empty($cliente)): ?>
				<strong>Código: </strong><?= $cliente['cliente']['Cliente']['codigo'] ?>
	    		<strong>Cliente: </strong><?= $cliente['cliente']['Cliente']['razao_social'] ?>
			<?php endif; ?>
			<strong>Última atualização:</strong> <?php echo date('d/m/Y H:i:s') ?> 
			<span class="pull-right">
				<?php echo $html->link('Atualizar', 'javascript:atualizaListaRelatorioSmOcorrenciaViagensAnalitico();') ?>
				<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export', $status_alvo, $alvo), array('escape' => false, 'title' =>'Exportar para Excel'));?>
			</span>
		</div>
		<?php 
		    echo $paginator->options(array('update' => 'div.lista')); 
		?>
		<div class='row-fluid' style='overflow-x:auto'>
	        <table class='table table-striped horizontal-scroll' style='width:1200px;max-width:none;'>
			    <thead>
			        <tr>
			            <th>SM</th>
			            <th>Placa</th>
			            <th>Ultima Ocorrencia</th>
			            <th class="input-medium">Usuario Ocorrencia</th>
			            <th class="input-medium">Data Ocorrencia</th>
			        </tr>
			    </thead>
			    <tbody>
			        <?php foreach($relatorio as $registro): ?>
			        
			        <?php $registro = $registro[0]; ?>

			        <?php $inicioReal = AppModel::dbDateToDate($registro['InicioPrevisto']); ?>
			        <?php $fimReal = empty($registro['FimReal']) ? date('d/m/Y H:i:s') : AppModel::dbDateToDate($registro['FimReal']); ?>
			        <tr>
			            <td><?php echo $this->Buonny->codigo_sm($registro['SM']); ?></td>
			            <td><?php echo isset($registro['Placa'][0]) && ctype_alpha($registro['Placa'][0])
			                ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $registro['Placa']), $inicioReal, $fimReal)
			                : $registro['Chassi'];
			            ?></td>
			            <td title="<?php echo $registro['TVocoDescricao'] ?>"><?php echo $this->Buonny->truncate($registro['TVocoDescricao'], 100); ?></td>
			            <td><?php echo $registro['TVocoUsuarioAdicionou']; ?></td>
			            <td><?php echo AppModel::dbDateToDate($registro['TVocoDataCadastro']); ?></td>
			        </tr>
			        <?php endforeach; ?>        
			    </tbody>
			    <tfoot>
                <tr>
                    <td class='numeric'><?php echo $this->Paginator->counter(array('format' => '%count%')); ?></td>
                    <td colspan="19"></td>
                </tr>
            </tfoot>
			</table>
		</div>
		<div class='row-fluid'>
			<div class='numbers span6'>
				<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
			  <?php echo $this->Paginator->numbers(); ?>
				<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
			</div>
			<div class='counter span6'>
				<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
			</div>
		</div>
		<?php echo $this->Buonny->link_css('jquery.tablescroll'); ?>
		<?php echo $this->Buonny->link_js('jquery.tablescroll'); ?>
		<?php echo $this->Javascript->codeBlock("
		    jQuery(document).ready(function(){
		        $('.horizontal-scroll').tableScroll({width:1200, height:(window.innerHeight-".($tipo_view != 'popup' ? "380" : "220").")});

				$('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.lista')); });
		    });", false);
		?>
		<?php if($this->layout != 'new_window'): ?>
			<?php echo $this->Js->writeBuffer(); ?>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
