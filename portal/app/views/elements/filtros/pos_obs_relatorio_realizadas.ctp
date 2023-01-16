<div class='well'>
  <?= $bajax->form('PosObsObservacoes', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PosObsObservacoes', 'element_name' => 'pos_obs_relatorio_realizadas'), 'divupdate' => '.form-procurar')) ?>
  <div class="row-fluid inline">
    <?php echo $this->Buonny->input_ge_unidades_cargos_setores($this, 'PosObsObservacoes', $unidades, $setores); ?>
  </div>
  <div class="row-fluid inline">
    <?php
    echo $this->BForm->input('cliente_opco', array('label' => 'Opco', 'class' => 'input-medium', 'options' => $cliente_opco, 'empty' => 'Selecione'));
    echo $this->BForm->input('cliente_bu', array('label' => 'Business Unit', 'class' => 'input-medium', 'options' => $cliente_bu, 'empty' => 'Selecione'));
    ?>
  </div>
  <div class="row-fluid inline">
    <?php
    echo $this->BForm->input('id_observacao', array('type' => 'text', 'class' => 'input-mini',  'label' => 'ID '));
    echo $this->BForm->input('status', array('label' => 'Status', 'class' => 'input-large', 'options' => $status_observacao, 'empty' => 'Selecione o Status'));
    echo $this->BForm->input('observador', array('label' => 'Observador', 'class' => 'input-large', 'options' => $observador, 'empty' => 'Selecione o Observador'));
    echo $this->BForm->input('categoria', array('label' => 'Tipo de Observação', 'class' => 'input-large', 'options' => $categorias, 'empty' => 'Selecione o Tipo'));
    ?>
  </div>

  <?= $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
  <?= $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
  <?= $this->BForm->end() ?>
</div>

<script>
  $(document).ready(function() {

    function atualizaLista() {
      var div = $("div.lista");
      bloquearDiv(div);
      div.load(baseUrl + "pos_obs_relatorio_realizadas/listagem/" + Math.random());
    }

    function carregaUnidades(codigoClienteSelecionado) {
      const selectUnidadeInput = $('#PosObsObservacoesCodigoClienteAlocacao');

      selectUnidadeInput.html('');
      selectUnidadeInput.append($('<option />').val('').text('Selecione a unidade'));

      bloquearDiv(selectUnidadeInput.parent());

      $.ajax({
        'url': baseUrl + 'swt/combo_clientes_ajax/' + codigoClienteSelecionado + '/' + Math.random(),
        'dataType': 'json',
        'success': function(result) {
          if (result != null) {
            $.each(result, function(i, r) {
              selectUnidadeInput.append($('<option />').val(r['Cliente']['codigo']).text(r['Cliente']['nome_fantasia']));
            });
          }
          selectUnidadeInput.parent().unblock();
        }
      });
    }

    function carregaSetores(codigoUnidadeSelecionada) {
      const selectSetoresInput = $('#PosObsObservacoesCodigoSetor');

      selectSetoresInput.html('');
      selectSetoresInput.append($('<option />').val('').text('Selecione o Setor'));

      bloquearDiv(selectSetoresInput.parent());

      $.ajax({
        'url': baseUrl + 'swt/combo_setores_ajax/' + codigoUnidadeSelecionada + '/' + Math.random(),
        'dataType': 'json',
        'success': function(result) {
          if (result != null) {
            $.each(result, function(i, r) {
              selectSetoresInput.append($('<option />').val(result[i]['Setor']['codigo']).text(result[i]['Setor']['descricao']));
            });
          }
          selectSetoresInput.parent().unblock();
        }
      });
    }

    function carregaOpco(codigoUnidadeSelecionada) {
      const selectOpcoInput = $('#PosObsObservacoesClienteOpco');

      selectOpcoInput.html('');
      selectOpcoInput.append($('<option />').val('').text('Selecione a opco'));

      bloquearDiv(selectOpcoInput.parent());

      $.ajax({
        'url': baseUrl + 'swt/combo_opco_ajax/' + codigoUnidadeSelecionada + '/' + Math.random(),
        'dataType': 'json',
        'success': function(result) {
          if (result != null) {
            $.each(result, function(i, r) {
              selectOpcoInput.append($('<option />').val(r['ClienteOpco']['codigo']).text(r['ClienteOpco']['descricao']));
            });
          }
          selectOpcoInput.parent().unblock();
        }
      });
    }

    function carregaBu(codigoUnidadeSelecionada) {
      const selectBuInput = $('#PosObsObservacoesClienteBu');

      selectBuInput.html('');
      selectBuInput.append($('<option />').val('').text('Selecione a Business Unit'));

      bloquearDiv(selectBuInput.parent());

      $.ajax({
        'url': baseUrl + 'swt/combo_bu_ajax/' + codigoUnidadeSelecionada + '/' + Math.random(),
        'dataType': 'json',
        'success': function(result) {
          if (result != null) {
            $.each(result, function(i, r) {
              selectBuInput.append($('<option />').val(r['ClienteBu']['codigo']).text(r['ClienteBu']['descricao']));
            });
          }
          selectBuInput.parent().unblock();
        }
      });
    }

    $("#PosObsObservacoesCodigoCliente").on("change", function() {
      const codigoClienteSelecionado = $(this).val();

      $('#PosObsObservacoesClienteBu').html('');
      $('#PosObsObservacoesClienteBu').append($('<option />').val('').text('Selecione a Business Unit'));
      $('#PosObsObservacoesClienteOpco').html('');
      $('#PosObsObservacoesClienteOpco').append($('<option />').val('').text('Selecione a opco'));
      
      carregaUnidades(codigoClienteSelecionado);
    });

    $("#PosObsObservacoesCodigoClienteAlocacao").on("change", function() {
      const codigoUnidadeSelecionada = $(this).val();
      carregaSetores(codigoUnidadeSelecionada);
      carregaOpco(codigoUnidadeSelecionada);
      carregaBu(codigoUnidadeSelecionada);
    });

    $("#limpar-filtro").click(function() {
      bloquearDiv(jQuery(".form-procurar"));
      jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PosObsObservacoes/element_name:pos_obs_relatorio_realizadas/" + Math.random())
      atualizaLista();
    });

    atualizaLista();

  });
</script>