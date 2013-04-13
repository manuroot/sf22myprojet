<?php
namespace Application\PaypalExpressCheckoutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Payum\Paypal\ExpressCheckout\Nvp\Bridge\Doctrine\Entity\PaymentDetails as BasePaymentDetails;

/**
 * @ORM\Entity
 */
class PaymentDetails extends BasePaymentDetails
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="added_date", type="datetime", nullable=false)
     */
    private $addedDate;

   
     /**
     * Set addedDate
     *
     * @param \DateTime $addedDate
     * @return CertificatsCenter
     */
    public function setAddedDate($addedDate)
    {
        // $this->addedDate = new \DateTime('now');
        $this->addedDate = $addedDate;
    
        return $this;
    }

    /**
     * Get addedDate
     *
     * @return \DateTime 
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }
    
     public function __construct()
  {
   // parent::__construct();
    $this->addedDate = new \DateTime('now');
    
  }
  
}