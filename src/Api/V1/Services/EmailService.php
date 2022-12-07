<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Models\Email;
use Aparlay\Core\Notifications\EmailErrorNotRecognized;
use Illuminate\Support\Facades\Notification;

class EmailService extends AbstractService
{
    private array $errors = [
        '4.2.1' => 'The email account that you tried to reach is denying to receive new email, please try again later.', // The recipient’s mailbox has exceeded its storage limit.
        '4.2.2' => 'The email account that you tried to reach is full and cannot receive any new email.', // The recipient’s mailbox has exceeded its storage limit.
        '4.5.0' => 'You email service provider reject our email, please check your spam folder.', // Requested action not taken – The user’s mailbox is unavailable. The mailbox has been corrupted or placed on an offline server, or your email hasn’t been accepted for IP problems or blacklisting.
        '4.5.2' => 'You email service provider reject our email, please check your spam folder.', // Too many emails sent or too many recipients: more in general, a server storage limit exceeded.
        '4.7.1' => 'You email service provider reject our email, please check your spam folder.', // An error of your mail server, often due to an issue of the local anti-spam filter.
        '5.1.0' => 'The email account that you tried to reach does not exist.', // Bad email address.
        '5.1.1' => 'The email account that you tried to reach does not exist.', // Bad email address.
        '5.1.2' => 'The email account that you tried to reach does not exist.', // the host server for the recipient’s domain name cannot be found.
        '5.1.3' => 'The email account that you tried to reach does not exist.', // another problem concerning address misspelling. In few cases, however, it’s related to an authentication issue.
        '5.2.3' => 'The email account that you tried to reach is full and cannot receive any new email.', // The total size of your mailing exceeds the recipient server’s limits.
        '5.3.0' => 'You email service provider reject our email, please check your spam folder.', // Normally, an authentication problem. But sometimes it’s about the recipient’s server blacklisting yours, or an invalid email address.
        '5.4.1' => 'You email service provider reject our email, please check your spam folder.', // The recipient address rejected your message: normally, it’s an error caused by an anti-spam filter.
        '5.5.0' => 'The email account that you tried to reach does not exist.', // It usually defines a non-existent email address on the remote side.
        '5.5.1' => 'The email account that you tried to reach does not exist.', // User not local or invalid address – Relay denied. Meaning, if both your address and the recipient\'s are not locally hosted by the server, a relay can be interrupted.
        '5.5.2' => 'The email account that you tried to reach is full and cannot receive any new email.', // Requested mail actions aborted – Exceeded storage allocation: simply put, the recipient’s mailbox has exceeded its limits.
        '5.5.3' => 'The email account that you tried to reach does not exist.', // Requested action not taken – Mailbox name invalid. That is, there’s an incorrect email address into the recipients line.
    ];

    /**
     * Responsible to create a more user-friendly error message based on mail server error.
     *
     * @param  Email  $email
     * @return string
     */
    public function humanizeError(Email $email): string
    {
        foreach ($this->errors as $code => $message) {
            if ($email->dsn === $code) {
                return __($message);
            }
        }

        Notification::send($email, new EmailErrorNotRecognized());

        return __('There is an unknown error with your email address we are investigating the issue, meantime you can use other email address.');
    }
}
