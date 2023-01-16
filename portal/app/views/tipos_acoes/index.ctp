<div class = 'form-procurar'>
    <?php echo $this->element('/filtros/tipos_acoes') ?>
</div>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'tipos_acoes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Tipos de Acoes'));?>
</div>
<div class='lista'></div>
<script type="text/javascript">
    function atualizaListaTiposAcoes() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "tipos_acoes/listagem/" + Math.random());
    }
    function fnc_toggle_tipo_acao(codigo, status){
        let listagem = jQuery("div.lista");
        bloquearDiv(listagem);
        jQuery.get(baseUrl + "tipos_acoes/status/"+codigo+"/"+status+"/"+Math.random(), function(data){
            swal({type: data.status, title: 'Atenção', text: data.message});
        })
        .fail(function(data){
            console.log(data.responseText);
            swal({type: 'error', title: 'Atenção', text: 'Não foi possivel atualizar o status agora, tente novamente mais tarde!'});
        })
        .always(function(){
            desbloquearDiv(listagem);
            atualizaListaTiposAcoes();
        });
    }
    jQuery(document).ready(function(){
        atualizaListaTiposAcoes();
    });
</script>