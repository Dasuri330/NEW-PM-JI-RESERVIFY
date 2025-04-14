<?php
session_start();
// Check if admin is logged in; if not, redirect to the login page.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../admin_login.php");
    exit;
}
$admin_username = $_SESSION['admin_username'];

// Database connection configuration.
$host = 'localhost';
$db = 'db_pmji';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Fetch pending bookings
$stmtPending = $pdo->prepare("SELECT * FROM tbl_bookings WHERE status = 'pending'");
$stmtPending->execute();
$pendingBookings = $stmtPending->fetchAll();

// Fetch approved bookings
$stmtApproved = $pdo->prepare("SELECT * FROM tbl_bookings WHERE status = 'approved'");
$stmtApproved->execute();
$approvedBookings = $stmtApproved->fetchAll();

// Fetch booking history (e.g., completed bookings)
$stmtHistory = $pdo->prepare("SELECT * FROM tbl_bookings WHERE status = 'completed'");
$stmtHistory->execute();
$historyBookings = $stmtHistory->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="../index.css">
</head>

<body>
    <!-- Include common header -->
    <?php include '../components/admin_header.php'; ?>

    <!-- Dashboard Container: Sidebar + Main Content -->
    <div class="dashboard-container">
        <!-- Include Sidebar -->
        <?php include '../components/admin_sidebar.php'; ?>

        <!-- Main Content Section -->
        <main class="main-content">
            <div class="container my-4">
                <h2 class="mb-4">Manage Bookings</h2>

                <!-- Bootstrap Tabs Navigation -->
                <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                            type="button" role="tab" aria-controls="pending" aria-selected="true">
                            Pending Bookings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved"
                            type="button" role="tab" aria-controls="approved" aria-selected="false">
                            Approved Bookings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history"
                            type="button" role="tab" aria-controls="history" aria-selected="false">
                            Booking History
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="bookingTabsContent">
                    <!-- Pending Bookings Tab -->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>User ID</th>
                                        <th>Event Type</th>
                                        <th>Reservation Date</th>
                                        <th>Time Slot</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($pendingBookings) > 0): ?>
                                        <?php foreach ($pendingBookings as $booking): ?>
                                            <!-- Main Row -->
                                            <tr>
                                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['event_type']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['reservation_date']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['time_slot']); ?></td>
                                                <td>
                                                    <!-- Action Buttons -->
                                                    <button class="btn btn-success btn-sm mx-1" title="Approve"><i
                                                            class="fas fa-check"></i></button>
                                                    <button class="btn btn-danger btn-sm mx-1" title="Reject"><i
                                                            class="fas fa-times"></i></button>
                                                    <button class="btn btn-primary btn-sm mx-1" title="Edit"><i
                                                            class="fas fa-pen"></i></button>
                                                    <!-- Expand/Collapse Button -->
                                                    <button class="btn btn-secondary btn-sm mx-1" data-bs-toggle="collapse"
                                                        data-bs-target="#details-<?php echo $booking['id']; ?>"
                                                        title="Expand/Collapse">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Expanded Details Row (Hidden by default) -->
                                            <tr class="collapse" id="details-<?php echo $booking['id']; ?>">
                                                <td colspan="6">
                                                    <div class="p-3 bg-light">
                                                        <strong>Duration:</strong>
                                                        <?php echo htmlspecialchars($booking['duration']); ?> hours<br>
                                                        <strong>Address:</strong>
                                                        <?php echo htmlspecialchars($booking['street_address'] . ', ' . $booking['barangay'] . ', ' . $booking['city'] . ', ' . $booking['province']); ?><br>
                                                        <strong>Reference Number:</strong>
                                                        <?php echo htmlspecialchars($booking['reference_number']); ?><br>
                                                        <strong>Payment Method:</strong>
                                                        <?php echo htmlspecialchars($booking['payment_method']); ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No pending bookings found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Approved Bookings Tab -->
                    <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>User ID</th>
                                        <th>Event Type</th>
                                        <th>Reservation Date</th>
                                        <th>Time Slot</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($approvedBookings) > 0): ?>
                                        <?php foreach ($approvedBookings as $booking): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['event_type']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['reservation_date']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['time_slot']); ?></td>
                                                <td>
                                                    <button class="btn btn-success btn-sm mx-1" title="Approve"><i
                                                            class="fas fa-check"></i></button>
                                                    <button class="btn btn-danger btn-sm mx-1" title="Reject"><i
                                                            class="fas fa-times"></i></button>
                                                    <button class="btn btn-primary btn-sm mx-1" title="Edit"><i
                                                            class="fas fa-pen"></i></button>
                                                    <button class="btn btn-secondary btn-sm mx-1" data-bs-toggle="collapse"
                                                        data-bs-target="#details-<?php echo $booking['id']; ?>"
                                                        title="Expand/Collapse">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="collapse" id="details-<?php echo $booking['id']; ?>">
                                                <td colspan="6">
                                                    <div class="p-3 bg-light">
                                                        <strong>Duration:</strong>
                                                        <?php echo htmlspecialchars($booking['duration']); ?> hours<br>
                                                        <strong>Address:</strong>
                                                        <?php echo htmlspecialchars($booking['street_address'] . ', ' . $booking['barangay'] . ', ' . $booking['city'] . ', ' . $booking['province']); ?><br>
                                                        <strong>Reference Number:</strong>
                                                        <?php echo htmlspecialchars($booking['reference_number']); ?><br>
                                                        <strong>Payment Method:</strong>
                                                        <?php echo htmlspecialchars($booking['payment_method']); ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No approved bookings found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Booking History Tab -->
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>User ID</th>
                                        <th>Event Type</th>
                                        <th>Reservation Date</th>
                                        <th>Time Slot</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($historyBookings) > 0): ?>
                                        <?php foreach ($historyBookings as $booking): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['event_type']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['reservation_date']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['time_slot']); ?></td>
                                                <td>
                                                    <button class="btn btn-success btn-sm mx-1" title="Approve"><i
                                                            class="fas fa-check"></i></button>
                                                    <button class="btn btn-danger btn-sm mx-1" title="Reject"><i
                                                            class="fas fa-times"></i></button>
                                                    <button class="btn btn-primary btn-sm mx-1" title="Edit"><i
                                                            class="fas fa-pen"></i></button>
                                                    <button class="btn btn-secondary btn-sm mx-1" data-bs-toggle="collapse"
                                                        data-bs-target="#details-<?php echo $booking['id']; ?>"
                                                        title="Expand/Collapse">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="collapse" id="details-<?php echo $booking['id']; ?>">
                                                <td colspan="6">
                                                    <div class="p-3 bg-light">
                                                        <strong>Duration:</strong>
                                                        <?php echo htmlspecialchars($booking['duration']); ?> hours<br>
                                                        <strong>Address:</strong>
                                                        <?php echo htmlspecialchars($booking['street_address'] . ', ' . $booking['barangay'] . ', ' . $booking['city'] . ', ' . $booking['province']); ?><br>
                                                        <strong>Reference Number:</strong>
                                                        <?php echo htmlspecialchars($booking['reference_number']); ?><br>
                                                        <strong>Payment Method:</strong>
                                                        <?php echo htmlspecialchars($booking['payment_method']); ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No booking history found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- End Tabs Content -->
            </div><!-- End Container -->
        </main>
    </div><!-- End Dashboard Container -->

    <!-- Bootstrap 5 JS (with Popper) for tab/collapse functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>