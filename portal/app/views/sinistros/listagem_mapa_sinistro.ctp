<?php if (isset($listagem_mapa) && count($listagem_mapa)>0 ){ ?>
    <div style="width: 100%; height: 400px; background: none repeat scroll 0% 0% rgb(229, 227, 223); position: relative;" id="canvas_mapa"> </div>
    <script type="text/javascript">
        $(function(){
            $('.alert').delay(4000).animate({opacity:0,height:0,margin:0},function(){jQuery(this).slideUp()});          
            if (typeof(window.google) != 'undefined') {
                var map_coords = new google.maps.LatLng(-22.070647,-48.4337);
                var map_config = { zoom: 3, center: map_coords, mapTypeId: google.maps.MapTypeId.ROADMAP };
                var map = new google.maps.Map(document.getElementById('canvas_mapa'), map_config);
                <?php foreach($listagem_mapa as $key => $dado ): ?>
                    dado = new google.maps.LatLng('<?php echo @$dado['Sinistro']['latitude']; ?>', '<?php echo @$dado['Sinistro']['longitude']; ?>');
                    var marker_title = '<?php echo "SM: ". @$dado['Sinistro']['sm'] ." - Placa: ".@$dado['Recebsm']['Placa'] ." Lat: ". @$dado['Sinistro']['latitude'] ." Long: ". @$dado['Sinistro']['longitude'] ?>';
                    var marker_image = new google.maps.MarkerImage("/portal/img/marker/red-dot.png", new google.maps.Size(30, 30), new google.maps.Point(0, 0), new google.maps.Point(15, 15));
                    map_marker = new google.maps.Marker({ position: dado, map: map, title: marker_title, icon: marker_image });

                <?php endforeach; ?>
            } else {
                var html  = '<div class="alert alert-error">';
                html += '    <h4>Erro na api do googlemaps</h4>';
                html += '    <h5>Verifique as susas configurações de proxy, ou se o script da api está carregado corretamente.</h5>';
                html += '    </div>';
                $("#canvas_mapa").html(html);
            }
        });
    </script>
    <br />
<?php echo $paginator->options(array('update' => 'div.lista'));?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Sinistro</th>
            <th class="input-mini">SM</th>
            <th class="input-mini">Data Evento</th>
            <th class="input-mini">Tipo de Sinistro</th>
            <th class="input-mini">Embarcador</th>
            <th class="input-mini">Transportador</th>
            <th class="input-mini">Motorista</th>
            <th class="input-mini">Placa</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($listagem as $value): ?>
        <tr>
            <td><?php echo $this->Buonny->codigo_sinistro($value['Sinistro']['codigo']); ?></td>
            <td><?php echo $this->Buonny->codigo_sm($value['Sinistro']['sm']); ?></td>
            <td><?php echo substr($value['Sinistro']['data_evento'],0,10); ?></td>
            <td><?php echo $natureza[$value['Sinistro']['natureza']] ?></td>
            <td><?php echo $value['Embarcador']['razao_social'] ?></td>
            <td><?php echo $value['Transportador']['razao_social'] ?></td>
            <td><?php echo $value['Profissional']['Nome'] ?></td>
            <td><?php echo $value['Veiculo']['placa'] ?></td>
        </tr>
    <?php endforeach; ?>        
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8">
                <div class="xpull-right">
                    <strong>Total</strong>
                    <?php echo $this->Paginator->params['paging']['Sinistro']['count']; ?>
                </div>
            </td>
        </tr>
    </tfoot>

</table>
<div class='row-fluid'>
    <div class='numbers span6'>
        <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
      <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){       
       jQuery("div#filtros").slideToggle("slow");        
    });', false);?>
<? }else{?>
    <div class="alert">Nenhum registro encontrado.</div>
<?} ?>