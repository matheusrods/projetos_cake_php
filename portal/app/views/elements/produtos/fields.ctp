  <div class='row-fluid inline'>
    <?php echo $this->BForm->input('codigo', array('label' => 'Código', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('descricao', array('label' => 'Descrição', 'class' => 'input-xxlarge')); ?>
    <?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-large', 'default' => 1,'options' => array(1 => 'Ativo',2=> 'Inativo'), 'label' => 'Ativo')); ?>
    <?php //if(!$edit_mode): ?>
    <?php echo $this->BForm->input('tipo_servico', array('label' => 'Tipo de Serviço (**)', 'class' => 'input', 'default' => '','empty' => 'Tipo de Serviço', 'options' => array('E' => 'Exames Complementares', 'G' => 'Engenharia', 'C' => 'Consultorias e Palestras','S'=> 'Saúde','M'=>'Mensalidade','D'=>'Desenvolvimento') )); ?>
     <?php //endif; ?>
    <?php if($edit_mode): ?>
      <?php echo $this->BForm->hidden('codigo_servico'); ?>
    <?php endif; ?>
    
  </div>  
  <?php if((isset($produtos_sem_mensalidade) && !in_array($this->data['Produto']['codigo'], $produtos_sem_mensalidade)) || !isset($produtos_sem_mensalidade)){ ?>
    <div class='row-fluid inline'>
      <?php echo $this->BForm->input('mensalidade', array('type' => 'checkbox', 'label' => 'Mensalidade', 'class' => 'input-xxlarge')); ?>
    </div>
  <?php } ?>
  
  <h4>Dados para integração com sistema Naveg</h4>
  <div class='row-fluid inline'>
    <?php echo $this->BForm->input('codigo_naveg', array('label' => 'Código do Produto Naveg', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('codigo_ccusto_naveg', array('label' => 'Centro de Custo Naveg', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('codigo_formula_naveg', array('label' => 'Código Fórmula Naveg', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('codigo_formula_naveg_sp', array('label' => 'Código Fórmula SP Naveg', 'class' => 'input-medium')); ?>
  </div>
  <div class='row-fluid inline'>
    <?php echo $this->BForm->input('formula_valor_acima_de', array('label' => 'Formulas Valor Acima De', 'class' => 'moeda numeric input-medium', 'value' => isset($this->data['Produto']['formula_valor_acima_de']) ? number_format(floatval($this->data['Produto']['formula_valor_acima_de']),2,',','.') : '0,00')); ?>
    <?php echo $this->BForm->input('codigo_formula_naveg_acima', array('label' => 'Código Fórmula Naveg', 'class' => 'input-medium')); ?>
    <?php echo $this->BForm->input('codigo_formula_naveg_sp_acima', array('label' => 'Código Fórmula SP Naveg', 'class' => 'input-medium')); ?>
  </div>
  <div class='row-fluid inline'>
    <?php echo $this->BForm->input('percentual_irrf', array('label' => 'Percentual IRRF', 'class' => 'input-medium numeric moeda', isset($this->data['Produto']['percentual_irrf']) ? number_format(floatval($this->data['Produto']['percentual_irrf']),2,',','.') : '0,00')); ?>
    <?php echo $this->BForm->input('valor_acima_irrf', array('label' => 'IRRF Valor Acima De', 'class' => 'moeda numeric input-medium', 'value' => isset($this->data['Produto']['valor_acima_irrf']) ? number_format(floatval($this->data['Produto']['valor_acima_irrf']),2,',','.') : '0,00')); ?>
    <?php echo $this->BForm->input('percentual_irrf_acima', array('label' => 'Percentual IRRF Acima', 'class' => 'input-medium numeric moeda', isset($this->data['Produto']['percentual_irrf_acima']) ? number_format(floatval($this->data['Produto']['percentual_irrf_acima']),2,',','.') : '0,00')); ?>
  </div>
  <?php if(!$edit_mode): ?>
    <span style='font-size: 11px'>
      ** Será gerado automaticamente um serviço com o mesmo nome do produto informado com o tipo selecionado
    </span>
  <?php endif; ?>
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('controller' => 'produtos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('$(document).ready(function() {setup_mascaras();});');