<?php
session_start();

include("php/config.php");
if (!isset($_SESSION['valid']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit; 
}

require_once 'db_connect.php';

$SL = 1;

// Pagination variables
$cvPerPage = 10; // Number of CVs per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $cvPerPage; // Offset calculation for SQL LIMIT

// Initialize variables for search
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Query to fetch CVs with pagination and search filter
$sql = "SELECT cv.Id, cv.Username, cv.cv_name, cv.cv_path, u.Username as user_name, u.Age, u.Email 
        FROM cv_uploads cv
        LEFT JOIN users u ON cv.Username = u.Username
        WHERE cv.Username LIKE :searchKeyword 
        ORDER BY cv.Id DESC
        LIMIT :offset, :cvPerPage";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':searchKeyword', "%$searchKeyword%", PDO::PARAM_STR); 
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':cvPerPage', $cvPerPage, PDO::PARAM_INT);
$stmt->execute();
$cvList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate starting ID for display
$startingId = ($page - 1) * $cvPerPage + 1;

// Count total CVs (for pagination calculation)
$totalCvsQuery = $pdo->prepare("SELECT COUNT(*) as total FROM cv_uploads WHERE Username LIKE :searchKeyword");
$totalCvsQuery->bindValue(':searchKeyword', "%$searchKeyword%", PDO::PARAM_STR); 
$totalCvsQuery->execute();
$totalCvs = $totalCvsQuery->fetchColumn();
$totalPages = ceil($totalCvs / $cvPerPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CV List</title>
    
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .center {
            text-align: center;
        }
    </style>
    <script>
        function viewDetails(username, age, email) {
            alert(`Name: ${username}\nAge: ${age}\nEmail: ${email}`);
        }
    </script>
</head>
<body>
    <h2 class="center">CV List</h2>

    <p align="right"><a href="php/logout.php"><button class="btn">Log Out</button></a></p>

    <!-- Search Form -->
    <form method="GET" action="">
        <label for="search">Search by Username:</label>
        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchKeyword); ?>">
        <button type="submit">Search</button>
    </form>

    <span style="float: right;">Total Records: <strong><?php echo $totalCvs; ?></strong></span>

    <!-- CV Table -->
    
    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Username</th>
                <th>CV Name</th>
                <th>Download Link</th>
                <th>Action</th> 
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cvList as $index => $cv): ?>
                <tr>
                    <td><?php echo $SL++; ?></td>
                    <td><?php echo htmlspecialchars($cv['Username']); ?></td>
                    <td><?php echo htmlspecialchars($cv['cv_name']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($cv['cv_path']); ?>" target="_blank">Download</a></td>
                    <td><a href="view_details.php?username=<?php echo urlencode($cv['user_name']); ?>&age=<?php echo urlencode($cv['Age']); ?>&email=<?php echo urlencode($cv['Email']); ?>">View Details</a>
</td>
                </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($cvList)): ?>
                <tr>
                    <td colspan="5" class="center">No CVs found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="center" style="margin-top: 20px;">
        <?php if ($totalPages > 1): ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i . '&search=' . urlencode($searchKeyword); ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        <?php endif; ?>
    </div>

</body>
</html>
