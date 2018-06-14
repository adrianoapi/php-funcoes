<?php require '../_config.php'; 

class Account
{
    
  private $_totalBalance = 0;
 
  public function makeDeposit( $amount )
  {
    $this->_totalBalance += $amount;
  }
 
  public function makeWithdrawal( $amount )
  {
    if ( $amount < $this->_totalBalance ) {
      $this->_totalBalance -= $amount;
    } else {
      die( "Insufficient funds<br />" );
    }
  }
 
  public function getTotalBalance()
  {
    return $this->_totalBalance;
  }
  
}

$bank = new Account;
$bank->makeDeposit(750);
$bank->makeWithdrawal(152);
$bank->makeWithdrawal(273);
echo $bank->getTotalBalance() . "<br/>";                                        // Display '325'
$bank->makeWithdrawal(700);                                                     // Display 'Insufficient funds'

?>