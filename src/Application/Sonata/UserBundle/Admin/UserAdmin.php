<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;
use FOS\UserBundle\Model\UserManagerInterface;

class UserAdmin extends SonataUserAdmin
{
    protected $formOptions = array(
        'validation_groups' => 'Profile'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
            parent::configureListFields($listMapper);
        $listMapper
        ->add('city');
        
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
         parent::configureDatagridFilters($filterMapper);
        $filterMapper
         ->add('city');
                }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
            parent::configureShowFields($showMapper);

         $showMapper
                ->with('Extra-MROOT')
                 ->add('city')
                
            ->end();
    
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
         parent::configureFormFields($formMapper);

        $formMapper
              ->with('Extra-MROOT')
                   ->add('city', 'text', array('required' => false))
            ->end();
   
    }

   
}
