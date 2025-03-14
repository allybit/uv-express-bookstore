<?php
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
?>