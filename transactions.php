<?php
$debit = 5000;
$credit = 3000;
$balance = $credit - $debit;
?>

<html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transactions</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
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
        <a href="index.php" class="active">Transactions</a>
        <a href="costAllocation.php">Cost Center Allocation</a>
        <a href="invoice.php">Invoice</a>
        <a href="journal.php">Journal</a>
      </nav>
    </header>

    <main class="dashboard-content">

      <div class="transaction-head">
        <h1>Transaction Dashboard</h1>
        <button class="new-record" id="openModalBtn">Add New Record</button>

        <div id="myModal" class="modal">
          <div class="modal-content">
            <span class="close" id="closeModalBtn">&times;</span>
            <h2>Add Transaction Record</h2>
            <form id="dataForm" method="POST">

              <br><label for="code">Code:</label>
              <input type="text" name="code" placeholder="Enter your code" required><br><br>

              <label for="account">Account:</label>
              <input type="text" name="account" placeholder="Enter account name" required><br>

              <label for="amount">Amount:</label>
              <input type="number" name="amount" placeholder="Enter amount" required><br>

              <label>Transaction Type:</label><br>

              <div class="radio-group">
                <input type="radio" name="type" value="Debit" required>
                <label for="python">Debit</label><br>

                <input type="radio" name="type" value="Credit" required>
                <label for="javascript">Credit</label><br>

              </div>



              <button type="submit">Submit</button>
              <button type="button" id="cancelBtn">Cancel</button>

            </form>
          </div>
        </div>

        <script>
          var modal = document.getElementById("myModal");
          var openModalBtn = document.getElementById("openModalBtn");
          var closeModalBtn = document.getElementById("closeModalBtn");
          var cancelBtn = document.getElementById("cancelBtn");
          openModalBtn.onclick = function() {
            modal.style.display = "block";
          }
          closeModalBtn.onclick = function() {
            modal.style.display = "none";
          }
          cancelBtn.onclick = function() {
            modal.style.display = "none";
          }
          window.onclick = function(event) {
            if (event.target == modal) {
              modal.style.display = "none";
            }
          }
        </script>

      </div>
      <h3>Account Summary</h3>
      <div class="summary">
        <div class="card">
          <span class="material-symbols-outlined">payments</span>
          <div class="text-content">
            <p>Debits</p>
            <h2><?php echo "SAR " . $debit ?></h2>
          </div>
        </div>
        <div class="card">
          <span class="material-symbols-outlined">paid</span>
          <div class="text-content">
            <p>Credits</p>
            <h2><?php echo "SAR " . $credit ?></h2>
          </div>
        </div>
        <div class="card">
          <span class="material-symbols-outlined">account_balance</span>
          <div class="text-content">
            <p>Balance</p>
            <h2><?php echo "SAR " . $balance ?></h2>
          </div>
        </div>
      </div>

      <div class="table-container">

        <table>
          <thead>
            <tr>
              <th>Number</th>
              <th>Code</th>
              <th>Account</th>
              <th>Amount</th>
              <th>Type</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Invoiced</td>
              <td>$1,800</td>
              <td>$2.00</td>
              <td>$1,798</td>
              <td><span class="status debit">Debit</span></td>
            </tr>
            <tr>
              <td>Invoiced</td>
              <td>$300</td>
              <td>$0.40</td>
              <td>$299.60</td>
              <td><span class="status credit">Credit</span></td>
            </tr>
          </tbody>
        </table>

      </div>
    </main>
  </div>
</body>

</html>