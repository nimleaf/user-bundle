<?php

namespace Imatic\Bundle\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @var string
     */
    private $userClass;

    public function __construct($userClass)
    {
        $this->userClass = $userClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.new_password'),
                'second_options' => array('label' => 'form.new_password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('email')
            ->add('enabled')
            ->add('groups')
            ->add('save', 'submit', ['attr' => ['class' => 'btn-primary']]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'ImaticUserBundleUser',
            'data_class' => $this->userClass,
            'validation_groups' => function (FormInterface $form) {
                    $user = $form->getData();
                    if ($user->getId()) {
                        return array('Profile');
                    } else {
                        return array('Profile', 'ChangePassword');
                    }
                },
            'empty_data' => function () {
                    return new $this->userClass;
                }
        ));
    }

    public function getName()
    {
        return 'imatic_user_user';
    }
}