<?php
$pdo = new PDO('sqlite:databases/journal.db');
?>

<html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice</title>
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
        <a href="index.php">Transactions</a>
        <a href="costAllocation.php">Cost Center Allocation</a>
        <a href="invoice.php" class="active">Invoice</a>
        <a href="journal.php">Journal</a>
      </nav>
    </header>

    <main class="dashboard-content">

      <div class="transaction-head">
        <h1>Invoice Dashboard</h1>
        <button class="new-record" id="openModalBtn">Add New Record</button>

        <div id="addModal" class="modal-overlay">
          <div class="modal-content">
            <h2>Add Invoice Record</h2>

            <?php
            if (isset($_POST['submit'])) {

              $code = $_POST['code'];
              $type = $_POST['type'];
              $date = $_POST['date'];
              $amount = $_POST['amount'];
              $transType = $_POST['transType'];

              $stmt = $pdo->prepare("SELECT invoice FROM invoice");
              $stmt->execute();
              $codes = $stmt->fetchAll(PDO::FETCH_COLUMN);
              
              if (in_array($code, $codes)) {
                echo "<script>alert('Given invoice code already exists in the database. Codes shoud be unique. Please try again.')</script>";
              } else {
                $stmt = $pdo->prepare("INSERT INTO invoice (invoice, type, date, amount, transType) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$code, $type, $date, $amount, $transType]);

                $data = "SAR $amount $transType" . "ed as $type on $date";

                $stmt = $pdo->prepare("INSERT INTO master (entry, data, code) VALUES ('Invoice', ?, ?)");
                $stmt->execute([$data, $code]);

                header('Location: invoice.php');
              }

            }
            ?>

            <form method="POST" action="invoice.php">

              <br><label>Code:</label>
              <input type="text" name="code" pattern="^[a-zA-Z0-9]+$" placeholder="Enter invoice code" required><br><br>

              <label>Type:</label>
              <input type="text" name="type" pattern="^[a-zA-Z0-9 ]+$" placeholder="Enter account name" required><br>

              <label>Date:</label>
              <input type="date" name="date" required><br>

              <label>Amount:</label>
              <input type="number" step="0.01" name="amount" placeholder="Enter amount" required><br>

              <label>Transaction Type:</label><br>

              <div class="radio-group">
                <input type="radio" name="transType" value="Debit" required>
                <label>Debit</label><br>

                <input type="radio" name="transType" value="Credit" required>
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
      

      <div class="table-container">

        <?php

        $records_per_page = 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $records_per_page;

        $stmt = $pdo->query("SELECT COUNT(*) FROM invoice");
        $total_records = $stmt->fetchColumn();
        $total_pages = ceil($total_records / $records_per_page);

        $stmt = $pdo->prepare("SELECT * FROM invoice LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        ?>

        <table>
          <thead>
            <tr>
              <th>Number</th>
              <th>Invoice</th>
              <th>Type</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Dr/Cr</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $counter = $offset + 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
              <tr class="row-container">
                <td><?= $counter ?></td>
                <td><?= $row['invoice'] ?></td>
                <td><?= $row['type'] ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= number_format($row['amount'], 2) ?></td>
                <td class="edit-row"><span class="status <?= $row['transType'] ?>"><?= $row['transType'] ?></span></td>
                <td><button class="action-btn" onclick="toggleMenu(this)">⋮</button>
                  <div class="dropdown-menu">
                    <a onclick="editRow('<?= $row['invoice'] . '\',\'' . $row['type'] . '\',\'' . $row['date'] . '\',\'' . $row['amount'] . '\',\'' . $row['transType'] ?>')">Edit</a>
                    <a onclick="deleteRow('<?= $row['invoice'] ?>')">Delete</a>
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
              $type = $_POST['type'];
              $date = $_POST['date'];
              $amount = $_POST['amount'];
              $transType = $_POST['transType'];

              $stmt = $pdo->prepare("UPDATE invoice SET type = ?, date = ?, amount = ?, transType = ? WHERE invoice = ?");
              $stmt->execute([$type, $date, $amount, $transType, $code]);

              $data = "Updated Record: SAR $amount $transType" . "ed as $type on $date";

              $stmt = $pdo->prepare("INSERT INTO master (entry, data, code) VALUES ('Invoice', ?, ?)");
              $stmt->execute([$data, $code]);

              header('Location: invoice.php');
            }
            ?>

            <label>Type:</label>
            <input type="text" id="editType"><br>

            <label>Date:</label>
            <input type="date" id="editDate"><br>

            <label>Amount:</label>
            <input type="number" id="editAmount"><br>

            <label>Transaction Type:</label><br>

            <div class="radio-group">
              <input type="radio" name="editTransType" value="Debit" required>
              <label>Debit</label><br>

              <input type="radio" name="editTransType" value="Credit" required>
              <label>Credit</label><br>

            </div>

            <button id="editSubmitBtn" class="button-green">Submit</button>
            <button type="button" id="editCancelBtn" class="button-red">Cancel</button>

          </div>
        </div>

        <?php
        if (isset($_POST['delCode'])) {
          $code = $_POST['delCode'];
          $stmt = $pdo->prepare("DELETE FROM invoice WHERE invoice = :code");
          $stmt->bindParam(':code', $code, PDO::PARAM_STR);
          $stmt->execute();

          $date = date("d-m-Y");
          $data = "Record Deleted";

          $stmt = $pdo->prepare("INSERT INTO master (entry, data, code) VALUES ('Invoice', ?, ?)");
          $stmt->execute([$data, $code]);

          header('Location: invoice.php');
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

          function editRow(codeNew, type, date, amount, transType) {
            editModal.style.display = "block";
            document.querySelector('#editModal h2').innerText = `Edit Transaction Records of Code ${codeNew}`;
            document.querySelector('#editType').placeholder = type;
            document.querySelector('#editAmount').placeholder = amount;
            document.querySelector('#editDate').placeholder = date;
            document.querySelector(`input[name="editTransType"][value="${transType}"]`).checked = true;
            editCode = codeNew;
          }

          var editCancelBtn = document.getElementById("editCancelBtn");
          var editSubmitBtn = document.getElementById("editSubmitBtn");

          editCancelBtn.onclick = function() {
            editModal.style.display = "none";
          }

          editSubmitBtn.onclick = function() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "invoice.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            var editTypeInput = document.querySelector('#editType');
            var type = editTypeInput.value.trim() || editTypeInput.placeholder;

            if (!/^[a-zA-Z0-9 ]+$/.test(type)) {
              alert("Invoice Type must only contain letters, numbers, and spaces.");
              event.preventDefault();
              return;
            }

            var editDateInput = document.querySelector('#editDate');
            var date = editDateInput.value.trim() || editDateInput.placeholder;

            var editAmountInput = document.querySelector('#editAmount');
            var amount = editAmountInput.value.trim() || editAmountInput.placeholder;

            var amountFloat = parseFloat(amount);
            if (isNaN(amountFloat) || !/^\d+(\.\d{1,2})?$/.test(amount)) {
              alert("Amount must be a valid number with up to two decimal places.");
              event.preventDefault();
              return;
            }

            var transType = document.querySelector('input[name="editTransType"]:checked').value;

            var data = "editCode=" + encodeURIComponent(editCode) +
              "&type=" + encodeURIComponent(type) +
              "&date=" + encodeURIComponent(date) +
              "&amount=" + encodeURIComponent(amount) +
              "&transType=" + encodeURIComponent(transType);

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
            xhr.open("POST", "invoice.php", true);
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