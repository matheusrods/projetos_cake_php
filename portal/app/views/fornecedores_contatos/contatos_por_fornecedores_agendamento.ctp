<div class="row-fluid">
  <div class="span4">  

  <label for="FornecedorDescricaoContato">Descricao (*)</label>
    <textarea name="data[Fornecedor][descricao_contato]" id="FornecedorDescricaoContato" cols="30" style='width: 100%; height:100px;'><?= $descricao_contato;?></textarea>        
  </div>

  <div class="span8">
    <table class="table table-striped tabela_select" style="margin-top: 25px;">
    <thead>
      <tr>
        <th><input type="checkbox" id="select_all"  /></th>
        <th>Retorno</th>
        <th>Contato ( Fone / Email )</th>
      </tr>
    </thead>
    <?php if(!empty($contatos)):?>
    <tbody>
    <?php foreach ($contatos as $key => $contato): ?>
      <?php $descricao_contato = $contato['FornecedorContato']['ddd'].$contato['FornecedorContato']['descricao']; ?>
      <?php if (in_array($contato['FornecedorContato']['codigo_tipo_retorno'], array(1,3,5,7,12))): ?>
      <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
      <?php endif; ?>
      <tr>
          <td><input type="checkbox" name="data[FornecedorContato][checado][<?= $contato['FornecedorContato']['codigo'] ?>]" <?php echo $contato['FornecedorContato']['checado'] == 1 ? "checked" : "" ; ?> /></td>
          <td><?php echo $contato['TipoRetorno']['descricao'] ?></td>
          <td><?php echo $descricao_contato ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <?php else:?>
        <tr>
            <td colspan=6">
              <div>Nenhum dado foi encontrado.</div>
            </td>
        </tr>    
    <?php endif;?>
    </table>
  </div>
</div>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
    });
  
    ") 
?>

<script>

    $(function(){

        $("#select_all").on("change", function(){

            if ($(this).is(":checked")) {
                $('.tabela_select tbody tr input:checkbox').prop('checked','checked');
            } else {
                $('.tabela_select tbody tr input:checkbox').removeProp('checked');
            }
        })
    })
</script>