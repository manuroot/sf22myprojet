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
                ->add('title', null, array('label' => 'Titre du Post'))
                ->add('abstract', 'textarea', array(
                    'label' => 'Résumé',
                    'attr' => array(
                        'cols' => "40",
                        'class' => "tinymce",
                        )))
                ->add('content', 'textarea', array(
                    'label' => 'Description',
                    'attr' => array(
                        'cols' => "40",
                        //   'rows' => "15",
                        'class' => 'tinymce',
                        )))
                ->add('tags')

                /* ->add('tags', 'sonata_type_model', array(
                  'required' => false,
                  'expanded' => true,
                  'multiple' => true,
                  )) */
//->add('image', 'sonata_type_model', array(), array('view'=>'list','edit' => 'list'))
           /*    
->add('image', 'sonata_type_model_list', array('required' => false, 'label' => 'Image'
                ),
                    array('link_parameters' => array
                    (
                        'provider' => 'sonata.media.provider.image',
                        'context'  => 'default'
                    )
            ))
                	
*/
    ->add('image', 'sonata_type_model_list', array('required' => false),
                    array('link_parameters'=>array('context'=>'default',
                   'provider'=>'sonata.media.provider.image')))
           
    ->add('galleryHasMedias')
        /*, 'sonata_type_collection', array(
                'by_reference' => false
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position',
                'link_parameters' => array('context' => 'default')
            ))*/



                // ->add('createdAt')
                ->add('enabled')
                ->add('author')
                 ->add('image')
               /* ->add('image', 'sonata_media_type', array('required' => true,
                    'cascade_validation' => true,
                    'context' => 'user',
                    'provider' => 'sonata.media.provider.image'
                ))*/
                ->add('category', null, array('label' => 'Categorie'))
                ->add('commentsEnabled')
                ->add('commentsCloseAt', 'datetime', array(
                    'label' => 'Date Fermeture des Commentaires',
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'yyyy-MM-dd HH:mm',
                    'widget_addon' => array(
                        'icon' => 'time',
                        'type' => 'prepend'
                    ), 'required' => false,
                ))
        //->add('commentsCloseAt')

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
