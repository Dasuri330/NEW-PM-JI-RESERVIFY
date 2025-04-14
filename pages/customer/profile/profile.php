<?php
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_email'])) {
    header("Location: /NEW-PM-JI-RESERVIFY/index.php");
    exit;
}

// Database connection
$host = 'localhost';
$db = 'db_pmji';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Fetch user data using email
$userEmail = $_SESSION['user_email'];
$sql = "SELECT first_name, middle_name, last_name, contact_no, email FROM tbl_users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $userEmail]);
$user = $stmt->fetch();

if (!$user) {
    die('User not found.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <!-- External CSS & Fonts -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/customer/components/top_header.css">
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/pages/customer/profile/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <!-- jQuery and Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/pages/customer/components/top_header.php'; ?>

    <main>
        <section class="profile-container">
            <h2>
                <i class="fas fa-user-circle"></i> My Profile
            </h2>
            <div class="profile-details">
                <!-- First Name -->
                <div class="profile-field" data-field="first_name">
                    <p>
                        <strong>First Name:</strong>
                        <span class="field-value"><?= htmlspecialchars($user['first_name']) ?></span>
                        <i class="fas fa-pen edit-btn"></i>
                    </p>
                </div>
                <!-- Middle Name -->
                <div class="profile-field" data-field="middle_name">
                    <p>
                        <strong>Middle Name:</strong>
                        <span class="field-value"><?= htmlspecialchars($user['middle_name']) ?></span>
                        <i class="fas fa-pen edit-btn"></i>
                    </p>
                </div>
                <!-- Last Name -->
                <div class="profile-field" data-field="last_name">
                    <p>
                        <strong>Last Name:</strong>
                        <span class="field-value"><?= htmlspecialchars($user['last_name']) ?></span>
                        <i class="fas fa-pen edit-btn"></i>
                    </p>
                </div>
                <!-- Contact No -->
                <div class="profile-field" data-field="contact_no">
                    <p>
                        <strong>Contact No:</strong>
                        <span class="field-value"><?= htmlspecialchars($user['contact_no']) ?></span>
                        <i class="fas fa-pen edit-btn"></i>
                    </p>
                </div>
                <!-- Email -->
                <div class="profile-field" data-field="email">
                    <p>
                        <strong>Email:</strong>
                        <span class="field-value"><?= htmlspecialchars($user['email']) ?></span>
                        <i class="fas fa-pen edit-btn"></i>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- JavaScript for Inline Editing -->
    <script>
        $(document).ready(function () {
            $('.edit-btn').on('click', function () {
                var $fieldContainer = $(this).closest('.profile-field');
                var $span = $fieldContainer.find('.field-value');
                var currentValue = $span.text().trim();

                $(this).hide();

                $span.data('old-value', currentValue);

                $span.attr('contenteditable', 'true').addClass('editing').focus();

                var iconsHtml = '<span class="edit-actions">' +
                    '<i class="fas fa-check btn-save" title="Save"></i>' +
                    '<i class="fas fa-times btn-cancel" title="Cancel"></i>' +
                    '</span>';
                if ($fieldContainer.find('.edit-actions').length === 0) {
                    $fieldContainer.find('p').append(iconsHtml);
                }
            });

            // When clicking the save icon, update the value via AJAX.
            $(document).on('click', '.btn-save', function () {
                var $fieldContainer = $(this).closest('.profile-field');
                var fieldName = $fieldContainer.data('field');
                var $span = $fieldContainer.find('.field-value');
                var newValue = $span.text().trim();

                $.ajax({
                    url: '/NEW-PM-JI-RESERVIFY/pages/customer/profile/update_profile.php',
                    method: 'POST',
                    data: { field: fieldName, value: newValue },
                    success: function (response) {
                        try {
                            var res = JSON.parse(response);
                            if (res.status === 'success') {
                                // Optionally show a success indicator here.
                            } else {
                                alert('Update failed: ' + res.message);
                                $span.text($span.data('old-value'));
                            }
                        } catch (e) {
                            alert('Unexpected error');
                            $span.text($span.data('old-value'));
                        }
                    },
                    error: function () {
                        alert('Error updating profile.');
                        $span.text($span.data('old-value'));
                    },
                    complete: function () {
                        $span.removeAttr('contenteditable').removeClass('editing');
                        $fieldContainer.find('.edit-actions').remove();
                        $fieldContainer.find('.edit-btn').show();
                    }
                });
            });

            // When clicking the cancel icon, revert changes.
            $(document).on('click', '.btn-cancel', function () {
                var $fieldContainer = $(this).closest('.profile-field');
                var $span = $fieldContainer.find('.field-value');
                $span.text($span.data('old-value'));
                $span.removeAttr('contenteditable').removeClass('editing');
                $fieldContainer.find('.edit-actions').remove();
                $fieldContainer.find('.edit-btn').show();
            });
        });
    </script>
</body>

</html>