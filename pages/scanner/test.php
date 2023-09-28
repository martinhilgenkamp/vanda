<!DOCTYPE html>
<html>
<head>
  <!-- Define viewport for handheld scanners !-->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=240, initial-scale=1, maximum-scale=1, user-scalable=0">
  <meta name="viewport" content="width=240, initial-scale=1, maximum-scale=1, user-scalable=0">
  <title>Vanda Carpets - Process Management</title>

  <!-- Iinclude Stylesheet !-->
  <link rel="stylesheet" href="inc/css/style.css">

  <!-- Adding required scripts !-->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="inc/script/inactivity-timer.js"></script>
  <script src="inc/script/table-click-handler.js"></script>
  <script src="inc/script/scanner.js"></script>
  <script>
	// Set the required parameters for the page to run.
	const inactivityTimeout = 10 * 1000; // 10 minutes
  </script>
</head>
<body>
    <!-- Input fields to display selected values -->

    <input type="text" id="klant" placeholder="Klant">
    <input type="text" id="barcode" placeholder="Barcode">
    <input type="text" id="zending" placeholder="Zending">

    <!-- Table with rows of data -->
    <table id="data-table">
        <thead>
            <tr>
                <th>Klant</th>
                <th>Zending</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Customer 1</td>
                <td>Shipment 1</td>
            </tr>
            <tr>
                <td>Customer 2</td>
                <td>Shipment 2</td>
            </tr>
            <!-- Add more rows as needed -->
        </tbody>
    </table>

    
</body>
</html>
