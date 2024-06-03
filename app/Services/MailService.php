<?php

namespace App\Services;

use App\Mail\SendMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class MailService
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Sending email
     *
     * @param string|array $email         Recipient's email address.
     * @param string $subject       Email subject.
     * @param array $data           Email content data.
     * @param string $layout        Email layout/template.
     * @param mixed $ccTo          CC recipient (optional).
     * @param mixed $bccTo         BCC recipient (optional).
     *
     * @return void
     */
    public function send($email, $subject, $data, $layout, $ccTo = null, $bccTo = null)
    {
        Mail::to($email)->cc($ccTo)->bcc($bccTo)->send(new SendMail($subject, $data, $layout));
    }

    public function sendMail($email, $title, $data)
    {
        $this->send($email, $title, $data, 'mails.bill', null, null);
    }
}
