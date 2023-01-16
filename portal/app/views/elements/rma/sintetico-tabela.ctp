<?php
	switch ($agrupamento) {
		case TOrmaOcorrenciaRma::AGRP_TRANSPORTADOR:
			$label = 'Transportador';
			break;
        case TOrmaOcorrenciaRma::AGRP_GERADOR:
            $label = 'Gerador';
            break;
        case TOrmaOcorrenciaRma::AGRP_TIPO:
            $label = 'Tipo';
            break;
        case TOrmaOcorrenciaRma::AGRP_MOTORISTA:
            $label = 'Motorista';
            break;
        case TOrmaOcorrenciaRma::AGRP_EMBARCADOR:
            $label = 'Embarcador';
            break;
        case TOrmaOcorrenciaRma::AGRP_ALVO_ORIGEM:
            $label = 'Alvo Origem';
            break;            
	}
?>
<table class='table table-striped tablesorter'>
	<thead>
		<tr>
			<th><?= $this->Html->link($label, 'javascript:void(0)') ?></th>
            <th class='numeric'><?= $this->Html->link('Informativo', 'javascript:void(0)') ?></th>
            <th class='numeric'><?= $this->Html->link('Médio', 'javascript:void(0)') ?></th>
            <th class='numeric'><?= $this->Html->link('Grave', 'javascript:void(0)') ?></th>
			<th class='numeric'><?= $this->Html->link('Quantidade', 'javascript:void(0)') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
            $total_ocorrencias = 0;
            $total_informativo = 0;
            $total_medio = 0;
            $total_grave = 0;
        ?>
        <?php foreach ($dados as $dado): ?>
            <?php 
            if($agrupamento == TOrmaOcorrenciaRma::AGRP_EMBARCADOR || $agrupamento == TOrmaOcorrenciaRma::AGRP_TRANSPORTADOR ) {
                $codigo_selecionado = empty($dado[0]['codigo']) ? -1 : DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny($dado[0]['codigo']);
            }else {
                $codigo_selecionado = empty($dado[0]['codigo']) ? -1 : $dado[0]['codigo'];
            }

            ?>
			<?php 
                $total_ocorrencias += $dado[0]['qtd_ocorrencias'] ;
                $total_informativo += $dado[0]['informativo'] ;
                $total_medio += $dado[0]['medio'] ;
                $total_grave += $dado[0]['grave'] ;
            ?>
			<tr title="<?php echo $codigo_selecionado ?>">
				<td><?= (!empty($dado[0]['descricao']) ? $dado[0]['descricao'] : 'Sem '.$label ) ?></td>
                <td class='numeric'><?= $this->Html->link($this->Buonny->moeda($dado[0]['informativo'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', 'I')") ?></td>
                <td class='numeric'><?= $this->Html->link($this->Buonny->moeda($dado[0]['medio'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', 'M')") ?></td>
                <td class='numeric'><?= $this->Html->link($this->Buonny->moeda($dado[0]['grave'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', 'G')") ?></td>
				<td class='numeric'><?= $this->Html->link($this->Buonny->moeda($dado[0]['qtd_ocorrencias'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', '')") ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td></td>
            <td class='numeric'><?= $this->Html->link($this->Buonny->moeda($total_informativo, array('nozero' => true, 'places' => 0)), "javascript:analitico('', 'I')") ?></td>
            <td class='numeric'><?= $this->Html->link($this->Buonny->moeda($total_medio, array('nozero' => true, 'places' => 0)), "javascript:analitico('', 'M')") ?></td>
            <td class='numeric'><?= $this->Html->link($this->Buonny->moeda($total_grave, array('nozero' => true, 'places' => 0)), "javascript:analitico('', 'G')") ?></td>
			<td class='numeric'><?= $this->Html->link($this->Buonny->moeda($total_ocorrencias, array('nozero' => true, 'places' => 0)), "javascript:analitico('', '')") ?></td>
        </tr>
	</tfoot>
</table>
<?php echo $this->Javascript->codeBlock("
function analitico(codigo_selecionado, grau_risco) {
	var agrupamento = {$agrupamento};
	var form = document.createElement('form');
    var form_id = ('formresult' + Math.random()).replace('.','');
    form.setAttribute('method', 'post');
    form.setAttribute('action', '/portal/rma/analitico/1');
    form.setAttribute('target', form_id);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][data_inicial]');
    field.setAttribute('value', '{$this->data['TOrmaOcorrenciaRma']['data_inicial']}');
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][data_final]');
    field.setAttribute('value', '{$this->data['TOrmaOcorrenciaRma']['data_final']}');
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][codigo_cliente]');
    field.setAttribute('value', '{$this->data['TOrmaOcorrenciaRma']['codigo_cliente']}');
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][grma_codigo]');
    field.setAttribute('value', (agrupamento == 1 ? codigo_selecionado : '{$this->data['TOrmaOcorrenciaRma']['grma_codigo']}'));
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][trma_codigo]');
    field.setAttribute('value', (agrupamento == 2 ? codigo_selecionado : '{$this->data['TOrmaOcorrenciaRma']['trma_codigo']}'));
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][codigo_transportador]');
    field.setAttribute('value', (agrupamento == 3 ? codigo_selecionado : '{$this->data['TOrmaOcorrenciaRma']['codigo_transportador']}'));
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][pfis_cpf]');
    field.setAttribute('value', (agrupamento == 4 ? codigo_selecionado : '{$this->data['TOrmaOcorrenciaRma']['pfis_cpf']}'));
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][codigo_embarcador]');
    field.setAttribute('value', (agrupamento == 5 ? codigo_selecionado : '{$this->data['TOrmaOcorrenciaRma']['codigo_embarcador']}'));
    field.setAttribute('type', 'hidden');
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][cd_id]');
    field.setAttribute('value', (agrupamento == 6 ? codigo_selecionado : ''));
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][cd_id_agrupado]');
    field.setAttribute('value', (agrupamento == 6 ? '' : '{$this->data['TOrmaOcorrenciaRma']['cd_id_agrupado']}'));
    field.setAttribute('type', 'hidden');
    form.appendChild(field);

    field = document.createElement('input');
    field.setAttribute('name', 'data[TOrmaOcorrenciaRma][trma_prioridade]');
    field.setAttribute('value', (grau_risco != '' ? grau_risco : '{$this->data['TOrmaOcorrenciaRma']['trma_prioridade']}'));
    field.setAttribute('type', 'hidden');
    form.appendChild(field);

    document.body.appendChild(form);
    var janela = window_sizes();
    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    form.submit();
};
$(document).ready(function() {
	
    $.tablesorter.addParser({
        debug:true,
        id: 'qtd', 
        is: function(s) { 
            return false;
        },
        format: function(s) { 
           return $.tablesorter.formatInt(s.replace('.', '').replace(new RegExp(/\(\d*\)/g),''));
        }, 
        type: 'numeric'
    });
    jQuery('table.tablesorter').tablesorter({
        headers: {
            1: {sorter: 'qtd'},
            2: {sorter: 'qtd'},
            3: {sorter: 'qtd'},
            4: {sorter: 'qtd'}
        },
        widgets: ['zebra']
    });

})") ?>