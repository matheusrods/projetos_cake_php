<div class='well'>
<?php 
    echo $bajax->form('GerenciarPCMSO', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes_implantacao', 'action' => 'index_pcmso_ext', ), 'divupdate' => '.form-procurar'), false);
    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false);
    echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn'));
    echo $this->BForm->end();
?>
</div>
<div style="float:right">
  <?php echo $html->link('Importar Dados PCMSO', array('controller' => 'importar', 'action' => 'importar_pcmso', $this->data['Cliente']['codigo']), array('class' => 'btn btn-warning', 'data-toggle' => 'tooltip', 'title' => 'Importar PCMSO')); ?>
</div>
 
<div class='lista'>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Código Cliente</th>
        <th>Razão Social</th>
        <th>Nome Fantasia</th>
        <th>Bairros</th>
        <th>Cidade</th>
        <th>Estado</th>
        <th>Funcionários Alocados</th>
        <th>Credenciado</th>
        <th>Início Vigência</th>
        <th>Período Vigência</th>
        <th>Status</th>
        <th class="input-small">Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      foreach($lista_clientes_grupo as $dados):
       $print = false;
       $status = $dados[0]['StatusOrdemServico_descricao'];
       if(isset($dados[0]['OrdemServico_status'])) {
        switch ($dados[0]['OrdemServico_status']) {
         case '1':
           $class = "badge badge-empty badge-info";
           $destino = "";
         break;
         case '2':
           $class = "badge badge-empty badge-info";
           $destino = "";
         break;
         case '3':
           $class = "badge badge-empty badge-success";
           $destino = "";
           $print = true;
         break;
         case '5':
           $class = "badge badge-empty badge-warning";
           $destino = "";
           $print = true;
         break;
         default:
           $class = "badge badge-empty badge-important";
           $destino = $this->Html->link('', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado',  $dados[0]['Unidade_codigo'], $codigo_servico_pcmso), array('class' => 'icon-search', 'data-toggle' => 'tooltip', 'title' => 'Localizar Fornecedor'));
         break;  
       }
     } else {
      $status = 'Pendente';
      $class = "badge badge-empty badge-important";
      $destino = $this->Html->link('', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado',  $dados[0]['Unidade_codigo'], $codigo_servico_pcmso), array('class' => 'icon-search', 'data-toggle' => 'tooltip', 'title' => 'Localizar Fornecedor')); 
    }
   

    ?>
    <tr>
      <td class="input-small"><?php echo $dados[0]['Unidade_codigo'];?></td>
      <td><?php echo $dados[0]['Unidade_razao_social'];?></td>
      <td><?php echo $dados[0]['Unidade_nome_fantasia'];?></td>
      <td><?php echo $dados[0]['ClienteEndereco_bairro'];?></td>
      <td><?php echo $dados[0]['ClienteEndereco_cidade'];?></td>
      <td><?php echo $dados[0]['ClienteEndereco_abreviacao'];?></td>
      <td>
          <?php
          if(isset($qtd_funcionarios[$dados[0]['Unidade_codigo']])) {
            echo $qtd_funcionarios[$dados[0]['Unidade_codigo']];
          }
          ?>
      </td>
      <td><?php echo isset($dados[0]['Fornecedor_razao_social']) ? $dados[0]['Fornecedor_razao_social'] : '---';?></td>
      <td><?php echo !empty($dados[0]['OrdemServico_inicio_vigencia_p'])? AppModel::dbDateToDate($dados[0]['OrdemServico_inicio_vigencia_p']) : '---';?></td>
      <td><?php echo!empty($dados[0]['OrdemServico_vigencia_em_meses'])? $dados[0]['OrdemServico_vigencia_em_meses'].' meses' : '---';?></td>
      <td>
        <?php echo $this->Html->link($status, 'javascript:void(0);', array('class' => $class, 'style' => 'cursor:default'));?>
      </td>
      <td>
          <?php 
            if($dados[0]['OrdemServico_status'] != 3):
                if(!empty($dados[0]['Fornecedor_codigo'])):
                    echo $this->Html->link('', array('controller' => 'aplicacao_exames', 'action' => 'index', $dados[0]['Unidade_codigo'], $this->data['Cliente']['codigo']), array('class' => 'icon-wrench', 'data-toggle' => 'tooltip', 'title' => 'Gerenciar Aplicação de Exames (PCMSO)'));
                else:
                    echo $this->Html->link('', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado', $dados[0]['Unidade_codigo'],$codigo_servico_pcmso), array('class' => 'icon-wrench', 'data-toggle' => 'tooltip', 'title' => 'Gerenciar Aplicação de Exames (PCMSO)'));
                endif;
            else:  
                echo $this->Html->link('', array('controller' => 'aplicacao_exames', 'action' => 'visualizar_gae', $dados[0]['Unidade_codigo'], ), array('class' => 'input-small icon-eye-open', 'data-toggle' => 'tooltip', 'title' => 'Visualizar Gerenciar Aplicação de Exames (PCMSO)')).'&nbsp';              
                echo $this->Html->link('', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado', $dados[0]['Unidade_codigo'],$codigo_servico_pcmso,'pcmso'), array('class' => 'icon-retweet', 'data-toggle' => 'tooltip', 'title' => 'Gerar nova Versão'));
            endif;

            if($dados[0]['OrdemServico_status'] == 5) : //processando
                echo $this->Html->link('', array('controller' => 'cronogramas_acoes', 'action' => 'editar', $dados[0]['GrupoEconomico_codigo_cliente'], $dados[0]['Unidade_codigo']), array('class' => 'icon-file', 'data-toggle' => 'tooltip', 'title' => 'Cronograma de Ações'));
            endif;

            if($print) {
                // echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-print', 'data-toggle' => 'tooltip', 'title' => 'Imprimir relatório PCMSO','onclick' => 'verifica_setor_cargo_funcionario('.$dados[0]['Unidade_codigo'].')'));
                echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-print', 'data-toggle' => 'tooltip', 'title' => 'Imprimir relatório PCMSO','onclick' => 'parametros_relatorio_pcmso('.$dados[0]['Unidade_codigo'].',1)'));
          ?> &nbsp;
            <?php } else { ?>
                <span class="icon-print opacity" data-toggle="tooltip" title="Opção indisponível">&nbsp;</span>&nbsp;
            <?php } ?>

        <?php echo $this->Html->link('', array('controller' => 'grupos_homogeneos_exames', 'action' => 'index', $dados[0]['Unidade_codigo'],'implantacao'), array('class' => 'icon-addgrupo', 'data-toggle' => 'tooltip', 'title' => 'Cadastrar Grupos Homogêneos', 'style' => 'color: #000; text-decoration: none;')); ?> &nbsp;

        <?php echo $destino;?>
      </td>
    </tr>
  <?php endforeach ?>
</tbody>
</table>
</div>

<?php if(!isset($pcmso_ext)): ?>
<div class='form-actions well'>
  <?php echo $html->link('Concluido', array('controller' => 'clientes_implantacao', 'action' => 'atualiza_status', $this->data['Cliente']['codigo'], 'pcmso', 'C' ), array('class' => 'btn btn-primary')); ?>
  <?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php endif; ?>

<div class="modal fade" id="modal_parametros_relatorio_pcmso" data-backdrop="static"></div>

<script type="text/javascript">
    $(document).ready(function() {

      $(".modal").css("z-index", "1");
      $(".modal").css("top", "96px");
                
      /**
       * [verifica_seto_cargo_funcionario description]
       *
       * funcao para apresentar o swall com a opcao de sim/nao
       * @param  {[type]} codigo_unidade [description]
       * @return {[type]}                [description]
       */
      verifica_setor_cargo_funcionario = function(codigo_unidade) {
          
          swal({
            title: "Imprimir documento com Setor e Cargo sem Funcionários?",
            text: "Setores e Cargos sem Funcionários sair no relatório!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Sim",
            cancelButtonText: "Não",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm) {
              if (isConfirm) {
                  //sim para imprimir os setores e cargos
                  var url = baseUrl + "/clientes_implantacao/imprimir_relatorio/"+codigo_unidade+"/0";
                  window.location.href = url;                    
              } 
              else {
                  //não para não imprimir setores e cargos que não tenha funcionarios
                  var url = baseUrl + "/clientes_implantacao/imprimir_relatorio/"+codigo_unidade+"/1";
                  window.location.href = url;
              }
          });

      }//fim verifica_seto_cargo_funcionario(codigo_unidade)

    });

    function parametros_relatorio_pcmso(codigo_unidade,mostra) {
      if(mostra) {
        
        var div = jQuery("div#modal_parametros_relatorio_pcmso");
        bloquearDiv(div);
        div.load(baseUrl + "clientes_implantacao/modal_parametros_relatorio_pcmso/" + codigo_unidade + "/" + Math.random());
    
        $("#modal_parametros_relatorio_pcmso").css("z-index", "1050");
        $("#modal_parametros_relatorio_pcmso").modal("show");

      } else {
        $(".modal").css("z-index", "-1");
        $("#modal_parametros_relatorio_pcmso").modal("hide");
      }
	  }
</script>