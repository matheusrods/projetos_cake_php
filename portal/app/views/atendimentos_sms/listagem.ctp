<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th title="Prioridade"><?php echo $this->Paginator->sort('Prioridade', '') ?></th>
            <th title="Código SM"><?php echo $this->Paginator->sort('SM', 'codigo_sm') ?></th>
            <th title="Data Início"><?php echo $this->Paginator->sort('Data Início', 'data_inicio') ?></th>
            <th title="Data Análise"><?php echo $this->Paginator->sort('Data Análise', 'data_analise') ?></th>
            <th title="Data Fim"><?php echo $this->Paginator->sort('Data Fim', 'data_fim') ?></th>
            <th title="Operação"><?php echo $this->Paginator->sort('Operação', 'codigo_operacao') ?></th>
            <th title="Empresa"><?php echo $this->Paginator->sort('Empresa', 'ClientEmpresa.Raz_social') ?></th>
            <th title="Tipo Evento"><?php echo $this->Paginator->sort('Tipo Evento', '') ?></th>
            <th title="Status"><?php echo $this->Paginator->sort('Status', '') ?></th>
            <th title="Pronta Resposta"><?php echo $this->Paginator->sort('Pronta Resposta', '') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if( isset($atendimentos) ):?>
            <?php foreach($atendimentos as $atendimento): ?>
                <tr>
                    <td><?php echo ($atendimento[0]['codigo_prioridade'] == 3 ? '<span class="badge-empty badge badge-important" title="Prioridade Alta"></span>' : ($atendimento[0]['codigo_prioridade'] == 2 ? '<span class="badge-empty badge badge-warning" title="Prioridade Média"></span>' : ($atendimento[0]['codigo_prioridade'] == 1 ? '<span class="badge-empty badge badge-success" title="Prioridade Baixa"></span>' : ''))) ; ?></td>
                    <td><?php echo $this->Buonny->codigo_sm($atendimento[0]['codigo_sm']); ?></td>
                    <td><?php echo AppModel::dbDateToDate($atendimento[0]['data_inicio_atendimento_sm']); ?></td>
                    <td><?php echo $atendimento[0]['data_analise_atendimento_sm']; ?></td>
                    <td><?php echo $atendimento[0]['data_fim_atendimento_sm']; ?></td>
                    <td><?php echo utf8_encode($atendimento[0]['descricao']); ?></td>
                    <td><?php echo $atendimento[0]['Raz_social']; ?></td>
                    <td><?php echo $atendimento['0']['espa_descricao']; ?></td>
                    <td><?php echo $atendimento[0]['status'] = !empty($atendimento[0]['data_fim']) ? 'Finalizado': (!empty($atendimento[0]['data_encaminhado']) ? 'Encaminhado':(!empty($atendimento[0]['data_analise_atendimento_sm']) ? 'Em analise': 'Iniciado')); ?></td>
					<td><?php echo $atendimento[0]['pronta_resposta'] ? 'Sim': 'Não' ?></td>
                    <td>
                        <?php
echo $html->link('', array('controller' => 'historicos_sms', 'action' => 'adicionar_historico', $atendimento[0]['codigo_sm'], $atendimento[0]['codigo_passo_atendimento_sm'], $atendimento[0]['codigo'], 0), array(
    //'onclick' => 'return open_dialog(this, "Detalhe Atendimento", 960)', 
    'class' => ($atendimento[0]['status'] != 'Finalizado' ? 'icon-edit': 'icon-eye-open'), 'title' => 'Detalhes do Atendimento')
    );
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
       <?php endif;?>
    </tbody>
	<tfoot>
		<?php if( isset($atendimentos) ): ?>
			<tr>
				<td colspan='10'><strong>Total Pronta Resposta</strong></td>
				<td class='numeric'><?= $qtd_pronta_resposta ?></td>
			</tr>
			<tr>
				<td colspan='10'><strong>Total Buonny Sat</strong></td>
				<td class='numeric'><?= $qtd_buonny_sat ?></td>
			</tr>
		<?php  endif;?>
	</tfoot>
</table>
<?php
	if( isset($atendimentos) ) {
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
<?php echo $this->Js->writeBuffer(); ?>