<?php
function send_mail_raw($to, $subject, $htmlBody) {
    if (MAIL_USE_SMTP && SMTP_HOST) {
        return send_smtp($to, $subject, $htmlBody);
    }
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_EMAIL . '>';
    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $htmlBody, implode("\r\n", $headers));
}

function send_smtp($to, $subject, $htmlBody) {
    $host = SMTP_HOST;
    $port = (int)SMTP_PORT;
    $user = SMTP_USER;
    $pass = SMTP_PASS;
    $tls = SMTP_TLS;
    $errno = 0;
    $errstr = '';
    $transport = $tls ? 'tls://' . $host : $host;
    $fp = @stream_socket_client($transport . ':' . $port, $errno, $errstr, 15);
    if (!$fp) {
        return false;
    }
    stream_set_timeout($fp, 15);
    $read = function () use ($fp) {
        $d = '';
        while ($l = fgets($fp, 515)) {
            $d .= $l;
            if (strlen($l) < 4 || $l[3] === ' ') {
                break;
            }
        }
        return $d;
    };
    $read();
    fwrite($fp, "EHLO localhost\r\n");
    $read();
    if ($tls && $port === 587) {
        fwrite($fp, "STARTTLS\r\n");
        $read();
        stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        fwrite($fp, "EHLO localhost\r\n");
        $read();
    }
    if ($user !== '') {
        fwrite($fp, "AUTH LOGIN\r\n");
        $read();
        fwrite($fp, base64_encode($user) . "\r\n");
        $read();
        fwrite($fp, base64_encode($pass) . "\r\n");
        $read();
    }
    fwrite($fp, 'MAIL FROM:<' . MAIL_FROM_EMAIL . ">\r\n");
    $read();
    fwrite($fp, 'RCPT TO:<' . $to . ">\r\n");
    $read();
    fwrite($fp, "DATA\r\n");
    $read();
    $msg = "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
    $msg .= "From: " . MAIL_FROM_NAME . ' <' . MAIL_FROM_EMAIL . ">\r\n";
    $msg .= "To: <" . $to . ">\r\n";
    $msg .= "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n\r\n";
    $msg .= $htmlBody . "\r\n.\r\n";
    fwrite($fp, $msg);
    $read();
    fwrite($fp, "QUIT\r\n");
    fclose($fp);
    return true;
}
