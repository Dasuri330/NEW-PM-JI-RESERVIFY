<!-- filepath: c:\xampp\htdocs\NEW-PM-JI-RESERVIFY\pages\admin\dashboard\customers\profile.php -->
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/config/database.php'; // adjust path as needed

// Get user_id from query parameter
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}
$user_id = intval($_GET['user_id']);

// Fetch user info
$stmt = $pdo->prepare("SELECT first_name, middle_name, last_name, contact_no, email, created_at, is_verified FROM tbl_users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Profile | PM&JI Reservify Admin</title>
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/styles/about.css">
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/components/top-header.css">
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/components/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <style>
        .profile-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px #0001;
            padding: 32px;
        }

        .profile-title {
            font-size: 2rem;
            margin-bottom: 24px;
        }

        .profile-info dt {
            font-weight: bold;
        }

        .profile-info dd {
            margin-bottom: 16px;
        }

        .verified {
            color: green;
            font-weight: bold;
        }

        .not-verified {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/components/top-header.php'; ?>

    <div class="profile-container">
        <h1 class="profile-title"><i class="fas fa-user"></i> Customer Profile</h1>
        <dl class="profile-info">
            <dt>Full Name:</dt>
            <dd>
                <?php
                echo htmlspecialchars($user['first_name']);
                if ($user['middle_name'])
                    echo ' ' . htmlspecialchars($user['middle_name']);
                echo ' ' . htmlspecialchars($user['last_name']);
                ?>
            </dd>
            <dt>Email:</dt>
            <dd><?php echo htmlspecialchars($user['email']); ?></dd>
            <dt>Contact Number:</dt>
            <dd><?php echo htmlspecialchars($user['contact_no']); ?></dd>
            <dt>Account Created:</dt>
            <dd><?php echo htmlspecialchars($user['created_at']); ?></dd>
            <dt>Status:</dt>
            <dd>
                <?php if ($user['is_verified']): ?>
                    <span class="verified"><i class="fas fa-check-circle"></i> Verified</span>
                <?php else: ?>
                    <span class="not-verified"><i class="fas fa-times-circle"></i> Not Verified</span>
                <?php endif; ?>
            </dd>
        </dl>
        <a href="/NEW-PM-JI-RESERVIFY/pages/admin/dashboard/bookings/index.php" class="btn btn-secondary mt-3"><i
                class="fas fa-arrow-left"></i> Back to Bookings</a>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/components/footer.html'; ?>
</body>

</html>