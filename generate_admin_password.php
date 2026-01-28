<?php
// Generate correct password hash for Admin@123
$password = 'Admin@123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\nSQL Update:\n";
echo "UPDATE users SET password = '$hash' WHERE email = 'admin@rangamadala.com';\n";
?>
