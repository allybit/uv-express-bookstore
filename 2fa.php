<?php
// Step 1: Generate a Secret Key
$secretKey = base64_encode(random_bytes(32)); // Generate a random secret key
echo "Secret Key: " . $secretKey . "<br>";

// Step 2: Generate a QR Code
$qrCodeUrl = "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=" . urlencode("otpauth://totp/YourApp:user@example.com?secret=$secretKey&issuer=YourApp");
// Step 3: Verify the OTP
function verifyOTP($secretKey, $userOTP) {
    $timestamp = floor(time() / 30); // 30-second time window
    for ($i = -1; $i <= 1; $i++) { // Allow a small time window for synchronization
        $expectedOTP = hash_hmac('sha1', $timestamp + $i, $secretKey);
        if ($expectedOTP === $userOTP) {
            return true;
        }
    }
    return false;
}

// Example usage
$userOTP = "123456"; // Replace with the OTP entered by the user
if (verifyOTP($secretKey, $userOTP)) {
    echo "OTP is valid!";
} else {
    echo "OTP is invalid!";
}
?>