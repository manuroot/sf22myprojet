<?php

namespace Application\Sonata\NewsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\EntityRepository;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\NewsBundle\Model\CommentManagerInterface;

use Knp\Menu\ItemInterface as MenuItemInterface;


class EpostType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
        /*
         *  protected $title;

    protected $slug;

    protected $abstract;

    protected $content;

    protected $rawContent;

    protected $contentFormatter;

    protected $tags;

    protected $comments;

    protected $enabled;

    protected $publicationDateStart;

    protected $createdAt;

    protected $updatedAt;

    protected $commentsEnabled = true;



    protected $commentsDefaultStatus;

    protected $image;


         */
               ->add('title')
                  ->add('abstract')
                ->add('content')
               ->add('tags')
                
                /* ->add('tags', 'sonata_type_model', array(
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ))*/
                
                
               // ->add('createdAt')
        ->add('enabled')
                ->add('author')
                  ->add('image')
                ->add('category')
                ->add('commentsEnabled')
                 ->add('commentsCloseAt')
                    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\NewsBundle\Entity\Post'
        ));
    }

    public function getName() {
        return 'application_sonata_eposttype';
    }

}
