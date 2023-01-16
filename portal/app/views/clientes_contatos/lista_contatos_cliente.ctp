<h5>Contatos</h5>
<?if( $incluir_contato ): ?>
<div class="row-fluid">
    <span class="span12 span-right">
      <?= $this->Html->link('<i class="icon-plus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn', 'title' => 'Incluir Contato', 'id' => 'incluir-contato')) ?>
      <?= $this->Html->link('<i class="icon-minus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn', 'title' => 'Incluir Contato', 'id' => 'excluir-contato')) ?>
    </span>
</div>
<?endif;?>
<div id="incluir_contato" style="display:<?=$disabled_contato==true?'none':'';?>">
  <div class="row-fluid inline">
      <?php echo $this->BForm->hidden('ClienteContato.codigo_cliente') ?>
      <?php echo $this->BForm->input('ClienteContato.codigo_tipo_retorno', array('label' => 'Retorno','options' => $tipos_retorno, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
      <?php echo $this->BForm->input('ClienteContato.descricao', array('label' => 'Contato', 'class' => 'input-medium')) ?>
      <?php echo $this->BForm->input('ClienteContato.codigo_tipo_contato', array('label' => 'Tipo','options' => $tipos_contato, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
      <?php echo $this->BForm->input('ClienteContato.nome', array('label' => 'Representante', 'class' => 'text-medium')) ?>
  </div>
</div>
<div id="contatos-cliente" class="grupo" style="display:<?=$disabled_contato==true?'':'none';?>">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Retorno</th>
          <th>Contato</th>
          <th>Tipo</th>
          <th>Representante</th>
          <?php if( $tipo_exibicao ):?>
          <th>Selecione</th>
          <?php endif;?>
        </tr>
      </thead>
        <?php foreach ($listagem as $contato): ?>
          <?php $descricao_contato = $contato['ClienteContato']['ddd'].$contato['ClienteContato']['descricao']; ?>
          <?php if (in_array($contato['ClienteContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
          <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
          <?php endif; ?>
          <tr id="<?=$contato['ClienteContato']['codigo']?>">
              <td id="tipo_retorno_descricao" codigo=<?= $contato['ClienteContato']['codigo_tipo_retorno'] ?> ><?php echo $contato['TipoRetorno']['descricao'] ?></td>
              <td id="contato_descricao"><?php echo $descricao_contato ?></td>
              <td id="tipo_contato_descricao" codigo=<?= $contato['ClienteContato']['codigo_tipo_contato'] ?> ><?php echo $contato['TipoContato']['descricao'] ?></td>
              <td id="contato_nome"><?php echo $contato['ClienteContato']['nome'] ?></td>
                <?php if( $tipo_exibicao ):?>
              <td>
                  <?$checked = (!empty($codigo_cliente_contato) && ($codigo_cliente_contato==$contato['ClienteContato']['codigo']) ? 'checked':'');?>
                <input name="data[ClienteContato][codigo]" type="radio" value="<?=$contato['ClienteContato']['codigo']?>" title="Contato utilizado na pesquisa" <?=$checked?> />
              </td>
                <?php endif;?>
          </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php echo $javascript->codeBlock("
    jQuery('#incluir-contato').click(function() {
      jQuery('#incluir_contato').show();
      $('#contatos-cliente input').each(function(){ $(this).hide(); });
    });
    jQuery('#excluir-contato').click(function() {
      jQuery('#incluir_contato').hide();
      jQuery('#contatos-cliente').show();
      $('#incluir_contato input').each(function(){ $(this).val(''); });
      $('#contatos-cliente input').each(function(){ $(this).show(); });
    });
    jQuery(document).ready(function(){
        tipoRetorno = jQuery('#ClienteContatoCodigoTipoRetorno');
        tipoRetorno.change(
          function(){
            var descricao = jQuery('input#ClienteContatoDescricao');
            if ($(this).val() == 1 || $(this).val() == 3 ||$(this).val() == 5) {
               descricao.addClass('telefone');
               setup_mascaras();
            } else {
                descricao.unmask();
                descricao.removeClass('telefone');
            }
          }
        );
        tipoRetorno.change();
    })"
) ?>