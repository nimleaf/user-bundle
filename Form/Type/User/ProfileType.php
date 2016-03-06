<?php

namespace Imatic\Bundle\UserBundle\Form\Type\User;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends BaseProfileFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('submit', SubmitType::class, [
            'label' => 'profile.edit.submit',
            'translation_domain' => 'FOSUserBundle',
            'attr' => ['class' => 'btn-primary']
        ]);
    }

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildUserForm($builder, $options);
    }
}
