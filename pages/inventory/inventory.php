<?php
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
?>
<link rel="stylesheet" href="inc/style/inventory.css">

<h2>Inventory</h2>

<div class="filters" id="filterdiv">
  <input type="text" id="f-barcode" placeholder="Rolnummer">
  <select id="f-location"><option value="">All locations</option></select>
  <select id="f-processed">
    <option value="">Alles</option>
    <option value="1">Uitgeboekt</option>
    <option value="0">Voorraad</option>
  </select>
  <input type="date" id="f-date-from" placeholder="Van datum">
  <input type="date" id="f-date-to" placeholder="Tot datum">
</div>

<!-- Update THEAD to match your data (9 visible columns) -->
<table id="invTable" class="display data-table" style="width:100%">
  <thead>
    <tr>
      <th class="ui-corner-tl">id</th>
      <th>Rolnummer</th>
      <th>Kwaliteit</th>
      <th>Lengte</th>
      <th>Breedte</th>
      <th>Locatie</th>
      <th>Verwerkt</th>
      <th>Datum</th>
      <th class="ui-corner-tr">Gewijzigd</th>
    </tr>
  </thead>
  <tbody></tbody>
  <tfoot>
    <tr>
      <!-- colspan must match number of columns -->
      <td colspan="9" class="ui-corner-bl ui-corner-br">&nbsp;</td>
    </tr>
  </tfoot>
</table>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
  // PHP -> JS data (includes: id, barcode, quality, lengte, breedte, location, processed, date, modified)
  const data = <?php echo $im->listInventory(); ?>;

  // Build unique locations for the dropdown
  const locations = [...new Set(data.map(r => r.location).filter(Boolean))].sort();
  const locSel = document.getElementById('f-location');
  locations.forEach(loc => {
    const opt = document.createElement('option');
    opt.value = loc;
    opt.textContent = loc;
    locSel.appendChild(opt);
  });

  // Keep column indexes in one place to avoid drift
  const COL = {
    id: 0,
    barcode: 1,
    quality: 2,
    lengte: 3,
    breedte: 4,
    location: 5,
    processed: 6,   // badge text "Ja/Nee"
    date: 7,
    modified: 8
  };

  // Date range filter (uses the 'date' column)
  $.fn.dataTable.ext.search.push(function (settings, dataRow) {
    const dateStr = dataRow[COL.date];        // index for 'Datum' column
    const fromVal = document.getElementById('f-date-from').value;
    const toVal   = document.getElementById('f-date-to').value;

    if (!fromVal && !toVal) return true;

    const rowTime = new Date(String(dateStr).replace(' ', 'T')).getTime();
    if (isNaN(rowTime)) return false;

    if (fromVal) {
      const fromTime = new Date(fromVal + 'T00:00:00').getTime();
      if (rowTime < fromTime) return false;
    }
    if (toVal) {
      const toTime = new Date(toVal + 'T23:59:59').getTime();
      if (rowTime > toTime) return false;
    }
    return true;
  });

// Helper: build the router URL
  function buildEditUrl(id) {
    // Matches your router: index.php?page=inventory%2Fedit&id=9
    return 'index.php?page=' + encodeURIComponent('inventory/edit') + '&id=' + encodeURIComponent(id);
  }

  const table = $('#invTable').DataTable({
    data,
    deferRender: true,
    pageLength: 25,
    order: [[COL.date, 'desc']],
    columns: [
      { data: 'id' },
      { data: 'barcode' },
      { data: 'quality' },
      {
        data: 'lengte',
        render: (v, t) => (v==null||v==='') ? '' : (t==='display' ? Number(v).toLocaleString() : v)
      },
      {
        data: 'breedte',
        render: (v, t) => (v==null||v==='') ? '' : (t==='display' ? Number(v).toLocaleString() : v)
      },
      { data: 'location' },
      {
        data: 'processed',
        render: function (val, type) {
          const yes = String(val) === '1';
          if (type !== 'display') return yes ? 'Ja' : 'Nee';
          return `<span class="badge ${yes ? 'yes' : 'no'}">${yes ? 'Ja' : 'Nee'}</span>`;
        }
      },
      {
        data: 'date',
        render: (v, t) => {
          if (!v) return '';
          if (t === 'display') {
            const d = new Date(String(v).replace(' ', 'T'));
            return isNaN(d) ? v : d.toLocaleString();
          }
          return v;
        }
      },
      {
        data: 'modified',
        render: (v, t) => {
          if (!v) return '';
          if (t === 'display') {
            const d = new Date(String(v).replace(' ', 'T'));
            return isNaN(d) ? v : d.toLocaleString();
          }
          return v;
        }
      }
    ],
    language: {
      search: "Zoeken:",
      lengthMenu: "Toon _MENU_ resultaten per pagina",
      info: "Resultaat _START_ tot _END_ van _TOTAL_",
      infoEmpty: "Geen resultaten beschikbaar",
      infoFiltered: "(gefilterd uit _MAX_ totaal)",
      zeroRecords: "Geen overeenkomende records gevonden",
      paginate: { first:"Eerste", last:"Laatste", next:"Volgende", previous:"Vorige" }
    },
    // Tag each row so it's clickable and accessible
    createdRow: function (row, rowData) {
      row.classList.add('row-link');
      row.setAttribute('data-href', buildEditUrl(rowData.id));
      row.setAttribute('role', 'link');
      row.setAttribute('tabindex', '0');
      row.setAttribute('aria-label', 'Bewerk record #' + rowData.id);
      // Optional: tooltip
      row.title = 'Bewerk record #' + rowData.id;
    }
  });

  // Navigate on click
  $('#invTable tbody').on('click', 'tr.row-link', function (e) {
    const url = this.dataset.href;
    if (!url) return;

    // Ctrl/Cmd click = new tab
    if (e.ctrlKey || e.metaKey) {
      window.open(url, '_blank');
    } else {
      window.location.href = url;
    }
  });

  // Middle-click support (auxclick = mouse button 1)
  $('#invTable tbody').on('auxclick', 'tr.row-link', function (e) {
    if (e.button === 1 && this.dataset.href) {
      window.open(this.dataset.href, '_blank');
    }
  });

  // Keyboard: Enter to open
  $('#invTable tbody').on('keydown', 'tr.row-link', function (e) {
    if (e.key === 'Enter' && this.dataset.href) {
      window.location.href = this.dataset.href;
    }
  });

  // --- keep your existing filters below ---
  $('#f-barcode').on('keyup change', function () {
    table.column(COL.barcode).search(this.value).draw();
  });
  $('#f-location').on('change', function () {
    const val = this.value;
    table.column(COL.location).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
  });
  $('#f-processed').on('change', function () {
    const val = this.value;
    const needle = val === '' ? '' : (val === '1' ? '^Ja$' : '^Nee$');
    table.column(COL.processed).search(needle, true, false).draw();
  });
  $('#f-date-from, #f-date-to').on('change', function () { table.draw(); });
</script>