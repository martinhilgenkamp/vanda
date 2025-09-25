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
  <input type="text" id="f-rolno" placeholder="Rolnummer">
  <select id="f-location"><option value="">All locations</option></select>
  <select id="f-processed">
    <option value="">Alles</option>
    <option value="1">Uitgeboekt</option>
    <option value="0">Voorraad</option>
  </select>
  <input type="date" id="f-date-from" placeholder="Van datum">
  <input type="date" id="f-date-to" placeholder="Tot datum">
</div>

<table id="invTable" class="display data-table" style="width:100%">
  <thead>
    <tr>
      <th class="ui-corner-tl">id</th>
      <th>Rolnummer</th>
      <th>Kwaliteit</th>
      <th>Locatie</th>
      <th>Verwerkt</th>
      <th class="ui-corner-tr">Datum</th>
    </tr>
  </thead>
  <tbody></tbody>
  <tfoot>
    <tr>
       <td colspan="6" class="ui-corner-bl ui-corner-br">&nbsp; </td> 
    </tr>
  </tfoot>
</table>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
  // PHP -> JS data
  const data = <?php echo $im->listInventory(); ?>; // expects an array of objects with keys: id, rolno, quality, location, processed, date

  // Build unique locations for the dropdown
  const locations = [...new Set(data.map(r => r.location).filter(Boolean))].sort();
  const locSel = document.getElementById('f-location');
  locations.forEach(loc => {
    const opt = document.createElement('option');
    opt.value = loc;
    opt.textContent = loc;
    locSel.appendChild(opt);
  });

  // DataTables custom date range filter
  $.fn.dataTable.ext.search.push(function(settings, dataRow) {
    // dataRow indexes follow column order defined below
    const dateStr = dataRow[5]; // date column as string
    const fromVal = document.getElementById('f-date-from').value;
    const toVal   = document.getElementById('f-date-to').value;

    if (!fromVal && !toVal) return true;
    const rowTime = new Date(dateStr.replace(' ', 'T')).getTime(); // tolerant parse (YYYY-MM-DD HH:mm:ss)

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

 const table = $('#invTable').DataTable({
  data,
  deferRender: true,
  pageLength: 25,
  order: [[5, 'desc']],
  columns: [
    { data: 'id' },
    { data: 'rolno' },
    { data: 'quality' },
    { data: 'location' },
    {
      data: 'processed',
      render: function(val) {
        const isYes = String(val) === '1';
        return `<span class="badge ${isYes ? 'yes' : 'no'}">${isYes ? 'Ja' : 'Nee'}</span>`;
      }
    },
    {
      data: 'date',
      render: function(val) {
        const d = new Date(val.replace(' ', 'T'));
        return isNaN(d) ? val : d.toLocaleString();
      }
    }
  ],
  language: {
    search: "Zoeken:",
    lengthMenu: "Toon _MENU_ resultaten per pagina",
    info: "Resultaat _START_ tot _END_ van _TOTAL_",
    infoEmpty: "Geen resultaten beschikbaar",
    infoFiltered: "(gefilterd uit _MAX_ total)",
    zeroRecords: "Geen overeenkomende records gevonden",
    paginate: {
      first:    "Eerste",
      last:     "Laatste",
      next:     "Volgende",
      previous: "Vorige"
    }
  }
});
  // Wire up filters
  $('#f-rolno').on('keyup change', function () {
    table.column(1).search(this.value).draw(); // rolno
  });

  $('#f-location').on('change', function () {
    // exact match using regex anchor
    const val = this.value;
    table.column(3).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
  });

  $('#f-processed').on('change', function () {
    const val = this.value;
    // Match underlying text "Yes"/"No" in the rendered column or underlying 1/0; simpler to search on 1/0 via hidden data
    // Since we render badges, use a custom search on the data source instead:
    table.column(4).search(val === '' ? '' : (val === '1' ? 'Yes' : 'No')).draw();
  });

  $('#f-date-from, #f-date-to').on('change', function () {
    table.draw();
  });


</script>