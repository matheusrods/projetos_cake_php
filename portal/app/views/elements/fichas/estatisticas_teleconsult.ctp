<div class='row-fluid' style='overflow-x:auto'>
    <table class="table table-striped table-bordered" style='width:100%;max-width:none'>
        <thead>
            <tr>
                <th><?= $this->Html->link('Serviço', 'javascript:void(0)') ?></th>
				<?php if(!empty($dados)): ?>
					<?php foreach ($head_title as $title): ?>
						<th class='input-small numeric'><?php echo $this->Html->link($title, 'javascript:void(0)') ?></th>
					<?php endforeach ?>
				<?php endif ?>
                <th class='input-small numeric'><?= $this->Html->link('Total', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($dados): ?>
                <?php foreach($dados as $dado): ?>
					<?php $qtd_total = 0 ?>
                    <tr>
                        <td><?= $dado['name'] ?></td>
						<?php foreach($dado['values'] as $valor): ?>
	                        <td class='numeric'><?= $valor == 0 ? '': $this->Buonny->moeda($valor, array('nozero' => true, 'places' => 0)) ?></td>
							<?php $qtd_total += $valor ?>
						<?php endforeach ?>
						<td class='numeric'><?= $this->Buonny->moeda($qtd_total, array('nozero' => true, 'places' => 0)) ?></td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
</div>
<?php echo $this->Buonny->link_css('tablesorter') ?>
<?php echo $this->Buonny->link_js('jquery.tablesorter.min') ?>
<?= $this->addScript($this->Javascript->codeBlock("
    jQuery(document).ready(function() {
        jQuery('table.table').tablesorter({
            sortList: [[1,0],[0,1]],
        });
    })"
)) ?>