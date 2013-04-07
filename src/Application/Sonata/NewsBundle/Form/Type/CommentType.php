<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\NewsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;




use Doctrine\ORM\EntityRepository;

class CommentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['truc']="Votre Nom";
   //$user=$this->buildvalues();
        $builder
            ->add('name',null,array('label'=>'Nom','data'=>$options['truc']))
               
            ->add('email', null, array('required' => false))
            ->add('url', null, array('required' => false))
            ->add('message')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_post_comment';
    }
    
     /*public function buildvalues()
    {
    
     $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $user_security = $this->container->get('security.context');
        //if( $user_security->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
        if ($user_security->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $user_id = $user->getId();
        } else {
            $user_id = 0;
        }
        return $user_id;
    }*/
}
