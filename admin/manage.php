<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/tables.php';
require_admin();

$tables = admin_tables();
$table = $_GET['table'] ?? '';
if (!isset($tables[$table])) {
    http_response_code(404);
    exit('Unknown table.');
}
$def = $tables[$table];
$fields = $def['fields'];
$pageTitle = $def['label'];
$baseUrl = 'manage.php?table=' . urlencode($table);

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// ----- POST actions -----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $stmt = db()->prepare("DELETE FROM `$table` WHERE id = ?");
        $stmt->execute([(int) $_POST['id']]);
        flash_set('success', 'Item deleted.');
        redirect($baseUrl);
    }

    if ($postAction === 'toggle') {
        $stmt = db()->prepare("UPDATE `$table` SET is_active = 1 - is_active WHERE id = ?");
        $stmt->execute([(int) $_POST['id']]);
        flash_set('success', 'Visibility updated.');
        redirect($baseUrl);
    }

    if ($postAction === 'save') {
        $values = [];
        $errors = [];
        foreach ($fields as $name => $meta) {
            if ($meta['type'] === 'checkbox') {
                $values[$name] = isset($_POST[$name]) ? 1 : 0;
                continue;
            }
            $value = trim($_POST[$name] ?? '');
            if (($meta['required'] ?? false) && $value === '') {
                $errors[] = $meta['label'] . ' is required.';
            }
            if ($meta['type'] === 'select' && $value !== '' && !isset($meta['options'][$value])) {
                $errors[] = 'Invalid value for ' . $meta['label'] . '.';
            }
            $values[$name] = $value;
        }
        $values['sort_order'] = (int) ($_POST['sort_order'] ?? 0);
        $values['is_active'] = isset($_POST['is_active']) ? 1 : 0;

        $editId = (int) ($_POST['id'] ?? 0);
        if ($errors) {
            flash_set('error', implode(' ', $errors));
        } else {
            $columns = array_keys($values);
            if ($editId > 0) {
                $set = implode(', ', array_map(fn ($c) => "`$c` = ?", $columns));
                $stmt = db()->prepare("UPDATE `$table` SET $set WHERE id = ?");
                $stmt->execute([...array_values($values), $editId]);
                flash_set('success', 'Item updated.');
            } else {
                $cols = implode(', ', array_map(fn ($c) => "`$c`", $columns));
                $placeholders = implode(', ', array_fill(0, count($columns), '?'));
                $stmt = db()->prepare("INSERT INTO `$table` ($cols) VALUES ($placeholders)");
                $stmt->execute(array_values($values));
                flash_set('success', 'Item added.');
            }
            redirect($baseUrl);
        }
        redirect($baseUrl . '&action=' . ($editId > 0 ? 'edit&id=' . $editId : 'add'));
    }
}

// ----- data for views -----
$editRow = null;
if ($action === 'edit' && $id > 0) {
    $stmt = db()->prepare("SELECT * FROM `$table` WHERE id = ?");
    $stmt->execute([$id]);
    $editRow = $stmt->fetch();
    if (!$editRow) {
        flash_set('error', 'Item not found.');
        redirect($baseUrl);
    }
}
$rows = ($action === 'list') ? db()->query("SELECT * FROM `$table` ORDER BY sort_order, id")->fetchAll() : [];

require __DIR__ . '/includes/admin_header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<h1><?= $editRow ? 'Edit' : 'Add' ?>: <?= e($def['label']) ?></h1>
<form method="post" action="<?= e($baseUrl) ?>" class="edit-form">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int) ($editRow['id'] ?? 0) ?>">

  <?php foreach ($fields as $name => $meta): $value = $editRow[$name] ?? ''; ?>
  <div class="field">
    <?php if ($meta['type'] === 'checkbox'): ?>
    <label class="check"><input type="checkbox" name="<?= e($name) ?>" value="1" <?= !empty($value) ? 'checked' : '' ?>> <?= e($meta['label']) ?></label>
    <?php elseif ($meta['type'] === 'select'): ?>
    <label for="f-<?= e($name) ?>"><?= e($meta['label']) ?></label>
    <select id="f-<?= e($name) ?>" name="<?= e($name) ?>">
      <?php foreach ($meta['options'] as $optValue => $optLabel): ?>
      <option value="<?= e($optValue) ?>" <?= $value === $optValue ? 'selected' : '' ?>><?= e($optLabel) ?></option>
      <?php endforeach; ?>
    </select>
    <?php elseif ($meta['type'] === 'textarea'): ?>
    <label for="f-<?= e($name) ?>"><?= e($meta['label']) ?></label>
    <textarea id="f-<?= e($name) ?>" name="<?= e($name) ?>" rows="7" <?= !empty($meta['required']) ? 'required' : '' ?>><?= e($value) ?></textarea>
    <?php else: ?>
    <label for="f-<?= e($name) ?>"><?= e($meta['label']) ?></label>
    <input type="text" id="f-<?= e($name) ?>" name="<?= e($name) ?>" value="<?= e($value) ?>" <?= !empty($meta['required']) ? 'required' : '' ?>>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>

  <div class="field-row">
    <div class="field">
      <label for="f-sort">Sort order</label>
      <input type="number" id="f-sort" name="sort_order" value="<?= (int) ($editRow['sort_order'] ?? 0) ?>">
    </div>
    <div class="field">
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= ($editRow['is_active'] ?? 1) ? 'checked' : '' ?>> Visible on website</label>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn-primary">Save</button>
    <a href="<?= e($baseUrl) ?>" class="btn-ghost">Cancel</a>
  </div>
</form>

<?php else: ?>
<div class="page-head">
  <h1><?= e($def['label']) ?></h1>
  <a href="<?= e($baseUrl) ?>&action=add" class="btn-primary">+ Add new</a>
</div>

<div class="table-wrap">
<table class="data-table">
  <thead>
    <tr>
      <?php foreach ($def['list'] as $col): ?>
      <th><?= e($fields[$col]['label'] ?? ucfirst($col)) ?></th>
      <?php endforeach; ?>
      <th>Order</th><th>Visible</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$rows): ?>
    <tr><td colspan="<?= count($def['list']) + 3 ?>" class="muted">No items yet.</td></tr>
    <?php endif; ?>
    <?php foreach ($rows as $row): ?>
    <tr class="<?= $row['is_active'] ? '' : 'inactive' ?>">
      <?php foreach ($def['list'] as $col): ?>
      <td><?= e(mb_strimwidth((string) $row[$col], 0, 70, '…')) ?></td>
      <?php endforeach; ?>
      <td><?= (int) $row['sort_order'] ?></td>
      <td>
        <form method="post" action="<?= e($baseUrl) ?>" class="inline">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
          <button type="submit" class="pill <?= $row['is_active'] ? 'on' : 'off' ?>"><?= $row['is_active'] ? 'Visible' : 'Hidden' ?></button>
        </form>
      </td>
      <td class="actions">
        <a href="<?= e($baseUrl) ?>&action=edit&id=<?= (int) $row['id'] ?>" class="btn-ghost sm">Edit</a>
        <form method="post" action="<?= e($baseUrl) ?>" class="inline" onsubmit="return confirm('Delete this item?');">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
          <button type="submit" class="btn-danger sm">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
