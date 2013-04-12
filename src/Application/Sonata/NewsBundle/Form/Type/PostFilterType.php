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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Lexik\Bundle\FormFilterBundle\Filter\Extension\Type\TextFilterType as TextFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;

//use Doctrine\ORM\EntityRepository;

class PostFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->add('title', 'filter_text', 
                 array( 
                     'label'         => 'Titre',
        /* 'widget_controls' => false,
                    'widget_control_group' => false,
                    'attr' => array('placeholder' => 'Name'),*/
            'condition_pattern' => FilterOperands::OPERAND_SELECTOR,
    ));
       /*  $builder->add('startDate', 'date', array(
            'label' => 'Date début',
            'widget' => 'single_text'
        ));*/
         $builder->add('publicationDateStart', 'filter_date_range', array(
             'label'=>'Date de Publication',
    'left_date' => array(
       'widget' => 'single_text'
        /*'time_widget' => 'single_text'*/
    ),
    'right_date' => array(
       'widget' => 'single_text'
       /* 'time_widget' => 'single_text'*/
    ),
));
         
    /*  $builder
            ->add(
                'abstract',
                'filter_text', 
                array(
                    'widget_controls' => false,
                    'widget_control_group' => false,
                    'attr' => array('placeholder' => 'Name'),
                   // 'condition_pattern' => TextFilterType::PATTERN_CONTAINS,
                )
            );*/
          /*  ->add('competition', 'filter_entity', array(
                'class' => 'CommonBundle:Competition',
                'property' => 'name'
            ))*/
       // ;
    }
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
    }
        /*
    public function getDefaultOptions(array $options)
    {
        return array(
            'validation_groups' => array('no_validation')
        );
    }

    public function getName()
    {
        return 'team_filter';
    }*/
}
