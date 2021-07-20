<?php

declare(strict_types=1);

namespace Lea\Module\ContractorModule\Entity;

use Lea\Core\Entity\Entity;

class ContractorEmployee extends Entity
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $workplace;

    
    /**
     * @var int
     */
    private $contractor_id;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName(string $name)
    {
        $this->name = $name;
        
        return $this;
    }
    public function getSurname()
    {
        return $this->surname;
    }
    
    public function setSurname(string $surname)
    {
        $this->surname = $surname;
        
        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail(string $email)
    {
        $this->email = $email;
        
        return $this;
    }
    
    public function getPhone()
    {
        return $this->phone;
    }
    
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
        
        return $this;
    }
    
    public function getWorkplace(): string
    {
        return $this->workplace;
    }
    
    public function setWorkplace(string $workplace)
    {
        $this->workplace = $workplace;
    }
    
    /**
     * Get the value of contractor_id
     *
     * @return  int
     */
    public function getContractorId()
    {
        return $this->contractor_id;
    }
    
    /**
     * Set the value of contractor_id
     *
     * @param  int  $contractor_id
     *
     * @return  self
     */
    public function setContractorId(int $contractor_id)
    {
        $this->contractor_id = $contractor_id;
        
        return $this;
    }
}
