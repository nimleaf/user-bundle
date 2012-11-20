<?php
namespace Imatic\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;

class UserAdmin extends Admin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    protected $formOptions = array(
        'validation_groups' => 'Profile'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('fullname')
            ->add('username')
            ->add('email')
            ->add('enabled', null, array('required' => false))
            ->add('roles', 'sonata_security_roles', array(
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'translation_domain' => 'roles'
        ));
    }

    protected function configureShowFields(ShowMapper $filter)
    {
        $filter
            ->add('fullname')
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('roles');
    }

    protected function configureDatagridFilters(DatagridMapper $dataGridMapper)
    {
        $dataGridMapper
            ->add('fullname')
            ->add('username')
            ->add('email')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $actions = array(
            'view' => array(),
            'edit' => array(),
        );
        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $actions['impersonating'] = array('template' => 'ImaticUserBundle:Admin:Field/impersonating.html.twig');
        }

        $listMapper
            ->add('fullname')
            ->addIdentifier('username')
            ->add('email')
            ->add('enabled', null, array('editable' => true))
            ->add('_action', 'actions', array('actions' => $actions)
        );
    }

    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }
}
