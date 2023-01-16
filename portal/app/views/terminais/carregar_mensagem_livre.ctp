<?php 
    echo $paginator->options(array('update' => 'div#msg_livre')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Mensagem</th>
            <th>Data Leitura</th>
            <th>Latitude/Longitude</th>
        </tr>
    </thead>
    <tbody>
        <?php if( isset($msg_livre) ): ?>
            <?php foreach($msg_livre as $msg): ?>
                <tr>
                    <td><?php echo $msg['TRmliRecebimentoMensagLivre']['rmli_texto']?></td>
                    <td><?php echo $msg['TReceRecebimento']['rece_data_computador_bordo']; ?></td>
                    <td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $this->Text->truncate($msg['TRposRecebimentoPosicao']['rpos_latitude'].' '.$msg['TRposRecebimentoPosicao']['rpos_longitude'], 70)), $msg['TRposRecebimentoPosicao']['rpos_latitude'], $msg['TRposRecebimentoPosicao']['rpos_longitude']) ?></td>
                </tr>
            <?php endforeach; ?>
       <?php  endif;?>
    </tbody>
</table>
<div class="ocultar">
	<?php
		if( isset($msg_livre) ) {
			echo $this->Paginator->prev('« Anterior ', null, null, array('class' => 'disabled'));
			echo $this->Paginator->numbers();
			echo $this->Paginator->next(' Proximo » ', null, null, array('class' => 'disabled'));

			if(isset($this->Paginator->params['paging']['TRmliRecebimentoMensagLivre']['count'])) {
				$total_msg = $this->Paginator->params['paging']['TRmliRecebimentoMensagLivre']['count'];
			} else {
				$total_msg = 0;
			}

			if(isset($this->Paginator->params['paging']['TRmliRecebimentoMensagLivre']['pageCount']))
				$total_paginas = $this->Paginator->params['paging']['TRmliRecebimentoMensagLivre']['pageCount'];
			else
				$total_paginas = 0;
			echo $this->Paginator->counter(array('format' => 'Página %page% de '.preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/", ".", $total_paginas).', mostrando %current% registros de um total de ' . preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/", ".", $total_msg) ));
		}
	?>
</div>
<?php echo $this->Js->writeBuffer(); ?>