<?php

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class SessionTimeoutPlugin extends AbstractPlugin
{

   public function timeout()
   {
      $configuration = $this->getController()->getServiceLocator()->get('config');
      $timeout = $configuration['configuration']['session']['timeout'];
      if($timeout === 0) {
         return true;
      }

      else {
         if($_SESSION['bareos']['rememberme']){
            setcookie('bareos_timeout', 0, '2147483647');
            return true;
         }
         else if($_SESSION['bareos']['idletime'] + $timeout > time()) {
            $_SESSION['bareos']['idletime'] = time();
            setcookie('bareos_timeout', 1, time() + $timeout);
            return true;
         }
         else {
            setcookie('bareos_timeout', -1, time()-60);
            session_destroy();
            return false;
         }
      }
   }

   public function isValid()
   {
       if($_SESSION['bareos']['authenticated'] && $this->timeout() ){
          return true;
      }
      else {
         return false;
      }
   }
}
