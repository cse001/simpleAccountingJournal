
<html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoices</title>
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
        <a href="costAllocation.php">Cost Center Allocation</a>
        <a href="invoice.php" class="active">Invoice</a>
        <a href="journal.php">Journal</a>
        </nav>
    </header>
    <main class="dashboard-content">
        <div class="transaction-head">
            <h1>Invoice Dashboard</h1>
            <button class="new-record">Add New Record</button>
        </div>

      <div class="table-container">

        <table>
          <thead>
            <tr>
              <th>Transaction Date</th>
              <th>Type</th>
              <th>Send Amount</th>
              <th>Fee</th>
              <th>Receive Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>07/09/2022, 06:31</td>
              <td>Invoiced</td>
              <td>$1,800</td>
              <td>$2.00</td>
              <td>$1,798</td>
              <td><span class="status pending">Pending</span></td>
            </tr>
            <tr>
              <td>06/09/2022, 22:02</td>
              <td>Invoiced</td>
              <td>$300</td>
              <td>$0.40</td>
              <td>$299.60</td>
              <td><span class="status complete">Complete</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
