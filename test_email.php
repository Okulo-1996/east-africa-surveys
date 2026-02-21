// Add this temporary test function
function testSimpleEmail($to) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info.eastafricasurveys@gmail.com';
        $mail->Password = 'YOUR-APP-PASSWORD';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('info.eastafricasurveys@gmail.com');
        $mail->addAddress($to);
        $mail->Subject = 'Plain Text Test';
        $mail->Body = 'This is a plain text test.'; // No HTML
        
        return $mail->send();
    } catch (Exception $e) {
        echo "Error: " . $mail->ErrorInfo;
        return false;
    }
}