<?php

namespace App\Security\Voter;
use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductVoter extends Voter
{
    const NEW = 'PRODUCT_NEW';
    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    private $security;
    /**
     * @inheritDoc
     */
    public function  __construct(Security $security){
        $this->security =$security;
    }

    protected function supports(string $attribute, $product): bool
    {
       if (!in_array($attribute,[self::NEW,self::EDIT,self::DELETE])){
           return false;
       }
       if (!$product instanceof Product){
           return false;
       }
       return true;
    }


    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $product, TokenInterface $token): bool
    {
        // on récupère l'utilisateur à partir du token
        $user = $token->getUser();

        if (!$user instanceof UserInterface) return false;

        // on vérifie si l'utilisateur est admin
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        //on vérifie les permissions
        switch($attribute){
            case self::NEW:
                //on vérifie s'il peut faire un nouveau produit
                return $this->canNew();
                break;
            case self::EDIT:
                //on vérifie s'il peut éditer produit
                return $this->canEdit();
                break;
            case self::DELETE:
                //on vérifie s'il peut supprimer un produit
                return $this->canDelete();
                break;

        }
        
    }
    private function canNew(){
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
    private function canEdit(){
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
    private function canDelete(){
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
}