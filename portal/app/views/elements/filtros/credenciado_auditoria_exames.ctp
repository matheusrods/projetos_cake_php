<?php
$mes_referencia = array(); // todo
$ano_referencia = array(); // todo	
$status_auditoria = array('Liberados para Pagamento','Pendente', 'Pagamento Bloqueado'); // todo
?>
<div class='well'>
  <?php echo $bajax->form('Credenciado', 
      array('autocomplete' => 'off', 
            'url' => array('controller' => 'filtros', 
                            'action' => 'filtrar', 
                            'model' => 'Credenciado', 
                            'element_name' => 'credenciado_auditoria_exames'
                            ), 'divupdate' => '.form-procurar')) ?>
    
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo_credenciado', array('class' => 'input-small', 'placeholder' => 'Código Credenciado', 'label' => 'Código Credenciado', 'type' => 'text')) ?>
      
      
    </div>        
    <div class="row-fluid inline">			
      <?php 
      
        echo $this->BForm->input('status_auditoria', array('label' => 'Status Auditoria', 'class' => 'input-small', 'options' => $status_auditoria, 'empty' => 'Todos', 'default' => 'Liberados para Pagamento'));

				echo $this->BForm->input('mes_referencia', 
						array(
							'class' => 'input-medium',
							'label' => 'Mes', 
							'options' => $mes_referencia, 
							'empty' => 'Selecione'  
						)
          );
          
			  echo $this->BForm->input('ano_referencia', 
						array(
							'class' => 'input-medium',
							'label' => 'Ano', 
							'options' => $ano_referencia, 
							'empty' => 'Selecione'
						)
          );
        ?>		
		</div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        function atualizaListagem() {	
          var div = jQuery("div.lista");
          bloquearDiv(div);		
          div.load(baseUrl + "credenciado_auditoria_exames/listagem/" + Math.random());
        }

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Credenciado/element_name:credenciado_auditoria_exames/" + Math.random())
        });

        setup_datepicker();
        atualizaListagem();

    });', false);
