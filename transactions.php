<?php
$pdo = new PDO('sqlite:databases/journal.db');
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

        <div id="myModal" class="modal-overlay">
          <div class="modal-content">
            <span class="close" id="closeModalBtn">&times;</span>
            <h2>Add Transaction Record</h2>

            <?php
            if (isset($_POST['submit'])) {
              $pdo = new PDO('sqlite:databases/journal.db');

              $code = $_POST['code'];
              $account = $_POST['account'];
              $amount = $_POST['amount'];
              $type = $_POST['type'];

              $stmt = $pdo->prepare("INSERT INTO transactions (code, account, amount, type) VALUES (?, ?, ?, ?)");
              $stmt->execute([$code, $account, $amount, $type]);

              header('Location: transactions.php');
              exit;
            }
            ?>

            <form method="POST" action="transactions.php">

              <br><label>Code:</label>
              <input type="text" name="code" placeholder="Enter your code" required><br><br>

              <label>Account:</label>
              <input type="text" name="account" placeholder="Enter account name" required><br>

              <label>Amount:</label>
              <input type="floatval" name="amount" placeholder="Enter amount" required><br>

              <label>Transaction Type:</label><br>

              <div class="radio-group">
                <input type="radio" name="type" value="Debit" required>
                <label>Debit</label><br>

                <input type="radio" name="type" value="Credit" required>
                <label>Credit</label><br>

              </div>

              <button type="submit" name="submit">Submit</button>
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
      <?php
      $stmt_credit = $pdo->query("SELECT SUM(amount) AS credit_sum FROM transactions WHERE type = 'Credit'");
      $credit_sum = $stmt_credit->fetchColumn();
      $stmt_debit = $pdo->query("SELECT SUM(amount) AS debit_sum FROM transactions WHERE type = 'Debit'");
      $debit_sum = $stmt_debit->fetchColumn();
      $credit_sum = $credit_sum ?: 0;
      $debit_sum = $debit_sum ?: 0;
      ?>
      <h3>Account Summary</h3>
      <div class="summary">
        <div class="card">
          <span class="material-symbols-outlined">payments</span>
          <div class="text-content">
            <p>Debits</p>
            <h2>SAR <?php echo number_format($debit_sum, 2) ?></h2>
          </div>
        </div>
        <div class="card">
          <span class="material-symbols-outlined">paid</span>
          <div class="text-content">
            <p>Credits</p>
            <h2>SAR <?php echo number_format($credit_sum, 2) ?></h2>
          </div>
        </div>
        <div class="card">
          <span class="material-symbols-outlined">account_balance</span>
          <div class="text-content">
            <p>Balance</p>
            <h2>SAR <?php echo number_format($credit_sum - $debit_sum, 2) ?></h2>
          </div>
        </div>
      </div>

      <div class="table-container">

        <?php

        $records_per_page = 20;

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $offset = ($page - 1) * $records_per_page;

        $stmt = $pdo->query("SELECT COUNT(*) FROM transactions");
        $total_records = $stmt->fetchColumn();

        $total_pages = ceil($total_records / $records_per_page);

        $stmt = $pdo->prepare("SELECT * FROM transactions LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        ?>

        <table>
          <thead>
            <tr>
              <th>Number</th>
              <th>Code</th>
              <th>Account</th>
              <th>Amount</th>
              <th>Type</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $counter = $offset + 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
            <tr class="row-container">
              <td><?= $counter ?></td>
              <td><?= $row['code'] ?></td>
              <td><?= $row['account'] ?></td>
              <td><?= number_format($row['amount'], 2) ?></td>
              <td class="edit-row"><span class="status <?= $row['type'] ?>"><?= $row['type'] ?></span></td>
                <td><button class="action-btn" onclick="toggleMenu(this)">⋮</button>
                <div class="dropdown-menu">
                  <a href="javascript:void(0)" onclick="editRow(<?= $counter ?>)">Edit</a>
                  <a href="javascript:void(0)" onclick="deleteRow(<?= $counter ?>)">Delete</a>
                </div>
              </td>
              </tr>
            <?php $counter++; } ?>
          </tbody>
        </table>
        <script>
          // Function to toggle dropdown menu for a specific row
          function toggleMenu(button) {
            const dropdown = button.nextElementSibling;
            dropdown.classList.toggle('open');

            // Close other open dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
              if (menu !== dropdown) {
                menu.classList.remove('open');
              }
            });
          }

          // Placeholder for Edit Row functionality
          function editRow(rowId) {
            alert(`Edit functionality for row ${rowId}`);
          }

          // Placeholder for Delete Row functionality
          function deleteRow(rowId) {
            const confirmation = confirm(`Are you sure you want to delete row ${rowId}?`);
            if (confirmation) {
              alert(`Row ${rowId} deleted!`);
            }
          }

          // Close dropdown when clicking outside
          document.addEventListener("click", function(e) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
              if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
                menu.classList.remove('open');
              }
            });
          });
        </script>

        <div class="pagination">
          <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">Previous</a>
          <?php endif; ?>
          <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next</a>
          <?php endif; ?>
        </div>

      </div>
    </main>
  </div>
</body>

</html>