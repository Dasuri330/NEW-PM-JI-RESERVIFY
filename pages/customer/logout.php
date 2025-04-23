<?php
session_start();

// destroy all session data
session_unset();
session_destroy();

// redirect to the login page
header("Location: /NEW-PM-JI-RESERVIFY/pages/customer/");
exit();
?>