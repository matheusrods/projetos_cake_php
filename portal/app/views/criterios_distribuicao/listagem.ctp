<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th><?= $this->Paginator->sort('Ordem', 'cdis_nivel') ?></th>
            <th class="input-large"><?= $this->Paginator->sort('Embarcador', 'TEmbarcador.pjur_razao_social') ?></th>
            <th class="input-large"><?= $this->Paginator->sort('Transportador', 'TTransportador.pjur_razao_social') ?></th>
            <th class="input-large"><?= $this->Paginator->sort('Alvo Origem', 'TRefeReferencia.refe_descricao') ?></th>
            <th><?= $this->Paginator->sort('Produto', 'TProdProduto.prod_descricao') ?></th>
            <th><?= $this->Paginator->sort('Tecnologia', 'TTecnTecnologia.tecn_descricao') ?></th>
            <th><?= $this->Paginator->sort('T. Transporte', 'TTraTipoTransporte.ttra_descricao') ?></th>
            <th><?= $this->Paginator->sort('A. Atuação', 'TAatuAreaAtuacao.aatu_descricao') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Min', 'TPfvaPgFaixaValor.pfva_valor_minimo') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Max', 'TPfvaPgFaixaValor.pfva_valor_maximo') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listagem as $key => $cdis): ?>

        <tr>
            <td>
                <?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'row-move-up icon-chevron-up', 'title' => 'Subir Nível', 'nivel' => $cdis['TCdisCriterioDistribuicao']['cdis_nivel'])) ?>
                <?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'row-move-down icon-chevron-down', 'title' => 'Descer Nível', 'nivel' => $cdis['TCdisCriterioDistribuicao']['cdis_nivel'])) ?>
            </td>
            <td class="input-mini">
				<?= $cdis['TCdisCriterioDistribuicao']['cdis_nivel'] ?>
			</td>
            <td>
				<?= $cdis['TEmbarcador']['pjur_razao_social'] ?>
			</td>
            <td>
                <?= $cdis['TTransportador']['pjur_razao_social'] ?>
            </td>
            <td>
                <?= $cdis['TRefeReferencia']['refe_descricao'] ?>
            </td>
            <td>
                <?= $cdis['TProdProduto']['prod_descricao'] ?>
            </td>
            <td>
                <?= $cdis['TTecnTecnologia']['tecn_descricao'] ?>
            </td>
            <td>
                <?= $cdis['TTtraTipoTransporte']['ttra_descricao'] ?>
            </td>
            <td>
                <?= $cdis['TAatuAreaAtuacao']['aatu_descricao'] ?>
            </td>
            <td class="numeric">
                <?= number_format($cdis['TCdfvCriterioFaixaValor']['cdfv_valor_minimo'], 2, ',', '.') ?>
            </td>
            <td class="numeric">
                <?= number_format($cdis['TCdfvCriterioFaixaValor']['cdfv_valor_maximo'], 2, ',', '.') ?>
            </td>
            <td class="numeric">
				<?php echo $this->Html->link('', array('action' => 'editar', $cdis['TCdisCriterioDistribuicao']['cdis_codigo']), array('class' => 'icon-edit excluir', 'title' => 'Editar')) ?>
                <?php echo $this->Html->link('', array('action' => 'excluir', $cdis['TCdisCriterioDistribuicao']['cdis_codigo']), array('class' => 'icon-trash excluir', 'title' => 'Excluir'), 'Confirma a exclusão?') ?>
			</td>
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

<?php echo $this->Javascript->codeBlock('
    $(function(){
        var db_click = 1;
        $(".row-move-up").click(function(){
            if(db_click){
                db_click  = 0;
                
                var nivel = $(this).attr("nivel");
                $.ajax({
                    url: baseUrl + "criterios_distribuicao/sobe_nivel/"+ nivel +"/" + Math.random(),
                    success: function(data){
                        if(data){
                            alert(data);
                        } else {
                            atualizaListaCriteriosDistribuicao();            
                        }
                    },
                    complete: function(){
                        db_click = 1;
                    }
                })
            }

            return false;
        });
    
        $(".row-move-down").click(function(){
            if(db_click){
                db_click  = 0;

                var nivel = $(this).attr("nivel");
                $.ajax({
                    url: baseUrl + "criterios_distribuicao/desce_nivel/"+ nivel +"/" + Math.random(),
                    success: function(data){
                        if(data){
                            alert(data);
                        } else {
                            atualizaListaCriteriosDistribuicao();            
                        }
                    },
                    complete: function(){
                        db_click = 1;
                    }
                });
            }
            return false;
        });
    });
'); ?>