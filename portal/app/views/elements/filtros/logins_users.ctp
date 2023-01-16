<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('UsuarioHistorico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'UsuarioHistorico', 'element_name' => 'logins_users'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('usuarios_historicos/fields_historicos') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){

        setup_datepicker();

        atualizaLista();

            var codigo_fornecedor = $(\'#UsuarioHistoricoCodigoFornecedor\').val();        
            if(codigo_fornecedor){
                preenche_name_fornecedor(codigo_fornecedor);
            }
        
        jQuery("#limpar-filtro").click(function(){

            bloquearDiv(jQuery(".form-procurar"));
            
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:UsuarioHistorico/element_name:logins_users/" + Math.random())

            atualizaLista();
        });

        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
			div.load(baseUrl + "usuarios_historicos/lista_logins_users/" + Math.random());
        }

        $(document).on(\'blur\', \'#UsuarioHistoricoCodigoFornecedor\', function() { 
            var codigo_fornecedor = $(\'#UsuarioHistoricoCodigoFornecedor\').val();
            if (codigo_fornecedor) {
                preenche_name_fornecedor(codigo_fornecedor);
            }
        });

        function preenche_name_fornecedor(codigo_fornecedor){
            var input = $("#UsuarioHistoricoCodigoFornecedorCodigo");
            $.ajax({
                url:baseUrl + "consultas/get_fornecedores/" + codigo_fornecedor + "/" + Math.random(),
                dataType: "json",
                beforeSend: function() {
                    bloquearDiv(input.parent());
                },
                success: function(data) {
                    // console.log(data);
                    if (data.sucesso) {
                        var input_name_display = $("#UsuarioHistoricoCodigoFornecedorCodigo").val(data.dados.razao_social);
                    } else {
                        swal("ATENÇÃO!", "Prestador Não encontrado", "warning");
                        var input_name_display = $("#UsuarioHistoricoCodigoFornecedorCodigo").val("");
                    }
                },
                complete: function() {
                    input.parent().unblock();
                }
            });
        }
        
    });', false);
?>