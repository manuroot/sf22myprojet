<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\NewsBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

      /*  $menu->addChild('Home', array('route' => 'homepage'));*/
        $menu->addChild('About Me', array(
            'route' => 'sonata_news_home',
        /*'routeParameters' => array('id' => 42)*/
        ));
        // ... add more children

        return $menu;
    }
}
