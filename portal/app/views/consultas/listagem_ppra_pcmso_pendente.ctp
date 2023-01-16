<?php if (!empty($listagem)):?>
    <?php
        echo $paginator->options(array('update' => 'div.lista')); 
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th >Código</th>
                <th >Cliente</th>
                <!-- <th ></th> -->
                <th >PGR</th>
                <th >PCMSO</th>
            </tr>        
        </thead>
        <tbody>
            <?php 
            //pr($listagem);
            foreach ($listagem as $list): 
                
                $w = false;
                $warning = '...';

                $codigo_cliente = $list['Cliente']['codigo'];

                /* Tratamento do Status */
                $class_prefix = 'badge badge-empty badge-';

                // Status PGR
                $output_link_ppra = '';
                $st_ppra = $list['PPRA']['STATUS_PPRA'];
                $ppra_class = $class_prefix.$StatusStyle[$st_ppra]['class'];
                $ppra_title = $StatusStyle[$st_ppra]['title'];
                $ppra_link = $StatusStyle[$st_ppra]['link'];

                $output_link_ppra = $this->Html->div( '', $ppra_title, array( 'class' => $ppra_class ) );

                if( $ppra_link ){
                    // Identifica se é um link tipo array
                    if( is_array($ppra_link) ){
                        $ppra_link[] = $codigo_cliente;
                        $ppra_link[] = 'ppra';  
                        $output_link_ppra = $this->Html->link( $ppra_title, $ppra_link, array( 'class' => $ppra_class ) );
                    }  
                    
                }  else {
                    $w = true;
                }

                // Status PCMSO
                $output_link_pcmso = '';
                $st_pcmso = $list['PCMSO']['STATUS_PCMSO'];
                $pcmso_class = $class_prefix.$StatusStyle[$st_pcmso]['class'];
                $pcmso_title = $StatusStyle[$st_pcmso]['title'];            
                $pcmso_link = $StatusStyle[$st_pcmso]['link'];

                $output_link_pcmso = $this->Html->div( '', $pcmso_title , array( 'class' => $pcmso_class ));

                if( $pcmso_link ){
                    // Identifica se é um link tipo array
                    if( is_array($pcmso_link) ) {
                        $pcmso_link[] = $codigo_cliente;
                        $pcmso_link[] = 'pcmso';  
                        $output_link_pcmso = $this->Html->link( $pcmso_title, $pcmso_link, array( 'class' => $pcmso_class ) );                 
                    }                 
                    
                } else {
                    $w = true;
                }

                //if( $w ) $warning = $this->Html->tag( 'div', 'Problema com hierarquia', array( 'class' => 'badge badge-empty badge-warning' ) );
            ?>
            <tr>
                <td><?= $list['Cliente']['codigo'] ?></td>
                <td><?= $list['Cliente']['nome_fantasia'] ?></td>
                <!-- <td><?= $warning ?></td> -->
                <td><?= $output_link_ppra ?></td>
                <td><?= $output_link_pcmso; ?></td>
            </tr>
            <?php endforeach; ?>        
        </tbody>
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

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado!</div>
<?php endif;?>
