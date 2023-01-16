<div class="listagem">
<?php if ($this->passedArgs[0] != 'export'): ?>
    <div class='well'>
        <span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
        </span>
    </div>

<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo $this->Paginator->sort('SM', 'codigo_sm') ?></th>
            <th><?php echo $this->Paginator->sort('Data Início', 'data_inicio') ?></th>
            <th><?php echo $this->Paginator->sort('Data Análise', 'data_analise') ?></th>
            <th><?php echo $this->Paginator->sort('Data Fim', 'data_fim') ?></th>
            <th><?php echo $this->Paginator->sort('Operação', 'codigo_operacao') ?></th>
            <th><?php echo $this->Paginator->sort('Empresa', 'ClientEmpresa.Raz_social') ?></th>
            <th><?php echo $this->Paginator->sort('Tipo Evento', '') ?></th>
            <th><?php echo $this->Paginator->sort('Status', '') ?></th>
            <th><?php echo $this->Paginator->sort('Pronta Resposta', '') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if( isset($atendimentos) ): 
        ?>
            <?php foreach($atendimentos as $atendimento): ?>
                <tr>
                    <td><?php echo $this->Buonny->codigo_sm($atendimento['0']['codigo_sm']); ?></td>
                    <td><?php echo AppModel::dbDateToDate($atendimento['0']['data_inicio_atendimento_sm']); ?></td>
                    <td><?php echo AppModel::dbDateToDate($atendimento['0']['data_analise_atendimento_sm']); ?></td>
                    <td><?php echo AppModel::dbDateToDate($atendimento['0']['data_fim_passo_atendimento']); ?></td>
                    <td><?php echo utf8_encode($atendimento['0']['descricao']); ?></td>
                    <td><?php echo $atendimento['0']['Raz_social']; ?></td>
                    <td><?php echo $atendimento['0']['espa_descricao']; ?></td>
                    <td><?php echo $atendimento['0']['status'] = !empty($atendimento['0']['data_fim_passo_atendimento']) ? 'Finalizado': (!empty($atendimento['0']['data_encaminhado']) ? 'Encaminhado':(!empty($atendimento['0']['data_analise_atendimento_sm']) ? 'Em analise': 'Iniciado')); ?></td>
					<td><?php echo $atendimento['0']['pronta_resposta'] ? 'Sim': 'Não' ?></td>
                    <td>
                        <?php
                            echo $html->link('', array(
                                    'controller' => 'historicos_sms', 
                                    'action' => 'adicionar_historico', 
                                    $atendimento['0']['codigo_sm'], 
                                    $atendimento[0]['codigo_passo_atendimento_sm'], 
                                    $atendimento['0']['codigo_atendimento_sm'],1,1
                                ), 
                                array('onclick' =>'return open_popup(this)','class' => 'icon-eye-open', 'title' => 'Histórico do Atendimento'));
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
       <?php  endif;?>
    </tbody>
	<tfoot>
		<?php if( isset($atendimentos) ): ?>
			<tr>
				<td colspan='9'><strong>Total Pronta Resposta</strong></td>
				<td class='numeric'><?php echo $qtd_pronta_resposta ?></td>
			</tr>
			<tr>
				<td colspan='9'><strong>Total Buonny Sat</strong></td>
				<td class='numeric'><?php echo $qtd_buonny_sat ?></td>
			</tr>
		<?php  endif;?>
	</tfoot>
</table>
<div class="ocultar">
	<?php
		if( isset($atendimentos) ){
			echo $this->Paginator->prev('« Anterior ', null, null, array('class' => 'disabled'));
			echo $this->Paginator->numbers();
			echo $this->Paginator->next(' Proximo » ', null, null, array('class' => 'disabled'));

			if(isset($this->Paginator->params['paging']['AtendimentoSm']['count']))
				$total_sms = $this->Paginator->params['paging']['AtendimentoSm']['count'];
			else
				$total_sms = 0;

			if(isset($this->Paginator->params['paging']['AtendimentoSm']['pageCount']))
				$total_paginas = $this->Paginator->params['paging']['AtendimentoSm']['pageCount'];
			else
				$total_paginas = 0;
			echo $this->Paginator->counter(array('format' => 'Página %page% de '.preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/",".",$total_paginas).', mostrando %current% registros de um total de ' . preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/",".",$total_sms) ));
		}
	?>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?php endif; ?>
</div>