<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Table with Action Buttons</title>
    <style>
        /* Basic Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border: 1px solid #ddd;
            white-space: nowrap;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Action Button Container */
        .action-btn-container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .action-btn {
            width: 24px;
            height: 24px;
            border: none;
            border-radius: 50%;
            background-color: transparent;
            color: black;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            background-color: #f0f0f0;
        }

        /* Dropdown Menu Styles */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 8px 0;
            z-index: 1000;
            min-width: 120px;
        }

        .dropdown-menu a {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            color: black;
        }

        .dropdown-menu a:hover {
            background-color: #f9f9f9;
        }

        /* Show Dropdown Menu */
        .dropdown-menu.show {
            display: block;
        }
    </style>
</head>
<body>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Account</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Example database query
                $pdo = new PDO('sqlite:databases/journal.db'); // Adjust the path to your database
                $stmt = $pdo->query("SELECT * FROM transactions");

                $counter = 1; // Initialize row counter
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $counter . '</td>';
                    echo '<td>' . $row['code'] . '</td>';
                    echo '<td>' . $row['account'] . '</td>';
                    echo '<td>SAR ' . $row['amount'] . '</td>';
                    echo '<td>' . $row['type'] . '</td>';
                    echo '<td class="action-btn-container">';
                    echo '<button class="action-btn" onclick="toggleMenu(this)">⋮</button>';
                    echo '<div class="dropdown-menu">
                            <a href="javascript:void(0)" onclick="editRow(' . $counter . ')">Edit</a>
                            <a href="javascript:void(0)" onclick="deleteRow(' . $counter . ')">Delete</a>
                          </div>';
                    echo '</td>';
                    echo '</tr>';
                    $counter++;
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Function to toggle dropdown menu visibility
        function toggleMenu(button) {
            const dropdown = button.nextElementSibling;
            dropdown.classList.toggle("show");

            // Close other dropdowns if open
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.remove("show");
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.matches('.action-btn')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove("show");
                });
            }
        });

        // Placeholder for edit and delete actions
        function editRow(rowId) {
            alert(`Editing row ${rowId}`);
        }

        function deleteRow(rowId) {
            alert(`Deleting row ${rowId}`);
        }
    </script>

</body>
</html>
