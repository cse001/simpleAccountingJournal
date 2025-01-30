<?php
$pdo = new PDO('sqlite:databases/journal.db');
?>

<html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Journal</title>
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
        <a href="invoice.php">Invoice</a>
        <a href="journal.php" class="active">Journal</a>
      </nav>
    </header>

    <main class="dashboard-content">

      <div class="transaction-head">
        <h1>Master Journal Entries</h1>
      </div>

      <div class="table-container">

        <?php

        $records_per_page = 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $records_per_page;

        $stmt = $pdo->query("SELECT COUNT(*) FROM master");
        $total_records = $stmt->fetchColumn();
        $total_pages = ceil($total_records / $records_per_page);

        $stmt = $pdo->prepare("SELECT * FROM master ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        ?>

        <table>
          <thead>
            <tr>
              <th>Number</th>
              <th>Table</th>
              <th>Data</th>
              <th>Code</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $counter = $offset + 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
              <tr class="row-container">
                <td><?= $counter ?></td>
                <td><?= $row['entry'] ?></td>
                <td><?= $row['data'] ?></td>
                <td><?= $row['code'] ?></td>
              </tr>
            <?php $counter++;
            } ?>
          </tbody>
        </table>
      </div>
      <div class="pagination">
        <div class="start">
          <?php if ($page > 1): ?>
            <a href="?page=<?php echo 1; ?>">First Page</a>
            <a href="?page=<?php echo $page - 1; ?>">Previous Page</a>
          <?php endif; ?>
        </div>
        <div class="end">
          <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next Page</a>
            <a href="?page=<?php echo $total_pages; ?>">Last Page</a>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
</body>

</html>