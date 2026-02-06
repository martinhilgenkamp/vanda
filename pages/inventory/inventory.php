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
  <div id="csv" class="csv"><img src="images/excel.png"></div>
  <input type="text" id="f-barcode" placeholder="Rolnummer">
  <select id="f-location"><option value="">All locations</option></select>
  <select id="f-processed">
    <option value="0">Voorraad</option>
    <option value="">Alles</option>
    <option value="1">Uitgeboekt</option>
  </select>
  <input type="date" id="f-date-from" placeholder="Van datum">
  <input type="date" id="f-date-to" placeholder="Tot datum">
</div>

<!-- Update THEAD to match your data (9 visible columns) -->
<table id="invTable" class="display data-table" style="width:100%">
  <thead>
    <tr>
      <th class="ui-corner-tl">id</th>
      <th>Relatie</th>
      <th>Rolnummer</th>
      <th>Kwaliteit</th>
      <th>Lengte</th>
      <th>Breedte</th>
      <th>Locatie</th>
      <th>Verwerkt</th>
      <th>Datum</th>
      <th>Dagen</th>
      <th class="ui-corner-tr">Gewijzigd</th>
    </tr>
  </thead>
  <tbody></tbody>
  <tfoot>
    <tr>
      <!-- colspan must match number of columns -->
      <td colspan="11" class="ui-corner-bl ui-corner-br">&nbsp;</td>
    </tr>
  </tfoot>
</table>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
  // PHP -> JS data
  // Order from backend: id, barcode, relation, quality, lengte, breedte, location, processed, date, modified
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

  // Central column index mapping (MATCHES TABLE, NOT DATABASE LOGIC)
  const COL = {
    id: 0,
    relation: 1,
    quality: 2,
    barcode: 3,
    lengte: 4,
    breedte: 5,
    location: 6,
    processed: 7,
    date: 8,
    daysInStock: 9,
    modified: 10
  };

  // Date range filter (uses the 'date' column)
  $.fn.dataTable.ext.search.push(function (_, row) {
    const dateStr = row[COL.date];
    const fromVal = document.getElementById('f-date-from').value;
    const toVal   = document.getElementById('f-date-to').value;

    if (!fromVal && !toVal) return true;

    const rowTime = new Date(String(dateStr).replace(' ', 'T')).getTime();
    if (isNaN(rowTime)) return false;

    if (fromVal && rowTime < new Date(fromVal + 'T00:00:00').getTime()) return false;
    if (toVal && rowTime > new Date(toVal + 'T23:59:59').getTime()) return false;

    return true;
  });

  // Helper: build edit URL
  function buildEditUrl(id) {
    return 'index.php?page=' + encodeURIComponent('inventory/edit') + '&id=' + encodeURIComponent(id);
  }

  const table = $('#invTable').DataTable({
    data,
    deferRender: true,
    pageLength: 25,
    order: [[COL.date, 'desc']],
    columns: [
      { data: 'id' },          // ID
      { data: 'relation' },    // Relatie
      { data: 'barcode' },     // Barcode
      { data: 'quality' },     // Quality
      {
        data: 'lengte',
        render: (v, t) =>
          v == null || v === '' ? '' : (t === 'display' ? Number(v).toLocaleString() : v)
      },
      {
        data: 'breedte',
        render: (v, t) =>
          v == null || v === '' ? '' : (t === 'display' ? Number(v).toLocaleString() : v)
      },
      { data: 'location' },
      {
        data: 'processed',
        render: (v, t) => {
          const yes = String(v) === '1';
          if (t !== 'display') return yes ? 'Ja' : 'Nee';
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
        data: null,
        title: 'Dagen',
        render: (row, t) => {
          // Keep comments in English
          const parse = (s) => {
            if (!s) return null;
            const d = new Date(String(s).replace(' ', 'T'));
            return isNaN(d) ? null : d;
          };

          const start = parse(row?.date);
          const end = parse(row?.modified) || new Date(); // if not modified: today

          if (!start || !end) return (t === 'display') ? '' : null;

          // Normalize to local midnight to avoid timezone/hour causing -1
          const a = new Date(start.getFullYear(), start.getMonth(), start.getDate()).getTime();
          const b = new Date(end.getFullYear(), end.getMonth(), end.getDate()).getTime();

          let days = Math.round((b - a) / 86400000);

          // Clamp negative results (data issues / clock issues)
          if (days < 0) days = 0;

          return (t === 'display') ? days.toLocaleString() : days;
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
      paginate: { first: "Eerste", last: "Laatste", next: "Volgende", previous: "Vorige" }
    },
    createdRow(row, rowData) {
      row.classList.add('row-link');
      row.dataset.href = buildEditUrl(rowData.id);
      row.tabIndex = 0;
      row.setAttribute('role', 'link');
      row.setAttribute('aria-label', 'Bewerk record #' + rowData.id);
      row.title = 'Bewerk record #' + rowData.id;
    }
  });

  // Row navigation
  $('#invTable tbody')
    .on('click', 'tr.row-link', function (e) {
      if (!this.dataset.href) return;
      e.ctrlKey || e.metaKey
        ? window.open(this.dataset.href, '_blank')
        : window.location.href = this.dataset.href;
    })
    .on('auxclick', 'tr.row-link', function (e) {
      if (e.button === 1 && this.dataset.href) {
        window.open(this.dataset.href, '_blank');
      }
    })
    .on('keydown', 'tr.row-link', function (e) {
      if (e.key === 'Enter' && this.dataset.href) {
        window.location.href = this.dataset.href;
      }
    });

  // Filters
  $('#f-barcode').on('keyup change', function () {
    table.column(COL.barcode).search(this.value).draw();
  });

  $('#f-location').on('change', function () {
    const v = this.value;
    table.column(COL.location)
      .search(v ? '^' + $.fn.dataTable.util.escapeRegex(v) + '$' : '', true, false)
      .draw();
  });

  $('#f-processed').on('change', function () {
    const v = this.value;
    const needle = v === '' ? '' : (v === '1' ? '^Ja$' : '^Nee$');
    table.column(COL.processed).search(needle, true, false).draw();
  });

  $('#f-date-from, #f-date-to').on('change', () => table.draw());

  $('#f-processed').trigger('change');

  //Export data to CSV file using blob download
  function exportCsv() {
    var csv = "id;relatie;rolnummer;kwaliteit;lengte;breedte;locatie;verwerkt;datum;gewijzigd\n";
    var rows = table.rows({ search: 'applied', order: 'applied' }).data();

    for(var i = 0; i < rows.length; i++){
      csv = csv.concat(rows[i].id + ';');
      csv = csv.concat(rows[i].relation + ';');
      csv = csv.concat(rows[i].barcode + ';');
      csv = csv.concat(rows[i].quality + ';');
      csv = csv.concat(rows[i].lengte + ';');
      csv = csv.concat(rows[i].breedte + ';');
      csv = csv.concat(rows[i].location + ';');
      csv = csv.concat(rows[i].processed + ';');
      csv = csv.concat(rows[i].date + ';');
      csv = csv.concat(rows[i].modified + ';');
      csv = csv.concat(`\n`);
    }

    //Create CSV blob object
    const blob = new Blob(
      [csv],
      { type: "text/csv;charset=utf-8;" }
    );

    //Convert BLOB to URL download
    const url = URL.createObjectURL(blob);
    
    //Start the download
    $("<a>")
    .attr("href", url)
    .attr("download", "voorraad.csv")
    .appendTo("body")[0]
    .click();
  }

  //CSV button
  $('#csv').on('click', () => exportCsv());
</script>
