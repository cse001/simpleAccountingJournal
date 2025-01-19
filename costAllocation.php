<?php
$pdo = new PDO('sqlite:databases/journal.db');
?>

<html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cost Center Allocation Dashboard</title>
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
        <a href="costAllocation.php" class="active">Cost Center Allocation</a>
        <a href="invoice.php">Invoice</a>
        <a href="journal.php">Journal</a>
      </nav>
    </header>

    <main class="dashboard-content">

      <div class="transaction-head">
        <h1>Cost Center Dashboard</h1>
        <button class="new-record" id="openModalBtn">Add New Record</button>

        <div id="addModal" class="modal-overlay">
          <div class="modal-content">
            <h2>Add Cost Allocation Record</h2>

            <?php
            if (isset($_POST['submit'])) {

              $code = $_POST['code'];
              $costCenter = $_POST['costCenter'];
              $amount = $_POST['amount'];
              $type = $_POST['type'];
              $remarks = $_POST['remarks'];

              $stmt = $pdo->prepare("SELECT code FROM costAllocation");
              $stmt->execute();
              $codes = $stmt->fetchAll(PDO::FETCH_COLUMN);

              if (in_array($code, $codes)) {
                echo "<script>alert('Given transaction code already exists in the database. Codes shoud be unique. Please try again.')</script>";
              } else {
                $stmt = $pdo->prepare("INSERT INTO costAllocation (code, costCenter, amount, remarks, type) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$code, $costCenter, $amount, $remarks, $type]);
                header('Location: costAllocation.php');
              }
            }
            ?>

            <form method="POST" action="costAllocation.php">

              <br><label>Code:</label>
              <input type="text" name="code" pattern="^[a-zA-Z0-9]+$" placeholder="Enter transaction code" required><br><br>

              <label>Cost Center:</label>
              <input type="text" name="costCenter" pattern="^[a-zA-Z0-9 ]+$" placeholder="Enter Cost Center" required><br>

              <label>Amount:</label>
              <input type="number" step="0.01" name="amount" placeholder="Enter amount" required><br>

              <label>Type:</label><br>
              <input type="text" name="type" pattern="^[a-zA-Z0-9 ]+$" placeholder="Enter type (Optional)"><br>

              <label>Remarks:</label>
              <input type="text" name="remarks" pattern="^[a-zA-Z0-9 ]+$" placeholder="Enter remarks (Optional)"><br><br>

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

        $stmt = $pdo->query("SELECT COUNT(*) FROM costAllocation");
        $total_records = $stmt->fetchColumn();
        $total_pages = ceil($total_records / $records_per_page);

        $stmt = $pdo->prepare("SELECT * FROM costAllocation LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        ?>

        <table>
          <thead>
            <tr>
              <th>Number</th>
              <th>Code</th>
              <th>Cost Center</th>
              <th>Amount</th>
              <th>Remarks</th>
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
                <td><?= $row['costCenter'] ?></td>
                <td><?= number_format($row['amount'], 2) ?></td>
                <td><?= $row['remarks'] ?></td>
                <td class="edit-row"><?= $row['type'] ?></span></td>
                <td><button class="action-btn" onclick="toggleMenu(this)">⋮</button>
                  <div class="dropdown-menu">
                    <a onclick="editRow('<?= $row['code'] . '\',\'' . $row['costCenter'] . '\',\'' . $row['amount'] . '\',\'' . $row['remarks'] . '\',\'' . $row['type'] ?>')">Edit</a>
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
              $costCenter = $_POST['costCenter'];
              $amount = $_POST['amount'];
              $remarks = $_POST['remarks'];
              $type = $_POST['type'];

              $stmt = $pdo->prepare("UPDATE costAllocation SET costCenter = ?, amount = ?, remarks = ?, type = ? WHERE code = ?");
              $stmt->execute([$costCenter, $amount, $remarks, $type, $code]);

              header('Location: costAllocation.php');
            }
            ?>

            <label>Cost Center:</label>
            <input type="text" id="editCostCenter"><br>

            <label>Amount:</label>
            <input type="number" id="editAmount"><br>

            <label>Type:</label><br>
            <input type="text" id="editType"><br>

            <label>Remarks:</label>
            <input type="text" id="editRemarks"><br><br>

            <button id="editSubmitBtn" class="button-green">Submit</button>
            <button type="button" id="editCancelBtn" class="button-red">Cancel</button>

          </div>
        </div>

        <?php
        if (isset($_POST['delCode'])) {
          $code = $_POST['delCode'];
          $stmt = $pdo->prepare("DELETE FROM costAllocation WHERE code = :code");
          $stmt->bindParam(':code', $code, PDO::PARAM_STR);
          $stmt->execute();
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

          function editRow(codeNew, costCenter, amount, remarks, type) {
            editModal.style.display = "block";
            document.querySelector('#editModal h2').innerText = `Edit Cost Center Allocation Records of Code ${codeNew}`;
            document.querySelector('#editCostCenter').placeholder = costCenter;
            document.querySelector('#editAmount').placeholder = amount;
            document.querySelector('#editRemarks').value = remarks;
            document.querySelector('#editType').value = type;
            editCode = codeNew;
          }

          var editCancelBtn = document.getElementById("editCancelBtn");
          var editSubmitBtn = document.getElementById("editSubmitBtn");

          editCancelBtn.onclick = function() {
            editModal.style.display = "none";
          }

          editSubmitBtn.onclick = function() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "costAllocation.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            var editCostCenterInput = document.querySelector('#editCostCenter');
            var costCenter = editCostCenterInput.value.trim() || editCostCenterInput.placeholder;

            if (!/^[a-zA-Z0-9 ]+$/.test(costCenter)) {
              alert("Cost Center must only contain letters, numbers, and spaces.");
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

            var editTypeInput = document.querySelector('#editType');
            var type = editTypeInput.value.trim();

            if (type && !/^[a-zA-Z0-9 ]+$/.test(type)) {
              alert("Type must only contain letters, numbers, and spaces, or be empty.");
              event.preventDefault();
              return;
            }

            var editRemarksInput = document.querySelector('#editRemarks');
            var remarks = editRemarksInput.value.trim();

            var data = "editCode=" + encodeURIComponent(editCode) +
              "&costCenter=" + encodeURIComponent(costCenter) +
              "&amount=" + encodeURIComponent(amount) +
              "&remarks=" + encodeURIComponent(remarks) +
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
            xhr.open("POST", "costAllocation.php", true);
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