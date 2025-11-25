<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Utility;
use Core\View;

class Home extends Controller
{
    private string $indexView = "Home/index.html";

    public function indexAction(): void
    {
        View::renderTemplate($this->indexView);
    }

    public function contactAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $msg = "contactError";

            if (Utility::areSet(
                $_POST['subject'] ?? null,
                $_POST['message'] ?? null,
                $_POST['mail'] ?? null,
                $_POST['name'] ?? null
            )) {
                // To block bot from spamming the contact form (honeypot technique)
                if (!empty($_POST['ANTI_BOT'])) {
                    return;
                }

                $to = $_ENV['MAIL_FROM'] ?? 'contact@example.com';
                $subject = htmlspecialchars($_POST['subject'], ENT_QUOTES, 'UTF-8');
                $message = View::getRenderTemplate('Home/contact-mail.html', [
                    'name' => htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'),
                    'subject' => $subject,
                    'message' => htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8')
                ]);

                $fromEmail = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
                $headers = [
                    'From' => $fromEmail,
                    'Reply-To' => $fromEmail,
                    'X-Mailer' => 'PHP/' . phpversion(),
                    'Content-Type' => 'text/html; charset=UTF-8'
                ];

                mail($to, $subject, $message, $headers);
                $msg = "contactSuccess";
            }

            echo $msg;
            header("Location:/#contact?$msg");
        }
    }

    protected function before(): mixed
    {
        if (isset($_GET['key'])) {
            echo htmlspecialchars($_GET['key'], ENT_QUOTES, 'UTF-8');
        }
        return true;
    }

    protected function after(): void {}
}
