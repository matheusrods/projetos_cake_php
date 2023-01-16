<table>
    <thead>
        <tr>
            <th>Sistema</th>
            <th>Ação</th>
        </tr>
    </thead>
    <?php foreach($sistemas as $sistema):?>
        <tr>
            <td><?php echo $sistema['Sistema']['descricao']?></td>
            <td></td>
        </tr>
    <?php endforeach;?>
</table>