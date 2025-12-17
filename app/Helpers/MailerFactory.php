<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Mail\Mailer;
use App\User;

class MailerFactory
{
    protected $mailer;
    protected $fromAddress = "";
    protected $fromName = "";

    /**
     * MailerFactory constructor.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->fromAddress = env('MAIL_FROM_ADDRESS');
        $this->fromName = env('MAIL_FROM_NAME');
    }
    /**
     * sendWelcomeEmail
     *
     *
     * @param $subject
     * @param $user
     */
    public function sendWelcomeEmail($user)
    {
        $subject = "Thank you for registering";
        try {
            $this->mailer->send("emails.welcome", ['user' => $user, 'subject' => $subject], function ($message) use ($subject, $user) {

                $message->from($this->fromAddress, $this->fromName)
                    ->to($user->email)->subject($subject);
            });
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
        return true;
    }

    public function sendGeneralEmail($user, $subject, $body = 'Thank you for here.', $documents = [])
    {
        try {
            $this->mailer->send("emails.general", ['user' => $user, 'subject' => $subject, 'body' => $body], function ($message) use ($subject, $user, $documents) {

                $message->from($this->fromAddress, $this->fromName)
                    ->to($user->email)->subject($subject);

                if (isset($documents) && !empty($documents)) {
                    foreach ($documents as $document) {
                        $message->attach($document['path'], [
                            'as' => $document['filename'],
                            'mime' => 'application/pdf'
                        ]);
                    }
                }
            });
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
        return true;
    }

    public function sendEnquiryToDealerEmail($purchaseEnquiry, $allDealer, $loginUser)
    {
        $subject = "New Purchase Enquiry";
        try {
            foreach ($allDealer as $key => $data) {
                $this->mailer->send("emails.purchase_enquiry", ['purchaseEnquiry' => $purchaseEnquiry, 'data' => $data, 'loginUser' => $loginUser, 'subject' => $subject], function ($message) use ($subject, $data) {

                    $message->from($this->fromAddress, $this->fromName)
                        ->to($data['email'])->subject($subject);
                });
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
        return true;
    }
    public function sendDealerStatusLevelUpdateEmail($dealer, $changeStatus)
    {
        if ($dealer->status_level != $changeStatus) {
            $subject = "Your account level status.";
            $type = 'normal';
            if ($dealer->status_level < $changeStatus) {
                $subject = "Congratulations, your account level was upgraded.";
                $type = 'upgraded';
            } else {
                $subject = "Your account level has been downgraded.";
                $type = 'downgraded';
            }

            if (isset($type) && !empty($type) && $type != 'normal') {
                try {
                    $this->mailer->send("emails.dealer_status", ['dealer' => $dealer, 'subject' => $subject, 'type' => $type], function ($message) use ($subject, $dealer) {

                        $message->from($this->fromAddress, $this->fromName)
                            ->to($dealer->email)->subject($subject);
                    });
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                }
                return true;
            }
            return false;
        } else {
            return false;
        }
    }
}
