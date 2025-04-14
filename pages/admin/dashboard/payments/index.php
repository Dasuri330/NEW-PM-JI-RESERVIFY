<?php
session_start();
// Check if admin is logged in; if not, redirect to the login page.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
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

// Fetch pending payments using the payment_status column
$stmtPending = $pdo->prepare("SELECT * FROM tbl_bookings WHERE payment_status = 'pending'");
$stmtPending->execute();
$pendingPayments = $stmtPending->fetchAll();

// Fetch approved payments using the payment_status column
$stmtApproved = $pdo->prepare("SELECT * FROM tbl_bookings WHERE payment_status = 'approved'");
$stmtApproved->execute();
$approvedPayments = $stmtApproved->fetchAll();

// Fetch payment history (e.g., completed payments) using the payment_status column
$stmtHistory = $pdo->prepare("SELECT * FROM tbl_bookings WHERE payment_status = 'completed'");
$stmtHistory->execute();
$historyPayments = $stmtHistory->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Payments</title>
    <!-- Bootstrap 5 CSS for tab layout and collapse functionality -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for action icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <!-- Custom admin dashboard styles -->
    <link rel="stylesheet" href="../index.css">
</head>

<body>
    <!-- Include common header -->
    <?php include '../components/admin_header.php'; ?>

    <!-- Dashboard Container: Sidebar + Main Content -->
    <div class="dashboard-container">
        <!-- Include the sidebar -->
        <?php include '../components/admin_sidebar.php'; ?>

        <!-- Main Content Section -->
        <main class="main-content">
            <div class="container my-4">
                <h2 class="mb-4">Manage Payments</h2>

                <!-- Bootstrap Tabs Navigation -->
                <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-payments-tab" data-bs-toggle="tab"
                            data-bs-target="#pending-payments" type="button" role="tab" aria-controls="pending-payments"
                            aria-selected="true">
                            Pending Payments
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="approved-payments-tab" data-bs-toggle="tab"
                            data-bs-target="#approved-payments" type="button" role="tab"
                            aria-controls="approved-payments" aria-selected="false">
                            Approved Payments
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payment-history-tab" data-bs-toggle="tab"
                            data-bs-target="#payment-history" type="button" role="tab" aria-controls="payment-history"
                            aria-selected="false">
                            Payment History
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="paymentTabsContent">
                    <!-- Pending Payments Tab -->
                    <div class="tab-pane fade show active" id="pending-payments" role="tabpanel"
                        aria-labelledby="pending-payments-tab">
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>User ID</th>
                                        <th>Payment Method</th>
                                        <th>Payment Type</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($pendingPayments) > 0): ?>
                                        <?php foreach ($pendingPayments as $payment): ?>
                                            <!-- Summary Row -->
                                            <tr>
                                                <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['payment_type']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['created_at']); ?></td>
                                                <td>
                                                    <button class="btn btn-success btn-sm mx-1" title="Approve"><i
                                                            class="fas fa-check"></i></button>
                                                    <button class="btn btn-danger btn-sm mx-1" title="Reject"><i
                                                            class="fas fa-times"></i></button>
                                                    <button class="btn btn-primary btn-sm mx-1" title="Edit"><i
                                                            class="fas fa-pen"></i></button>
                                                    <button class="btn btn-secondary btn-sm mx-1" data-bs-toggle="collapse"
                                                        data-bs-target="#payment-details-<?php echo $payment['id']; ?>"
                                                        title="Expand/Collapse">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Expandable Details Row -->
                                            <tr class="collapse" id="payment-details-<?php echo $payment['id']; ?>">
                                                <td colspan="6">
                                                    <div class="p-3 bg-light">
                                                        <strong>Reference Number:</strong>
                                                        <?php echo htmlspecialchars($payment['reference_number']); ?><br>
                                                        <strong>Payment Screenshot:</strong>
                                                        <?php if (!empty($payment['payment_screenshot'])): ?>
                                                            <img src="../uploads/<?php echo htmlspecialchars($payment['payment_screenshot']); ?>"
                                                                alt="Payment Screenshot" class="img-thumbnail"
                                                                style="max-width: 200px;">
                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No pending payments found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Approved Payments Tab -->
                    <div class="tab-pane fade" id="approved-payments" role="tabpanel"
                        aria-labelledby="approved-payments-tab">
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>User ID</th>
                                        <th>Payment Method</th>
                                        <th>Payment Type</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($approvedPayments) > 0): ?>
                                        <?php foreach ($approvedPayments as $payment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['payment_type']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['created_at']); ?></td>
                                                <td>
                                                    <button class="btn btn-success btn-sm mx-1" title="Approve"><i
                                                            class="fas fa-check"></i></button>
                                                    <button class="btn btn-danger btn-sm mx-1" title="Reject"><i
                                                            class="fas fa-times"></i></button>
                                                    <button class="btn btn-primary btn-sm mx-1" title="Edit"><i
                                                            class="fas fa-pen"></i></button>
                                                    <button class="btn btn-secondary btn-sm mx-1" data-bs-toggle="collapse"
                                                        data-bs-target="#payment-details-<?php echo $payment['id']; ?>"
                                                        title="Expand/Collapse">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="collapse" id="payment-details-<?php echo $payment['id']; ?>">
                                                <td colspan="6">
                                                    <div class="p-3 bg-light">
                                                        <strong>Reference Number:</strong>
                                                        <?php echo htmlspecialchars($payment['reference_number']); ?><br>
                                                        <strong>Payment Screenshot:</strong>
                                                        <?php if (!empty($payment['payment_screenshot'])): ?>
                                                            <img src="../uploads/<?php echo htmlspecialchars($payment['payment_screenshot']); ?>"
                                                                alt="Payment Screenshot" class="img-thumbnail"
                                                                style="max-width: 200px;">
                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No approved payments found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment History Tab -->
                    <div class="tab-pane fade" id="payment-history" role="tabpanel"
                        aria-labelledby="payment-history-tab">
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>User ID</th>
                                        <th>Payment Method</th>
                                        <th>Payment Type</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($historyPayments) > 0): ?>
                                        <?php foreach ($historyPayments as $payment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['payment_type']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['created_at']); ?></td>
                                                <td>
                                                    <button class="btn btn-success btn-sm mx-1" title="Approve"><i
                                                            class="fas fa-check"></i></button>
                                                    <button class="btn btn-danger btn-sm mx-1" title="Reject"><i
                                                            class="fas fa-times"></i></button>
                                                    <button class="btn btn-primary btn-sm mx-1" title="Edit"><i
                                                            class="fas fa-pen"></i></button>
                                                    <button class="btn btn-secondary btn-sm mx-1" data-bs-toggle="collapse"
                                                        data-bs-target="#payment-details-<?php echo $payment['id']; ?>"
                                                        title="Expand/Collapse">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="collapse" id="payment-details-<?php echo $payment['id']; ?>">
                                                <td colspan="6">
                                                    <div class="p-3 bg-light">
                                                        <strong>Reference Number:</strong>
                                                        <?php echo htmlspecialchars($payment['reference_number']); ?><br>
                                                        <strong>Payment Screenshot:</strong>
                                                        <?php if (!empty($payment['payment_screenshot'])): ?>
                                                            <img src="../uploads/<?php echo htmlspecialchars($payment['payment_screenshot']); ?>"
                                                                alt="Payment Screenshot" class="img-thumbnail"
                                                                style="max-width: 200px;">
                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No payment history found.</td>
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

    <!-- Bootstrap 5 JS Bundle (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>