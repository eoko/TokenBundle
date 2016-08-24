<?php

namespace Wini\TokenBundle\Manager;

use Wini\TokenBundle\Entity\Token;
use Wini\Manager\AbstractFlushManager;
use Doctrine\ORM\EntityManager;

class TokenManager extends AbstractFlushManager {

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    /**
     * Va créer une token
     * @param integer $duration Durée de vie de la token en secondes
     * @param DateTime $start Date de début de vie de la token
     * @return Token
     */
    public function create($data = null, $duration = null, $start = null) {
        $token = new Token();
        $token->setData($data)
              ->fillToken();
        
        if ($start) {
            $token->setStart($start);
        }
        
        if ($duration) {
            $token->setDuration($duration);
        }
        
        $this->em->persist($token);
        
        if ($this->shouldFlush()) {
            $this->em->flush();
        }
        
        return $token;
    }
    
    /**
     * Supprime la token
     * @param Token $token
     */
    public function remove(Token $token) 
    {
        $this->em->remove($token);
        
        if ($this->shouldFlush()) {
            $this->em->flush();
        }
    }
}
