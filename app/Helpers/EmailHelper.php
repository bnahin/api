<?php
namespace App\Helpers;

use Mail;

/**
 * Class EmailHelper
 * @package App\Helpers
 */
class EmailHelper {
    /**
     * @param $email
     * @param $subject
     * @param $template
     * @param $data
     */
    public static function sendEmail($email, $subject, $template, $data)
    {
        Mail::send($template, $data, function ($msg) use ($data, $email, $subject) {
            $msg->from('no-reply@vatusa.net', "VATUSA Web Services");
            $msg->to($email);
            $msg->subject("[VATUSA] $subject");
        });
    }

    /**
     * Send email "to" from with emails array set as BCC.
     *
     * @param $fromEmail
     * @param $fromName
     * @param $emails
     * @param $subject
     * @param $template
     * @param $data
     */
    public static function sendEmailBCC($fromEmail, $fromName, $emails, $subject, $template, $data)
    {
        Mail::send($template, $data, function ($msg) use ($data, $fromEmail, $emails, $fromName, $subject) {
            $msg->from("no-reply@vatusa.net", "VATUSA Web Services");
            $msg->to($fromEmail, $fromName);
            $msg->subject("[VATUSA] $subject");
            $msg->bcc($emails);
        });
    }

    /**
     * @param string $email
     * @param string $from_email
     * @param string $from_name
     * @param string $subject
     * @param string $template
     * @param array $data
     */
    public static function sendEmailFrom($email, $from_email, $from_name, $subject, $template, $data)
    {
        Mail::send($template, $data, function ($msg) use ($data, $from_email, $from_name, $email, $subject) {
            $msg->from("no-reply@vatusa.net", "$from_name");
            $msg->replyTo($from_email, $from_name);
            $msg->to($email);
            $msg->subject("[VATUSA] $subject");
        });
    }

    /**
     * Send Email, checking for facility template first
     *
     * @param string|array $email
     * @param string $subject
     * @param string $template
     * @param array $data
     */
    public static function sendEmailFacilityTemplate($email, $subject, $fac, $template, $data)
    {
        $global_templates = [
            'examassigned' => "emails.exam.assign",
            'exampassed' => 'emails.exam.passed',
            'examfailed' => 'emails.exam.failed',
            'transferpending' => 'emails.transfers.pending'
        ];
        if (view()->exists("emails.facility.$fac." . $template)) {
            $template = "emails.facility.$fac.$template";
        } else {
            $template = $global_templates[$template];
        }

        static::sendEmail($email, $subject, $template, $data);
    }
    /**
     * Send an email from support
     *
     * @param string|array $email
     * @param string $subject
     * @param string $template
     * @param array $data
     */

    public static function sendSupportEmail($email, $ticket, $subject, $template, $data)
    {
        $backup = Mail::getSwiftMailer();
        $transport = \Swift_SmtpTransport::newInstance("mail.vatusa.net", 587, null);
        $transport->setUsername(env("SUPPORT_EMAIL_USERNAME"));
        $transport->setPassword(env("SUPPORT_EMAIL_PASSWORD"));
        $support = new \Swift_Mailer($transport);
        Mail::setSwiftMailer($support);
        Mail::send($template, $data, function($msg) use ($data, $email, $subject, $ticket) {
            $msg->from('support@vatusa.net', 'VATUSA Help Desk');
            $msg->bcc($email);
            $msg->subject("[VATUSA Help Desk] (Ticket #$ticket) $subject");
        });
        Mail::setSwiftMailer($backup);
    }

    /**
     * @param $email
     * @param $subject
     * @param $template
     * @param $data
     */
    public static function sendWelcomeEmail($email, $subject, $template, $data)
    {
        $welcome = $data['welcome'];
        $welcome = preg_replace("/%fname%/", $data['fname'], $welcome);
        $welcome = preg_replace("/%lname%/", $data['lname'], $welcome);
        static::sendEmail($email, $subject, $template, ['welcome' => $welcome]);
    }
}
