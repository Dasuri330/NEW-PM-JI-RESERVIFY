<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/styles/reset-password.css">
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/components/top-header.css">
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/components/footer.css">
</head>

<body>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/components/top-header.php'; ?>

    <div class="content-section">
        <div class="container-content mt-5">
            <h2>Reset Your Password</h2>
            <form method="POST" action="/NEW-PM-JI-RESERVIFY/process-reset-password.php" id="resetPasswordForm"
                novalidate>
                <!-- New Password Field -->
                <label for="newpassword">New Password</label>
                <div class="input-box password-box">
                    <input type="password" name="password" id="newpassword" placeholder="Enter new password" required
                        minlength="8" pattern=".{8,}" title="Password must be at least 8 characters long">
                    <i class="toggle-password fas fa-eye"></i>
                </div>

                <!-- Confirm Password Field -->
                <label for="confirmPassword">Confirm Password</label>
                <div class="input-box password-box">
                    <input type="password" name="confirmPassword" id="confirmPassword"
                        placeholder="Confirm your password" required minlength="8" pattern=".{8,}"
                        title="Password must be at least 8 characters long">
                    <i class="toggle-password fas fa-eye"></i>
                </div>

                <!-- Error Message -->
                <small id="passwordError" class="form-text text-danger"></small>

                <!-- Hidden Token Field -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">

                <button id="submitBtn" type="submit" class="btn btn-primary btn-block">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/components/footer.html'; ?>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Toggle Password Visibility -->
    <script>
        $('.toggle-password').on('click', function () {
            const $box = $(this).closest('.password-box');
            const $input = $box.find('input');
            const isPassword = $input.attr('type') === 'password';
            $input.attr('type', isPassword ? 'text' : 'password');
            $(this).toggleClass('fa-eye fa-eye-slash');
        });
    </script>

    <!-- Validate Password Match -->
    <script>
        $(function () {
            var $form = $('#resetPasswordForm');
            var $newPass = $('#newpassword');
            var $confirmPass = $('#confirmPassword');
            var $error = $('#passwordError');

            function validateMatch() {
                if ($newPass.val() && $confirmPass.val() && $newPass.val() !== $confirmPass.val()) {
                    $error.text('Passwords do not match.');
                    return false;
                } else {
                    $error.text('');
                    return true;
                }
            }

            // real-time feedback
            $newPass.add($confirmPass).on('keyup change', validateMatch);

            // on form submit
            $form.on('submit', function (e) {
                // check HTML5 validity
                if (!this.checkValidity()) {
                    // let browser show native errors
                    return;
                }
                // check password match
                if (!validateMatch()) {
                    e.preventDefault();
                    $confirmPass.focus();
                }
            });
        });
    </script>
</body>

</html>