<!DOCTYPE html>
<html>

<head></head>

<body>
  <table>
    <thead>
      <tr>
        <?php foreach (array_keys($riscosArr[0]) as $nomeColuna) : ?>
          <th><?php echo $nomeColuna; ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($riscosArr as $numLinha => $linha) : ?>
        <tr>
          <?php foreach ($linha as $nomColuna => $valor) : ?>
            <td><?php echo $valor;  ?></td>
          <?php endforeach;  ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>

</html>