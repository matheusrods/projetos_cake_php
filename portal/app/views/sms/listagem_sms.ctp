<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<?php if( isset($dados) && !empty($dados) ): ?>    
    <table class='table table-striped'>
        <thead>
            <th class="input-medium">Data do Inclusão</th>
            <th class="input-medium">Liberar em</th>
            <th class="input-medium">Data do Envio</th>
            <th class="input-medium">Para</th>
            <th>Conteúdo</th>            
            <th>Modem</th>
            <th>Origem</th>
        </thead>
        <tbody>           
            <?php foreach($dados as $value): ?>
                <tr>
                    <td><?php echo $value['SmsOutbox']['data_inclusao'] ?></td>
                    <td><?php echo substr($value['SmsOutbox']['liberar_envio_em'], 0 , 10); ?></td>
                    <td><?php echo $value['SmsOutbox']['data_envio'] ?></td>
                    <td><?php echo COMUM::formatarTelefone($value['SmsOutbox']['fone_para']); ?></td>
                    <td><?php echo $value['SmsOutbox']['mensagem'] ?></td>                    
                    <td><?php                      
                    switch ($value['SmsOutbox']['fone_de']) {
                      case '1': $modem = 'Modem 1'; break;
                      case '2': $modem = 'Modem 2'; break;
                      case '3': $modem = 'Modem 3'; break;
                      case '4': $modem = 'Modem 4'; break;
                      default: $modem = 'Qualquer'; break;
                    }
                    echo $modem;  ?></td>                    
                    <td><?php echo $value['SmsOutbox']['sistema_origem'] ?></td>
                </tr>

            <?php endforeach; ?>
            
        </tbody>
    </table>
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

<?php echo $this->Js->writeBuffer(); ?>
<?php else : ?>
  <div class='alert'>Nenhum dado encontrado</div>
<?php endif; ?>
