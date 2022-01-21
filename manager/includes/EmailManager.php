<?php

@define( 'EMAIL_SMTP', true);
@define( 'EMAIL_CHAR_SET', "UTF-8" );
@define( 'EMAIL_DEBUG', 0);
@define( 'EMAIL_DEBUG_ADDRESS', 'cmendez@carlosmendez.com' );
@define( 'EMAIL_SMTP_AUTH', true );
//@define( 'EMAIL_SMTP_SECURE', 'ssl' );
@define( 'EMAIL_DEV_ENV', false );

@define( 'EMAIL_FROM_ADDRESS_DEFAULT', 'avocomm@avocadosfrommexico.com' );
@define( 'EMAIL_FROM_NAME_DEFAULT', 'AvoComm' );

@define( 'EMAIL_PORT', 587 );
//@define( 'EMAIL_USERNAME', "avocomm@avocadosfrommexico.com" );
//@define( 'EMAIL_PASSWORD', "AV0C0MM@dm1N!2019" );
@define( 'EMAIL_USERNAME', "ambo@avocadosfrommexico.com" );
@define( 'EMAIL_PASSWORD', "Ww3BR*nn663OivMYfY8NbaWxuf3!" );
@define( 'EMAIL_HOST', "us-smtp-outbound-1.mimecast.com" );

require_once( 'PHPMailer.php' );
require_once( 'SMTP.php' );
require_once( 'Exception.php' );

/** Class EmailManager **/
class EmailManager {
    private $templateId, $subject, $to, $toName, $from, $fromName;
    private $template;
    private $isHtml = true;
    private $db;
    private $mergeVariables = [];

    /**
     * EmailManager constructor.
     * @param string $subject
     * @param int $templateId
     * @param string $to
     * @param string $toName
     * @param string $from
     * @param string $fromName
     * @param bool $isHtml
     * @param array $mergeVariables
     * @throws Exception
     */
    function __construct($db, $subject = '', $templateId = self::EMAIL_CENTER_TEMPLATE, $to = '', $toName = '', $from = '', $fromName = '', $isHtml = true, $mergeVariables = []) {
      $this->db = $db;

      $this->setSubject($subject);
      $this->setTemplateId($templateId);
      $this->setTemplate();

      $this->setTo($to);
      $this->setToName($toName);
      $this->setFrom($from);
      $this->setFromName($fromName);
      $this->setIsHtml($isHtml);
      $this->setMergeVariables($mergeVariables);
    }

    public function setIsHtml($val) {
      $this->isHtml = $val;
    }

    /**
     * Set Subject
     * @param $val
     */
    public function setSubject($val) {
      $this->subject = $val;
    }

    /**
     * Get Subject
     * @return mixed
     */
    public function getSubject() {
      $subject = $this->subject;
      // Since we merge two layers of templates in some cases we need to run this twice.
      foreach ($this->mergeVariables as $variable => $variableValue) {
          $subject = str_replace("{" . $variable . "}", $variableValue, $subject);
      }
      return $subject;
    }

    /**
     * Set Template
     * @param $val
     */
    public function setTemplateId($val) {
      $this->templateId = $val;
    }

    /**
     * Return template with merge variables already processed.
     * @return mixed
     */
    public function getTemplate() {
      $template = $this->template;

      // Since we merge two layers of templates in some cases we need to run this twice.
      foreach ($this->mergeVariables as $variable => $variableValue) {
        if ( EMAIL_DEV_ENV == true ) $variableValue = str_replace( 'https://avocomm.avocadosfrommexico.com', 'https://devavocomm.avocadosfrommexico.com', $variableValue );
        $template = str_replace("{" . $variable . "}", $variableValue, $template);
      }
      foreach ($this->mergeVariables as $variable => $variableValue) {
          $template = str_replace("{" . $variable . "}", $variableValue, $template);
      }

      return $template;
    }

    /**
     * Set To Address
     * @param $val
     */
    public function setTo($val) {
      $this->to = $val;
    }

    /**
     * Get To Address
     * @return mixed
     */
    public function getTo() {
      return $this->to;
    }

    /**
     * Set To Name
     * @param $val
     */
    public function setToName($val) {
      $this->toName = $val;
    }

    /**
     * Get To Name
     * @return mixed
     */
    public function getToName() {
      return $this->toName;
    }

    /**
     * Set From Address
     * @param $val
     */
    public function setFrom($val) {
      $this->from = $val;
    }

    /**
     * Get From Address
     * @return string
     */
    public function getFrom() {
      if ($this->from == '') {
        return EMAIL_FROM_ADDRESS_DEFAULT;
      }

      return $this->from;
    }

    /**
     * Set From Name
     * @param $val
     */
    public function setFromName($val) {
        $this->fromName = $val;
    }

    /**
     * Get From Name
     * @return string
     */
    public function getFromName() {
      if ($this->fromName == '') {
        return EMAIL_FROM_NAME_DEFAULT;
      }

      return $this->fromName;
    }

    /**
     * Set merge variables for templating.
     *
     * @param $val
     * @throws Exception
     */
    public function setMergeVariables($val) {
      if (!is_array($val)) {
        throw new \Exception('EmailManager@setMergeVariables requires input to be an array.');
      }

      $this->mergeVariables = $val;
    }

    /**
     * Sets the local template
     * @param $id
     * @return bool|int
     */
    function setTemplate() {
      $id = (int)$this->templateId;
      if ( $id ) {
        $sql = 'SELECT template, subject FROM email_templates WHERE id = ?';
        $temps = $this->db->query( $sql, array( $id ) );
        if ( $this->db->num_rows() > 0 ) {
          $temp = $this->db->fetch( $temps );
          if ($this->subject == '') {
              $this->subject = $temp['subject'];
          }
          $this->template = $temp['template'];
          error_log($this->template);
          return;
        } else {
          $this->error = 'Could not find the specified template';
          return ERROR;
        }
      } else {
          $this->error = 'Please pass in a valid ID.';
          return ERROR;
      }
    }

    /**
     * Send email
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send() {
      $mail = new PHPMailer\PHPMailer\PHPMailer();

      // Settings
      if (EMAIL_SMTP) {
        $mail->IsSMTP();
      }

      $mail->CharSet = EMAIL_CHAR_SET;
      $mail->Host       = EMAIL_HOST;
      $mail->SMTPDebug  = EMAIL_DEBUG;
      $mail->SMTPAuth   = EMAIL_SMTP_AUTH;
      $mail->Port       = EMAIL_PORT;
      //$mail->SMTPSecure = EMAIL_SMTP_SECURE;
      $mail->Username   = EMAIL_USERNAME;
      $mail->Password   = EMAIL_PASSWORD;

      //Validate
      if ($this->getTo() == '') {
        throw new \Exception("Can't send email. No to address was set. ");
      }

      error_log("EMAIL_DEBUG: Sending new email");
      // Setup email
      $mail->setFrom( $this->getFrom(), $this->getFromName() );

      if (EMAIL_DEBUG == 0) {
        $mail->addAddress($this->to, $this->toName);
        error_log("EMAIL_DEBUG: Sending email ID: " . $this->templateId . " to " . $this->to);
      } else {
        $mail->addAddress(EMAIL_DEBUG_ADDRESS, "DEBUG");
        error_log("EMAIL_DEBUG: Sending email ID: " . $this->templateId . " to " . EMAIL_DEBUG_ADDRESS . " but it was meant for " . $this->to );
      }

      // Content
      $mail->isHTML( $this->isHtml );
      $mail->Subject 		= $this->getSubject();
      $mail->Body    		= $this->getTemplate();
      return $mail->send();
    }
}
?>
