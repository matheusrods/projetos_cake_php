<table class="table table-striped">
    <thead>
        <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th title="Prioridade do Alerta">Prioridade do Alerta</th>
            <th>SLA</th>
            <th>SLA Nível 2</th>
            <th>SLA Nível 3</th>
            <th title="Mostrar no Telão">Mostrar</th>            
            <th title="Prioridade da Ocorrência">Prioridade da Ocorrência</th>
            <th title="Ocorrência Automatizada">Ocorrência Automatizada</th>
            <th title="Evento Conforme">Evento Conforme</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($eventos as $evento): ?>
        <tr>
            <td><?php echo $evento['TEspaEventoSistemaPadrao']['espa_codigo'] ?></td>
            <td><?php echo $evento['TEspaEventoSistemaPadrao']['espa_descricao'] ?></td>
            <td class="numeric input-mini"><?php echo $evento['TEspaEventoSistemaPadrao']['espa_prioridade_alerta'] ?></td>
            <td><?php echo $evento['TEspaEventoSistemaPadrao']['espa_sla'] ?></td>
            <td><?php echo $evento['TEspaEventoSistemaPadrao']['espa_alerta_nivel_2'] ?></td>
            <td><?php echo $evento['TEspaEventoSistemaPadrao']['espa_alerta_nivel_3'] ?></td>
            <td><?php echo ( $evento['TEspaEventoSistemaPadrao']['espa_flag_telao'] == 'S' ? 'SIM' : 'NÃO' ) ?></td>
            <td>
                <?php                     
                    switch( $evento['TEspaEventoSistemaPadrao']['espa_tipo_ocorrencia'] ) 
                    {
                        case 1:
                            echo 'BAIXA';
                            break;
                        case 2:
                            echo 'MÉDIA';
                            break;
                        case 3:
                            echo 'ALTA';
                            break;
                        default:
                            echo 'SEM PRIORIDADE';
                    }
                ?>
            </td>
            <td><?php echo ( $evento['TEspaEventoSistemaPadrao']['espa_ocorrencia_autorizada'] == 'S' ? 'SIM' : 'NÃO' ) ?></td>
            <td><?php echo ( $evento['TEspaEventoSistemaPadrao']['espa_tipo_evento'] == 'S' ? 'SIM' : 'NÃO' ) ?></td>
            <td><?php echo $this->Html->link('', array('controller' => 'eventos_viagem', 'action' => 'editar', $evento['TEspaEventoSistemaPadrao']['espa_codigo']), array('class' => 'icon-edit', 'title' => 'editar')); ?></td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>