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
/*
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Expr;
use Lexik\Bundle\FormFilterBundle\Filter\Extension\Type\FilterTypeSharedableInterface;*/
//use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


//use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\Extension\Type\TextFilterType;

//use Doctrine\ORM\EntityRepository;

class PostFilteraaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    //  $name= $options['name'];
    //  print_r($options);
   //   exit(1);
   //$user=$this->buildvalues();
    //          $name="Nom";
        $builder
            ->add(
                'title',
                'filter_text', 
                array(
                    'widget_controls' => false,
                    'widget_control_group' => false,
                    'attr' => array('placeholder' => 'Name'),
                    'condition_pattern' => TextFilterType::STRING_EQUALS
                )
            );
     /*   $builder
        ->add('title',      'filter_text', array(
                'label'         => 'E-mail',
            'condition_pattern' => FilterOperands::OPERAND_SELECTOR,
 ));*/
     /*       ->add('title','filter_text',array(
                'condition_pattern' => FilterOperands::OPERAND_SELECTOR,
        //'condition_pattern' => TextFilterType::SELECT_PATTERN,
    ));*/
     /*  $builder->add('title', 'filter_text', array(
            'apply_filter' => function (QueryBuilder $filterBuilder, Expr $expr, $field, array $values) {

                // add conditions you need :)

            },
        ));*/
          //      ->add('publicationDateStart','filter_datetime_range');
        
               
    /*protected $slug;

    protected $abstract;

    protected $content;

    protected $rawContent;

    protected $contentFormatter;

    protected $tags;

    protected $comments;*/
     
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'post_filter';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
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
}