<?php
declare(strict_types=1);

namespace Services;

/**
 * MailService
 * Sends notification mails
 *
 */
class MailService {

  /**
   * Prepares a notification email and send the creation url to the notified email address
   *
   * @param string $url
   * @return void
   */
  public static function sendNotification(string $url): void {
    if ( !defined('NOTIFICATION_EMAIL') || NOTIFICATION_EMAIL === false )
      return;

    $email = NOTIFICATION_EMAIL;
    $subject = NOTIFICATION_SUBJECT ?? 'Mystic Micro Site - Creation Notification';
    $message = "
      A Mystic Micro Site has been created for you:
      $url
    ";

    self::sendMail($email, $subject, $message);
  }

  /**
   * Sends an e-mail
   *
   * @param string $email
   * @param string $subject
   * @param string $message
   * @return void
   */
  protected static function sendMail(string $email, string $subject, string $message): void {
    mail($email, $subject, $message);
  }

}
