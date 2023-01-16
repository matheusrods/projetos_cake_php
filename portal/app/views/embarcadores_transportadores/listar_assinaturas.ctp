<?php if (count($dados)): ?>
<div style="margin-bottom:20px;">
    <h4>Liberação: Embarcador / Transportador</h4>
</div>
<table class='table cliente-produto'>
    <thead>
        <th class='input-medium'>Embarcador</th>
        <th class='input-medium'>Transportador</th>
        <th class='input-mini'>Produto</th>
        <th class='input-medium'>Pagador</th>
        <th class='input-mini'>Status</th>
    </thead>
    <tbody>
		<?php foreach($dados as $assinatura): ?>
			<?php 
                $pattern = array(
                    '/(.*inativ.*)/i',
                	'/(.*pend.+ncia.*)/i',
                	'/(.*desatualizad.*)/i',
                );
                $replacement = array(
                    'INATIVO',
                	'PENDÊNCIA FIN.',
                	'DESATUALIZADO',
                );
                $motivo_bloqueio = preg_replace($pattern, $replacement, $assinatura['MotivoBloqueio']['descricao']);
                switch ($motivo_bloqueio) {
                    case 'OK':
                        $class_motivo_bloqueio = 'label label-success';
                        break;
                    case 'DESATUALIZADO':
                        $class_motivo_bloqueio = 'label label-warning';
                        break;
                    case 'PENDÊNCIA FIN.':
                        $class_motivo_bloqueio = 'label label-important';
                        break;
                    case 'INATIVO':
                    default:
                        $class_motivo_bloqueio = 'label';
                        break;
                }
            ?>
			<tr>
                <td><?php echo $assinatura['EmbarcadorTransportador']['codigo_cliente_embarcador'].' - '.
                $assinatura[0]['nome_embarcador'] ?>

                </td>
                <td><?php echo $assinatura['EmbarcadorTransportador']['codigo_cliente_transportador'].' - '.
                $assinatura[0]['nome_transportador'] ?></td>
                <td><?php echo $assinatura['Produto']['descricao'] ?></td>
                <td><?php echo $assinatura['ClienteProdutoPagador']['codigo_cliente_pagador'].' - '.
                $assinatura[0]['nome_pagador'] ?></td>
                <td>
                	<span style="margin-bottom:5px;" title="<?= $assinatura['MotivoBloqueio']['descricao'] ?>" class="<?= $class_motivo_bloqueio ?>"><?= $motivo_bloqueio ?></span>
                </td>
            </tr>
		<?php endforeach ?>
    </tbody>
</table>
<?php endif ?>