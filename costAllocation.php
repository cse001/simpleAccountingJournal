
<html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cost Center Allocation</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=receipt_long" />
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="dashboard">
    <header class="dashboard-header">
        <div class="logo">
        <span class="material-symbols-outlined">receipt_long</span>
        <span>Journal Entry Manager</span>
        </div>
        <nav>
        <a href="index.php">Transactions</a>
        <a href="costAllocation.php" class="active">Cost Center Allocation</a>
        <a href="invoice.php">Invoice</a>
        <a href="journal.php">Journal</a>
        </nav>
    </header>
    <main class="dashboard-content">
        <div class="transaction-head">
            <h1>Cost Center Allocation Dashboard</h1>
            <button class="new-record">Add New Record</button>
        </div>

      <div class="table-container">

        <table>
          <thead>
            <tr>
              <th>Number</th>
              <th>Code</th>
              <th>Cost Center</th>
              <th>Amount</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>M1010</td>
              <td>General P & C</td>
              <td>14,500</td>
              <td>General P&C</td>
            </tr>
            <tr>
              <td>2</td>
              <td>M1108</td>
              <td>SAUDIXAD</td>
              <td>10,000</td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
