<?php

namespace Pequi\Tools;

use AnexusPHP\Business\Configuration\Repository\ConfigurationRepository;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Swift_Plugins_Loggers_ArrayLogger;
use Swift_SmtpTransport;

class Email
{
    public static function send($toEmails, $subject, $message, $debug = false)
    {
        $smtp_url = ConfigurationRepository::getValue('email_url');
        $smtp_port = ConfigurationRepository::getValue('email_port');
        $smtp_pwd = ConfigurationRepository::getValue('email_password');
        $smtp_user = ConfigurationRepository::getValue('email_user');
        $smtp_fromEmail = ConfigurationRepository::getValue('email_from_email');
        $smtp_fromName = ConfigurationRepository::getValue('email_from_name');
        $smtp_protocol = ConfigurationRepository::getValue('email_protocol');
        $smtp_domain = ConfigurationRepository::getValue('email_domain');

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
