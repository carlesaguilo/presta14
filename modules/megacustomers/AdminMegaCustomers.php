<?php
/**
 * Description of AdminMegaCustomers
 *
 * @author Jorge Donet
 */
class AdminMegaCustomers extends AdminTab{
    
    function display(){

				echo Module::getInstanceByName('megacustomers')->getContent(array());
    }
}
?>
