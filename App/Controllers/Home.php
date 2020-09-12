<?php
namespace App\Controllers;

use Core\Utility;
use \Core\View;

class Home extends \Core\Controller
{
    private $indexView = "Home/index.html";

    public function indexAction()
    {
        View::renderTemplate($this->indexView);
    }

    public function contactAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $msg="contactError";
            if (Utility::areSet([
                $_POST['subject'],
                $_POST['message'],
                $_POST['mail'],
                $_POST['name']
            ])) {

                // To block bot from spamming
                if ($_POST['ANTI_BOT']) {
                    return;
                }

                $to      = 'contact@smfcoder.com';
                $subject = htmlspecialchars($_POST['subject']);
                $message = View::getRenderTemplate('Home/contact-mail.html', [
                    'name' => $_POST['name'],
                    'subject' => $_POST['subject'],
                    'message' => $_POST['message']
                ]);
                $headers = array(
                    'From' => filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL),
                    'Reply-To' => filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL),
                    'X-Mailer' => 'PHP/' . phpversion(),
                    'Content-Type' => 'text/html; charset=UTF-8'
                );

                mail($to, $subject, $message, $headers);
                $msg="contactSuccess";
            }

        }
        echo $msg;
        header("Location:/#contact?$msg");
    }

    protected function before()
    {
        if (isset($_GET['key'])) {
            echo $_GET['key'];
        }
        return true;
    }

    protected function after()
    {
    }
}