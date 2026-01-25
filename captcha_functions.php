<?php
session_start();

// Generate simple math CAPTCHA
function generateCaptcha() {
    // Only generate if not already generated in this session
    if (isset($_SESSION['captcha_answer']) && isset($_SESSION['captcha_time'])) {
        // Return existing CAPTCHA
        return $_SESSION['captcha_question'];
    }
    
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $operators = ['+', '-', '*'];
    $operator = $operators[array_rand($operators)];
    
    switch($operator) {
        case '+':
            $answer = $num1 + $num2;
            break;
        case '-':
            $answer = $num1 - $num2;
            break;
        case '*':
            $answer = $num1 * $num2;
            break;
    }
    
    $question = "$num1 $operator $num2";
    
    $_SESSION['captcha_answer'] = $answer;
    $_SESSION['captcha_time'] = time();
    $_SESSION['captcha_question'] = $question;
    
    return $question;
}

// Verify CAPTCHA answer
function verifyCaptcha($userAnswer) {
    if (!isset($_SESSION['captcha_answer']) || !isset($_SESSION['captcha_time'])) {
        return false;
    }
    
    // CAPTCHA expires after 10 minutes
    if (time() - $_SESSION['captcha_time'] > 600) {
        unset($_SESSION['captcha_answer']);
        unset($_SESSION['captcha_time']);
        unset($_SESSION['captcha_question']);
        return false;
    }
    
    $isValid = ((int)$userAnswer === (int)$_SESSION['captcha_answer']);
    
    if ($isValid) {
        unset($_SESSION['captcha_answer']);
        unset($_SESSION['captcha_time']);
        unset($_SESSION['captcha_question']);
    }
    
    return $isValid;
}
?>

