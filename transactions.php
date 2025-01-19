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

        <div id="addModal" class="modal-overlay">
          <div class="modal-content">
            <h2>Add Transaction Record</h2>

            <?php
            if (isset($_POST['submit'])) {

              $code = $_POST['code'];
              $account = $_POST['account'];
              $amount = $_POST['amount'];
              $type = $_POST['type'];

              $stmt = $pdo->prepare("SELECT code FROM transactions");
              $stmt->execute();
              $codes = $stmt->fetchAll(PDO::FETCH_COLUMN);
              
              if (in_array($code, $codes)) {
                echo "<script>alert('Given code already exists in the database. Codes shoud be unique. Please try again.')</script>";
              } else {
                $stmt = $pdo->prepare("INSERT INTO transactions (code, account, amount, type) VALUES (?, ?, ?, ?)");
                $stmt->execute([$code, $account, $amount, $type]);
                header('Location: transactions.php');
              }

            }
            ?>

            <form method="POST" action="transactions.php">

              <br><label>Code:</label>
              <input type="text" name="code" pattern="^[a-zA-Z0-9]+$" placeholder="Enter your code" required><br><br>

              <label>Account:</label>
              <input type="text" name="account" pattern="^[a-zA-Z0-9 ]+$" placeholder="Enter account name" required><br>

              <label>Amount:</label>
              <input type="number" step="0.01" name="amount" placeholder="Enter amount" required><br>

              <label>Transaction Type:</label><br>

              <div class="radio-group">
                <input type="radio" name="type" value="Debit" required>
                <label>Debit</label><br>

                <input type="radio" name="type" value="Credit" required>
                <label>Credit</label><br>

              </div>

              <button type="submit" name="submit" class="button-green">Submit</button>
              <button id="cancelBtn" class="button-red">Cancel</button>

            </form>
          </div>
        </div>

        <script>
          var modal = document.getElementById("addModal");
          var openModalBtn = document.getElementById("openModalBtn");
          var cancelBtn = document.getElementById("cancelBtn");

          openModalBtn.onclick = function() {
            modal.style.display = "block";
          }
          cancelBtn.onclick = function() {
            modal.style.display = "none";
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
                    <a onclick="editRow('<?= $row['code'] . '\',\'' . $row['account'] . '\',\'' . $row['amount'] . '\',\'' . $row['type'] ?>')">Edit</a>
                    <a onclick="deleteRow('<?= $row['code'] ?>')">Delete</a>
                  </div>
                </td>
              </tr>
            <?php $counter++;
            } ?>
          </tbody>
        </table>

        <div id="editModal" class="modal-overlay">
          <div class="modal-content">


            <h2></h2>

            <?php
            if (isset($_POST['editCode'])) {

              $code = $_POST['editCode'];
              $account = $_POST['account'];
              $amount = $_POST['amount'];
              $type = $_POST['type'];

              $stmt = $pdo->prepare("UPDATE transactions SET account = ?, amount = ?, type = ? WHERE code = ?");
              $stmt->execute([$account, $amount, $type, $code]);

              header('Location: transactions.php');
              exit;
            }
            ?>

            <label>Account:</label>
            <input type="text" id="editAccount"><br>

            <label>Amount:</label>
            <input type="number" id="editAmount"><br>

            <label>Transaction Type:</label><br>

            <div class="radio-group">
              <input type="radio" name="editType" value="Debit" required>
              <label>Debit</label><br>

              <input type="radio" name="editType" value="Credit" required>
              <label>Credit</label><br>

            </div>

            <button id="editSubmitBtn" class="button-green">Submit</button>
            <button type="button" id="editCancelBtn" class="button-red">Cancel</button>

          </div>
        </div>

        <?php
        if (isset($_POST['delCode'])) {
          $code = $_POST['delCode'];
          $stmt = $pdo->prepare("DELETE FROM transactions WHERE code = :code");
          $stmt->bindParam(':code', $code, PDO::PARAM_STR);
          $stmt->execute();
          header('Location: transactions.php');
          exit;
        }
        ?>

        <div id="delModal" class="modal-overlay">
          <div class="modal-content">
            <h3></h3>
            <button id="delSubmitBtn" class="button-red">Yes, Delete</button>
            <button id="delCancelBtn" class="button-green">Cancel</button>
          </div>
        </div>

        <script>
          function toggleMenu(button) {
            const dropdown = button.nextElementSibling;
            dropdown.classList.toggle('open');;
          }

          var editModal = document.getElementById("editModal");
          var editCode = "";

          function editRow(codeNew, account, amount, type) {
            editModal.style.display = "block";
            document.querySelector('#editModal h2').innerText = `Edit Transaction Records of Code ${codeNew}`;
            document.querySelector('#editAccount').placeholder = account;
            document.querySelector('#editAmount').placeholder = amount;
            document.querySelector(`input[name="editType"][value="${type}"]`).checked = true;
            editCode = codeNew;
          }

          var editCancelBtn = document.getElementById("editCancelBtn");
          var editSubmitBtn = document.getElementById("editSubmitBtn");

          editCancelBtn.onclick = function() {
            editModal.style.display = "none";
          }

          editSubmitBtn.onclick = function() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "transactions.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            var editAccountInput = document.querySelector('#editAccount');
            var account = editAccountInput.value.trim() || editAccountInput.placeholder;

            if (!/^[a-zA-Z0-9 ]+$/.test(account)) {
              alert("Account must only contain letters, numbers, and spaces.");
              event.preventDefault();
              return;
            }

            var editAmountInput = document.querySelector('#editAmount');
            var amount = editAmountInput.value.trim() || editAmountInput.placeholder;

            var amountFloat = parseFloat(amount);
            if (isNaN(amountFloat) || !/^\d+(\.\d{1,2})?$/.test(amount)) {
              alert("Amount must be a valid number with up to two decimal places.");
              event.preventDefault();
              return;
            }

            var type = document.querySelector('input[name="editType"]:checked').value;

            var data = "editCode=" + encodeURIComponent(editCode) +
              "&account=" + encodeURIComponent(account) +
              "&amount=" + encodeURIComponent(amount) +
              "&type=" + encodeURIComponent(type);

            xhr.send(data);
          }

          var delModal = document.getElementById("delModal");
          var code = ""

          function deleteRow(codeNew) {
            document.querySelector('#delModal h3').innerText = `Are you sure you want to delete record with code ${codeNew}?`;
            delModal.style.display = "block";
            code = codeNew;
          }

          var delCancelBtn = document.getElementById("delCancelBtn");
          var delSubmitBtn = document.getElementById("delSubmitBtn");

          delCancelBtn.onclick = function() {
            delModal.style.display = "none";
          }

          delSubmitBtn.onclick = function() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "transactions.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("delCode=" + encodeURIComponent(code));
          }

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