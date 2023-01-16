<div class="ocorrencias-profissional"> 
  <h4> Ocorrências do Profissinal</h4>
  <?php if( is_array($ocorrencia_profissional) && count($ocorrencia_profissional) > 0):?>
  <table class="table table-condensed table-striped">
    <thead>
      <th class="input-large">Nome</th>
      <th class="input-medium">Código Documento</th>
      <th class="input-midium">Ocorrência</th>
      <th class="input-large">Observação</th>
      <th class="input-medium">Data Inclusão</th>
      <th class="input-medium">Usuário</th>    
    </thead>
    <?php foreach( $ocorrencia_profissional as $key => $ocorrencia ):?>
    <tr>
      <td><?php echo $ocorrencia['Profissional']['nome'] ?></td>
      <td><?php echo comum::formatarDocumento($ocorrencia['Profissional']['codigo_documento']);?></td>
      <td><?php echo $ocorrencia['TipoNegativacao']['descricao'] ?></td>
      <td><?php echo $ocorrencia['ProfissionalNegativacao']['observacao'] ?></td>
      <td><?php echo $ocorrencia['ProfissionalNegativacao']['data_inclusao'] ?></td>
      <td><?php echo $ocorrencia['Usuario']['apelido'] ?></td>
    </tr>
    <?php endforeach;?>
  </table>    
  <?php else: ?>
    <div class="alert">Profissional não possui ocorrências</div>
<?php endif;?>
</div>
<div class="ocorrencias-profissional-cliente"> 
  <h4> Ocorrências do Profissinal por Cliente</h4>
  <?php if( is_array($ocorrencia_profissional_cliente) && count($ocorrencia_profissional_cliente) > 0):?>
  <table class="table table-condensed table-striped">
    <thead>
      <th class="input-large">Nome</th>
      <th class="input-medium">Código Documento</th>
      <th class="input-midium">Ocorrência</th>
      <th class="input-large">Observação</th>
      <th class="input-medium">Data Inclusão</th>
      <th class="input-medium">Usuário</th>    
    </thead>
    <?php foreach( $ocorrencia_profissional_cliente as $key => $ocorrencia ):?>
    <tr>
      <td><?php echo $ocorrencia['Profissional']['nome'] ?></td>
      <td><?php echo comum::formatarDocumento($ocorrencia['Profissional']['codigo_documento']);?></td>
      <td><?php echo $ocorrencia['TipoNegativacao']['descricao'] ?></td>
      <td><?php echo $ocorrencia['ProfNegativacaoCliente']['observacao'] ?></td>
      <td><?php echo $ocorrencia['ProfNegativacaoCliente']['data_inclusao'] ?></td>
      <td><?php echo $ocorrencia['Usuario']['apelido'] ?></td>
    </tr>
    <?php endforeach;?>
  </table>    
  <?php else: ?>
    <div class="alert">Profissional não possui ocorrências por cliente</div>
<?php endif;?>
</div>