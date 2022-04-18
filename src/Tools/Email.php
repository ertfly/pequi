<?php

namespace Pequi\Tools;

use Exception;
use Swift_Mailer;
use Swift_Message;
use Swift_Plugins_Loggers_ArrayLogger;
use Swift_SmtpTransport;

class Email
{
    public static function send($toEmails, $subject, $message, $debug = false, $config)
    {
        $smtp_url = isset($config['email_url']) ? $config['email_url'] : null;
        $smtp_port = isset($config['email_port']) ? $config['email_port'] : null;
        $smtp_pwd = isset($config['email_password']) ? $config['email_password'] : null;
        $smtp_user = isset($config['email_user']) ? $config['email_user'] : null;
        $smtp_fromEmail = isset($config['email_from_email']) ? $config['email_from_email'] : null;
        $smtp_fromName = isset($config['email_from_name']) ? $config['email_from_name'] : null;
        $smtp_protocol = isset($config['email_protocol']) ? $config['email_protocol'] : null;
        $smtp_domain = isset($config['email_domain']) ? $config['email_domain'] : null;

        $transport = (new Swift_SmtpTransport($smtp_url, intval($smtp_port), $smtp_protocol))
            ->setUsername($smtp_user)
            ->setPassword($smtp_pwd);

        if ($smtp_domain) {
            $transport->setLocalDomain($smtp_domain);
        }

        $logger = null;
        if ($debug) {
            $logger = new Swift_Plugins_Loggers_ArrayLogger();
        }

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        if ($debug) {
            $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));
        }

        $emails = array();
        foreach ($toEmails as $email => $name) {
            $emails[$email] = $name;
        }

        // Create a message
        $message = (new Swift_Message($subject))
            ->setFrom([$smtp_user => $smtp_fromName])
            ->setTo($emails)
            ->setBody($message, 'text/html', 'utf-8')
            ->setReplyTo($smtp_fromEmail, $smtp_fromName);

        $send = $mailer->send($message);

        // Send the message
        if (!$send) {
            if ($debug) {
                throw new Exception($logger->dump());
            } else {
                throw new Exception('Ocorreu um erro ao enviar o email solicitado, favor entrar em contato com o suporte!');
            }
        }

        return $send;
    }
}
