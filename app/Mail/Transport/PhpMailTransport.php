<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;

class PhpMailTransport implements TransportInterface
{
    public function send(RawMessage $message, ?object $envelope = null): ?SentMessage
    {
        $messageString = $message->toString();
        
        // Parse the message to extract headers and body
        $parts = explode("\r\n\r\n", $messageString, 2);
        $headers = $parts[0] ?? '';
        $body = $parts[1] ?? '';
        
        // Extract To header
        $to = '';
        if (preg_match('/^To:\s*(.+)$/m', $headers, $matches)) {
            $to = trim($matches[1]);
            // Remove angle brackets if present
            $to = preg_replace('/.*<(.+)>.*/', '$1', $to);
        }
        
        // Extract Subject header
        $subject = '';
        if (preg_match('/^Subject:\s*(.+)$/m', $headers, $matches)) {
            $subject = trim($matches[1]);
        }
        
        // Remove To and Subject from headers as they're passed separately to mail()
        $headers = preg_replace('/^To:\s*.+$/m', '', $headers);
        $headers = preg_replace('/^Subject:\s*.+$/m', '', $headers);
        $headers = trim(preg_replace('/\n\s*\n/', "\n", $headers));
        
        // Send email using PHP's built-in mail() function
        $result = mail($to, $subject, $body, $headers);
        
        if (!$result) {
            throw new \Exception('Failed to send email using PHP mail() function');
        }
        
        return new SentMessage($message, $envelope ?? new \stdClass());
    }

    public function __toString(): string
    {
        return 'php://mail';
    }
}