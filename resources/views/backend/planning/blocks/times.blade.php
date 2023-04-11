<option value="0"> -- </option>
<?php for ($i = 1; $i < 24; $i++): ?>
  <option value="<?php echo $i ?>" <?php if ($i == $s) { echo 'selected'; } ?>>
    <?php if ($i < 10): ?>
              <?php if ($i == 0): ?>
        --
      <?php else: ?>
        0<?php echo $i ?>
      <?php endif ?>

    <?php else: ?>
      <?php echo $i ?>
    <?php endif ?>
  </option>
<?php endfor ?>