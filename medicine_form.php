<?php
// Shared form used by add.php (empty) and edit.php (pre-populated). Expects $m, $cats, $errors.
foreach ($errors as $er) echo '<div class="alert bad">' . e($er) . '</div>';
?>
<form class="box validate" method="post" novalidate style="max-width:600px">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <label>Medicine name</label><input name="name" value="<?= e($m['name']) ?>" data-rule="required min:2">
  <label>Category</label><select name="category_id" data-rule="required"><option value="">Choose…</option>
    <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $m['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
  </select>
  <label>Supplier</label><input name="supplier" value="<?= e($m['supplier']) ?>" data-rule="required">
  <label>Quantity in stock</label><input name="quantity" type="number" min="0" value="<?= e($m['quantity']) ?>" data-rule="required number">
  <label>Reorder level</label><input name="reorder_level" type="number" min="0" value="<?= e($m['reorder_level']) ?>" data-rule="required number">
  <label>Unit price (UGX)</label><input name="unit_price" type="number" min="0" step="1" value="<?= e($m['unit_price']) ?>" data-rule="required number">
  <label>Expiry date</label><input name="expiry_date" type="date" value="<?= e($m['expiry_date']) ?>" data-rule="required">
  <button class="btn" type="submit">Save medicine</button> <a href="dashboard.php">Cancel</a>
</form>
