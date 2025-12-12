<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

/*
* Load required classes
*/  

require_once("inc/class/class.inventory.php");
require_once("inc/class/class.user.php");
require_once("inc/class/class.option.php");

/*
* Initiate classes
*/  
$im = new InventoryManager();
$um = new UserManager;
$om = new OptionManager();


// --- Helpers ---
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

//admin enabld
$adminEnabled = "disabled";

if($user->level === 1) {
  $adminEnabled = "enabled";
}

// Load existing when id present
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

$row = [
  'id'        => '',
  'relation' => '',
  'barcode'     => '',
  'quality'   => '',
  'location'  => '',
  'processed' => 0,
  'date'      => date('Y-m-d\TH:i') // default now for new record (HTML datetime-local format)
];

$errors = [];
$flash  = null;

// Fetch current row for edit mode
if ($isEdit) {
    $row = $id ? $im->getById($id) : null;
}
?>
<!-- HTML Output Below  !-->
<link rel="stylesheet" href="inc/style/inventory.css">
<div class="card">
  <h1><?= $isEdit ? 'Bewerk voorraad' : 'Nieuwe voorraad' ?></h1>

  <form id="inv-form" autocomplete="off">

  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= $isEdit ? htmlspecialchars($row['id']) : '' ?>">

  <div class="form-field">
    <label class="label-as-input">Rolnummer *</label>
    <input type="text" name="barcode" required value="<?= htmlspecialchars($row['barcode']) ?>">
  </div>

  <div class="form-field">
    <label class="label-as-input">Relatie *</label>
    <input type="text" name="relation" required value="<?= htmlspecialchars($row['relation']) ?>">
  </div>

  <div class="form-field">
    <label class="label-as-input">Quality *</label>
    <input type="text" name="quality" required value="<?= htmlspecialchars($row['quality']) ?>">
  </div>

  <div class="form-field">
    <label class="label-as-input">Lengte</label>
    <input type="number" step="0.01" name="lengte" value="<?= htmlspecialchars($row['lengte'] ?? '') ?>">
  </div>

  <div class="form-field">
    <label class="label-as-input">Breedte</label>
    <input type="number" step="0.01" name="breedte" value="<?= htmlspecialchars($row['breedte'] ?? '') ?>">
  </div>

  <div class="form-field">
    <label class="label-as-input">Location *</label>
    <input type="text" name="location" required value="<?= htmlspecialchars($row['location']) ?>">
  </div>

  <div class="form-field">
  <label class="label-as-input">
    <input type="checkbox" name="processed" value="1" <?= !empty($row['processed']) ? 'checked' : ''; $adminEnabled;?>>
    Verwerkt
  </label>
  <input type="hidden" name="date" required value="<?= htmlspecialchars(str_replace(' ', 'T', substr($row['date'] ?: date('Y-m-d H:i:s'), 0, 16))) ?>">
  </div>
  
  <div class="actions">
    <button class="ui-button ui-corner-all" type="submit" id="btn-save"><?= $isEdit ? 'Opslaan' : 'Aanmaken' ?></button>
    <?php if ($isEdit): ?>
      <button type="button" id="btn-delete" data-id="<?= (int)$row['id'] ?>">Verwijderen</button>
    <?php endif; ?>
  </div>
</form>

<div id="msg" style="margin-top:10px;"></div>

<!-- jQuery already on page; if not, include it -->
<script>
(function () {
  const form = document.getElementById('inv-form');
  const msg  = document.getElementById('msg');
  const btnSave = document.getElementById('btn-save');

  function showMsg(text, ok) {
    msg.textContent = text;
    msg.style.color = ok ? '#065f46' : '#991b1b';
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    // Normalize checkbox & date
    const fd = new FormData(form);
    // Ensure processed is 0/1
    if (!fd.has('processed')) fd.set('processed', '0');

    // Convert datetime-local to "YYYY-MM-DD HH:MM:SS"
    const raw = fd.get('date'); // e.g. 2025-09-25T14:30
    if (raw) fd.set('date', raw.replace('T', ' ') + (raw.length === 16 ? ':00' : ''));

    btnSave.disabled = true;
    btnSave.textContent = 'Bezig…';

    try {
      const res = await fetch('pages/inventory/process.php', {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      const data = await res.json();
      if (!res.ok || !data.ok) throw new Error(data.message || 'Onbekende fout');

      showMsg(data.message || 'Opgeslagen', true);

      // If created, replace URL with edit?id=NEW
      if (data.id && (fd.get('id') === '' || fd.get('id') === null)) {
        const params = new URLSearchParams(location.search);
        params.set('id', data.id);
        history.replaceState({}, '', location.pathname + '?' + params.toString());
        // also inject id into hidden field so subsequent saves are updates
        form.querySelector('input[name="id"]').value = data.id;
      }
    } catch (err) {
      showMsg(err.message, false);
    } finally {
      btnSave.disabled = false;
      btnSave.textContent = 'Opslaan';
    }
  });

  // Delete (optional)
  const btnDelete = document.getElementById('btn-delete');
  if (btnDelete) {
    btnDelete.addEventListener('click', async function () {
      if (!confirm('Weet je zeker dat je dit item wilt verwijderen?')) return;
      const id = this.getAttribute('data-id');
      const fd = new FormData();
      fd.set('action', 'delete');
      fd.set('id', id);

      try {
        const res = await fetch('process.php', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }});
        const data = await res.json();
        if (!res.ok || !data.ok) throw new Error(data.message || 'Verwijderen mislukt');
        showMsg('Verwijderd', true);
        // redirect back to list after a moment (or do in JS)
        setTimeout(() => { window.location.href = 'inventory_list.php'; }, 600);
      } catch (e) {
        showMsg(e.message, false);
      }
    });
  }
})();
</script>
  </div>