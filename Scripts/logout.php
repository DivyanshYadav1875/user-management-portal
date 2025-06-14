<?php
// Delete the cookie by setting its expiration time in the past
setcookie("remembered_user", "", time() - 3600, "/");

// Optional: clear session or do any cleanup here

// Redirect to login page
header("Location: index.php");
exit;
?>