<div class="rma-profissional">
  <?php if( is_array($rmas) && count($rmas) > 0):?>
  <table class="table table-condensed table-striped">
    <thead>
      <th class="input-large">Profissional</th>
      <th class="input-medium">Código documento</th>
      <th class="input-midium">Tipo</th>
      <th class="input-large">Qtde</th>      
    </thead>
    <?php foreach( $rmas as $key => $ocorrencia ):?>
    <tr>
      <td><?php echo $ocorrencia['TPessPessoa']['pess_nome'] ?></td>
      <td><?php echo comum::formatarDocumento($ocorrencia['TPfisPessoaFisica']['pfis_cpf']); ?></td>
      <td><?php echo $ocorrencia['TTrmaTipoRma']['trma_descricao'] ?></td>
      <td><?php echo $ocorrencia[0]['qtd_ocorrencias'] ?></td>      
    </tr>
    <?php endforeach;?>
  </table>    
  <?php else: ?>
    <div class="alert">Profissional não possui ocorrências</div>
<?php endif;?>
</div>