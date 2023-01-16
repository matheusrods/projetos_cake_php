  <table class="table table-striped">
    <tr>
      <td><b>Relatório</b></td>
      <td><b>Ação</b></td>
    </tr>
    <?php if (!empty($dados_relatorio)) : ?>
      <?php foreach ($dados_relatorio as $k_rel => $relatorio) : ?>
        <tr>
          <td><?php echo $list_tipos[$k_rel]; ?></td>
          <td style="width: 35px;">
            <a href="/portal/pedidos_exames/imprime/<?php echo $codigo_pedido; ?>/<?php echo !empty($codigo_fornecedor) ? $codigo_fornecedor : 'null' ?>/<?php echo $codigo_cliente_funcionario; ?>/<?php echo $k_rel; ?>/<?php echo $codigo_func_setor_cargo ?>" target="_blank">
              <!--<a href="/portal/pedidos_exames/imprime/<?php echo $codigo_pedido; ?>/null/<?php echo $codigo_cliente_funcionario; ?>/<?php echo $k_rel; ?>/<?php echo $codigo_func_setor_cargo ?>/<?php echo str_replace(" ", "_", $list_tipos[$k_rel]); ?>" target="_blank">-->
              <i class="icon-print"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>
  <div class="message">
    <?php $this->Buonny->flash(); ?>
  </div>