<div class="ocorrencia-veiculos">
  <?php if( is_array($ocorrencia_veiculo) && count($ocorrencia_veiculo) > 0):?>
    <?php foreach( $ocorrencia_veiculo as $key => $dados_ocorrencia ):?>      
      <h4> Ocorrências do veículo tipo <?php echo FichaScorecardVeiculo::descricao($key);?></h4>
      <table class="table table-condensed table-striped">
        <thead>
          <th class="input-large">Placa</th> 
          <th class="input-large">Data ocorrência</th> 
          <th class="input-large">Ocorrência</th>
          <th class="input-large">Observação</th>
          <th class="input-large">Data Inclusão</th>
          <th class="input-large">Usuário</th>    
        </thead>
        <?php if( is_array($dados_ocorrencia) && count($dados_ocorrencia) > 0):?>
          <?php foreach( $dados_ocorrencia as $key => $ocorrencia ):?>
            <?php if( isset($ocorrencia['Veiculo']) && count($ocorrencia['Veiculo'])> 0 ): ?>
        <tr>  
          <td><?php echo comum::formatarPlaca($ocorrencia['Veiculo']['placa'])?></td>
          <td><?php echo substr($ocorrencia['VeiculoOcorrencia']['data_ocorrencia'],0,10);?></td>
          <td><?php echo $ocorrencia['VeiculoOcorrencia']['observacao'] ?></td>
          <td><?php echo $ocorrencia['TipoOcorrenciaTeleconsult']['descricao'] ?></td>
          <td><?php echo substr($ocorrencia['VeiculoOcorrencia']['data_inclusao'],0,10)?></td>
          <td><?php echo $ocorrencia['Usuario']['apelido'] ?></td>
        </tr>
          <?php else: ?>
        <tr><td colspan="6"><div class="">Não possui ocorrências</div></td></tr>
          <?php endif;?>
        <?php endforeach;?>
      <?php else:?>
        <tr><td colspan="6"><div class="">Não possui ocorrências</div></td></tr>
      <?php endif;?>
      </table>
    <?php endforeach;?>
  <?php else: ?>
    <div class="alert">Veículo não possui ocorrências</div>
<?php endif;?>
</div>