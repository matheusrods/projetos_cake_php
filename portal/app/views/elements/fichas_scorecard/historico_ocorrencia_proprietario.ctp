<div class="ocorrencia-proprietario">
  <?php if( is_array($ocorrencia_proprietario) && count($ocorrencia_proprietario) > 0):?>
    <?php foreach( $ocorrencia_proprietario as $key => $dados_ocorrencia ):?>      
      <h4> Ocorrências do proprietário do veículo tipo <?php echo FichaScorecardVeiculo::descricao($key);?></h4>
      <table class="table table-condensed table-striped">
        <thead>
          <th class="input-large">Nome</th>
          <th class="input-medium">Código Documento</th>
          <th class="input-midium">Ocorrência</th>
          <th class="input-large">Observação</th>
          <th class="input-medium">Data Inclusão</th>
          <th class="input-medium">Usuário</th>
        </thead>
        <?php if( is_array($dados_ocorrencia) && count($dados_ocorrencia) > 0):?>
          <?php foreach( $dados_ocorrencia as $key => $ocorrencia ):?>
            <?php if( isset($ocorrencia['ProfissionalNegativacao']) && count($ocorrencia['ProfissionalNegativacao'])> 0 ): ?>
        <tr>  
          <td><?php echo $ocorrencia['Profissional']['nome'] ?></td>
          <td><?php echo $ocorrencia['Profissional']['codigo_documento'] ?></td>
          <td><?php echo $ocorrencia['TipoNegativacao']['descricao'] ?></td>
          <td><?php echo $ocorrencia['ProfissionalNegativacao']['observacao'] ?></td>
          <td><?php echo $ocorrencia['ProfissionalNegativacao']['data_inclusao'] ?></td>
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
    <div class="alert">Proprietário não possui ocorrências</div>
<?php endif;?>
</div>