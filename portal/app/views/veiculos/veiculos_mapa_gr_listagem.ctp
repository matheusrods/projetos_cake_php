<div align="right" id="divAtualizacaoAuto" style="display: none;">
    <?php echo $html->link('Atualização Automática: '.($status_atualizacao==1?'Ativado':'Desativado'), 'javascript:autoRefresh();', array('class' => 'auto-refresh')) ?>
</div>
<div class="well">
    <strong>Última atualização:</strong> <?php echo date('d/m/Y H:i:s') ?> 
    <span class="pull-right">
        <?php echo $html->link('Atualizar', 'javascript:recarregar_consulta();') ?>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
    </span>
</div>
<?php if(!empty($veiculos)): ?>

    <?php echo $this->element('/veiculos/veiculos_mapa_gr_map', array('veiculos'=>$veiculos)); ?>

    <?php echo $this->element('/veiculos/veiculos_mapa_gr_listagem', array('veiculos'=>$veiculos)); ?>

<?php else: ?>
    <div class="alert">
		Nenhum registro encontrado.
	</div>
<?php endif; ?>

    <?php echo $this->Javascript->codeBlock('
        function recarregar_consulta() {
            var div = jQuery("div.lista");
            $.ajax({
                type: "POST",
                url: baseUrl + "veiculos/veiculos_mapa_gr_listagem/" + Math.random(),
                data: $("#frmOpcoesMapa").serialize(),
                dataType: "html",
                beforeSend: function() {
                    bloquearDiv(div);
                },
                success: function(data) {
                    div.html(data);
                },
                complete: function() {
                    div.unblock();
                }
            });  
            //div.load(baseUrl + "veiculos/veiculos_mapa_gr_listagem/" + Math.random());

        };   
        
        function autoRefresh(){
            status_atual = ($("#RelatorioSmVeiculosStatusAtualizacao").val()=="1" ? true : false);
            if(!status_atual){
                $(".auto-refresh").html("Atualização Automática: Ativado");
                $("#RelatorioSmVeiculosStatusAtualizacao").val("1");
                autoRefreshInteval = setInterval(function(){
                    recarregar_consulta();
                }, 60000);
            }else{
                $("#RelatorioSmVeiculosStatusAtualizacao").val("");
                $(".auto-refresh").html("Atualização Automática: Desativado");
                if(typeof autoRefreshInteval != "undefined"){
                    clearInterval(autoRefreshInteval);
                }
            }
        }

        $(document).ready(function(){
            $("#divAtualizacaoAuto").show();
            status_atual = ($("#RelatorioSmVeiculosStatusAtualizacao").val()=="1" ? true : false);
            if (status_atual) {
                if(autoRefreshInteval == null){
                    autoRefreshInteval = setInterval(function(){
                        recarregar_consulta();
                    }, 60000);
                }
                $(".auto-refresh").html("Atualização Automática: Ativado");
            } else {
                $(".auto-refresh").html("Atualização Automática: Desativado");
            }

        });          
    ', false);
    ?>