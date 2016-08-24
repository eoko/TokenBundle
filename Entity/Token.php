<?php

namespace Wini\TokenBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Token
 *
 * @ORM\Table(schema="app", name="Token")
 * @ORM\Entity
 */
class Token 
{
    const ONE_MINUTE = 60;
    const ONE_HOUR   = self::ONE_MINUTE * 60;
    const ONE_DAY    = self::ONE_HOUR * 24;
    const ONE_WEEK   = self::ONE_DAY * 7;
    const ONE_MONTH  = self::ONE_WEEK * 4;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", unique=true, length=255)
     */
    private $token;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", nullable=true, type="integer")
     */
    private $duration;

    /**
     * @var \DateTime
     * @ORM\Column(name="start", type="datetime")
     * */
    private $start;
    
    /**
     * @var string
     * @ORM\Column(name="data", type="string", nullable = true, length=255)
     */
    private $data;

    public function __construct()
    {
        $this->start = new DateTime();
    }
    
    /**
     * Data lié a la token
     * @param string $data
     * @return \Wini\TokenBundle\Entity\Token
     */
    public function setData($data = null) 
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Va hasher une token
     * @param string $seed Donnée initiales
     * @param boolean $raw si on retourne le hash en buffer binaire
     * @return string 
     */
    private function hashToken($seed = null, $raw = false) 
    {
        return hash('sha512', uniqid(null, true) . mt_rand(0, time(NULL)) . ($seed ? $seed : ''), $raw);
    }
    
    /**
     * Va remplir la token
     * @param integer $rounds
     * @param string $seed
     * @return \Wini\TokenBundle\Entity\Token
     */
    public function fillToken($rounds = 32, $seed = null)
    {
        $token = $this->hashToken($seed, false);
        
        for ($i = 0; $i < $rounds; $i++) {
            $token = $this->hashToken($token, false); 
        }
        
        $this->setToken(base64_encode($this->hashToken($token, true)));
        return $this;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Token
     */
    public function setToken($token) {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Définit la durée de la token
     * @param integer $duration Durée de la clée en secondes
     * @return \Wini\TokenBundle\Entity\Token
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Récupère la durée de la token
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }
    
    /**
     * Récupère la date d'expiration
     * @return \DateTime
     */
    public function getExpireDate()
    {
        $duration = $this->getDuration();
        if ($duration === null) {
            return null;
        } 
        
        $date = clone $this->getStart();   
        return $date->modify('+' . $duration . 'seconds');
    }
    
    /**
     * @param DateTime $start
     * @return \Wini\TokenBundle\Entity\Token
     */
    public function setStart(DateTime $start = null) {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart() {
        return $this->start;
    }

    /**
     * @return boolean
     */
    public function isExpired() {
        $now = new \DateTime();
        if ($this->getExpireDate() === null ||
                $this->getStart() <= $now && $this->getExpireDate() >= $now)
            return false;
        return true;
    }
}