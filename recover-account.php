<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Account</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/styles/recover-account.css">
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/components/top-header.css">
    <link rel="stylesheet" href="/NEW-PM-JI-RESERVIFY/components/footer.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/components/top-header.php'; ?>

    <div class="content-section">
        <div class="container-content">
            <h2>Recover Your Account</h2>
            <p>Enter your email address to receive a password reset link.</p>
            <form id="recoverForm" action="/NEW-PM-JI-RESERVIFY/pages/customer/process_recover.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email"
                        required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                <div id="recoverError" class="text-danger mt-2"></div>
                <div id="recoverSuccess" class="text-success mt-2"></div>
            </form>
        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/NEW-PM-JI-RESERVIFY/components/footer.html'; ?>

    <script>
        $(document).ready(function () {
            $('#recoverForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '/NEW-PM-JI-RESERVIFY/process_recover-account.php',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.trim() === 'success') {
                            $('#recoverSuccess').text('a password reset link has been sent to your email.');
                            $('#recoverError').text('');
                        } else {
                            $('#recoverError').text('email not found or an error occurred.');
                            $('#recoverSuccess').text('');
                        }
                    },
                    error: function () {
                        $('#recoverError').text('An error occurred. Please try again.');
                        $('#recoverSuccess').text('');
                    }
                });
            });
        });
    </script>
</body>

</html>