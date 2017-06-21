<?php
/**
 * Created by PhpStorm.
 * User: barsi.adam
 * Date: 2017.06.08.
 * Time: 11:55
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *@ORM\Entity(repositoryClass="AppBundle\Repository\DebtRepository")
 *@ORM\Table(name="debt")
 *
 */
class Debt {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $debtor;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $creditor;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="integer")
     */
    private $paid;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $comp_name;

    public function getId()
    {
        return $this->id;
    }

    public function getDebtor()
    {
        return $this->debtor;
    }

    public function getCreditor()
    {
        return $this->creditor;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getPaid()
    {
        return $this->paid;
    }

    public function getComp_name()
    {
        return $this->comp_name;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setDebtor($debtor)
    {
        $this->debtor = $debtor;

        return $this;
    }

    public function setCreditor($creditor)
    {
        $this->creditor = $creditor;

        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    public function setComp_name($comp_name)
    {
        $this->comp_name = $comp_name;

        return $this;
    }
}