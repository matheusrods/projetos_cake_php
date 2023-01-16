<h5>Autotrac</h5>
<div class='row-fluid'>
    <table class="table table-striped table-bordered" style="width: 8900px;">
        <thead>
            <?php 
                $totais = array();
                $i = 0;
                foreach($utilizacoes_autotrac[0][0] as $key => $valor){ 
                    if($i > 1){
                        $totais[$key] = 0;
                        echo '<th class="numeric">'.(ucwords(str_replace("_", " ", $key))).'</th>';
                    }else{
                        echo '<th>'.(ucfirst(str_replace("_", " ", $key))).'</th>';
                    }
                    $i++;
            ?>
            <?php } ?>
        </thead>
        <tbody>
            <?php                 
                foreach($utilizacoes_autotrac as $key => $valor){ 
                    $i = 0;
                ?>
            <tr>
                <?php foreach($utilizacoes_autotrac[0][0] as $chave => $conteudo){ ?>
                    <?php 
                        if($i > 1){
                            $totais[$chave]  += $utilizacoes_autotrac[$key][0][$chave];
                            echo('<td class="numeric"> '.number_format($utilizacoes_autotrac[$key][0][$chave], 2,',','.').'</td>');
                        }else{
                            echo('<td>'.$utilizacoes_autotrac[$key][0][$chave].'</td>');
                        }
                        $i++;

                    ?>
                
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
        
        <tfoot>
            <tr>
                <td class="numeric"><b><?=count($utilizacoes_autotrac)?></b></td>
                <td class="numeric"><b>TOTAL</b></td>
                
                <?php foreach($totais as $valor){ ?>
                    <td class="numeric"><b><?php echo number_format($valor,2,',','.'); ?></b></td>
                <?php } ?>
            </tr>
        </tfoot>
        
    </table>
</div>