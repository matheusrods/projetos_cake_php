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
      <?php echo $this->BForm->hidden('FuncionarioContato.codigo_fornecedor') ?>
      <?php echo $this->BForm->input('FuncionarioContato.codigo_tipo_retorno', array('label' => 'Retorno','options' => $tipos_retorno, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
      <?php echo $this->BForm->input('FuncionarioContato.descricao', array('label' => 'Contato', 'class' => 'input-medium')) ?>
      <?php echo $this->BForm->input('FuncionarioContato.codigo_tipo_contato', array('label' => 'Tipo','options' => $tipos_contato, 'class' => 'input-medium', 'empty' => 'Selecione')) ?>
      <?php echo $this->BForm->input('FuncionarioContato.nome', array('label' => 'Representante', 'class' => 'text-medium')) ?>
  </div>
</div>
<div id="contatos-funcionario" class="grupo" style="display:<?= $disabled_contato == true ? '' : 'none'; ?>">
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
          <?php $descricao_contato = $contato['FuncionarioContato']['ddd'].$contato['FuncionarioContato']['descricao']; ?>
          <?php if (in_array($contato['FuncionarioContato']['codigo_tipo_retorno'], array(1,3,5))): ?>
          <?php    $descricao_contato = $buonny->telefone($descricao_contato);?>
          <?php endif; ?>
          <tr id="<?=$contato['FuncionarioContato']['codigo']?>">
              <td id="tipo_retorno_descricao" codigo=<?= $contato['FuncionarioContato']['codigo_tipo_retorno'] ?> ><?php echo $contato['TipoRetorno']['descricao'] ?></td>
              <td id="contato_descricao"><?php echo $descricao_contato ?></td>
              <td id="tipo_contato_descricao" codigo=<?= $contato['FuncionarioContato']['codigo_tipo_contato'] ?> ><?php echo $contato['TipoContato']['descricao'] ?></td>
              <td id="contato_nome"><?php echo $contato['FuncionarioContato']['nome'] ?></td>
                <?php if( $tipo_exibicao ):?>
              <td>
                  <?$checked = (!empty($codigo_funcionario_contato) && ($codigo_funcionario_contato==$contato['FuncionarioContato']['codigo']) ? 'checked':'');?>
                <input name="data[FuncionarioContato][codigo]" type="radio" value="<?=$contato['FuncionarioContato']['codigo']?>" title="Contato utilizado na pesquisa" <?=$checked?> />
              </td>
                <?php endif;?>
          </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php echo $javascript->codeBlock("
    jQuery('#incluir-contato').click(function() {
		jQuery('#incluir_contato').show();
      	$('#contatos-funcionario input').each(function(){ $(this).hide(); });
    });
    jQuery('#excluir-contato').click(function() {
      	jQuery('#incluir_contato').hide();
      	jQuery('#contatos-funcionario').show();
      	$('#incluir_contato input').each(function(){ $(this).val(''); });
      	$('#contatos-funcionario input').each(function(){ $(this).show(); });
    });
    jQuery(document).ready(function(){
        tipoRetorno = jQuery('#FuncionarioContatoCodigoTipoRetorno');
        tipoRetorno.change(
        	function() {
            	var descricao = jQuery('input#FuncionarioContatoDescricao');
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