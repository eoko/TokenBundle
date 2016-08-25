<?php

namespace Wini\TokenBundle\Manager;

use Doctrine\ORM\EntityRepository;
use Wini\TokenBundle\Entity\Token;
use Wini\Manager\AbstractFlushManager;
use Doctrine\ORM\EntityManager;

class TokenManager extends AbstractFlushManager {

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
        $this->repo = $this->em->getRepository(Token::class);
    }
    
    /**
     * Va créer une token
     * @param mixed $data
     * @param integer $duration Durée de vie de la token en secondes
     * @param integer|null $type Type de la token
     * @param DateTime $start Date de début de vie de la token
     * @return Token
     */
    public function create($data = null, $duration = null, $type = null, $start = null) {
        $token = new Token();
        $token->setData($data)
              ->setType($type)
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
     * Récupère la token grace a la string de base
     * @param string $token
     * @return null|object
     */
    public function getToken($token)
    {
        return $this->repo->findOneBy([ 'token' => $token ]);
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
