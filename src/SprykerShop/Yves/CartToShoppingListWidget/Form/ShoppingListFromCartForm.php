<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CartToShoppingListWidget\Form;

use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ShoppingListFromCartForm extends AbstractType
{
    protected const FIELD_ID_QUOTE = 'idQuote';
    protected const FIELD_SHOPPING_LIST_NAME = 'shoppingListName';
    public const FIELD_NEW_SHOPPING_LIST_NAME_INPUT = 'newShoppingListName';
    public const OPTION_SHOPPING_LISTS = 'OPTION_SHOPPING_LISTS';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addQuoteTransferField($builder);
        $this->addShoppingListField($builder, $options);
        $this->addShoppingListNameField($builder);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(static::OPTION_SHOPPING_LISTS);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    protected function addQuoteTransferField(FormBuilderInterface $builder): void
    {
        $builder->add(static::FIELD_ID_QUOTE, HiddenType::class);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    protected function addShoppingListField(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(static::FIELD_SHOPPING_LIST_NAME, ChoiceType::class, [
            'choices' => array_flip($options[static::OPTION_SHOPPING_LISTS]),
            'choices_as_values' => true,
            'expanded' => false,
            'required' => true,
            'label' => false,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    protected function addShoppingListNameField(FormBuilderInterface $builder): void
    {
        $builder->add(static::FIELD_NEW_SHOPPING_LIST_NAME_INPUT, TextType::class, [
            'label' => false,
            'mapped' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'cart.add-to-shopping-list.form.placeholder',
            ],
            'constraints' => [
                new Callback([
                    'callback' => $this->nameValidateCallback($builder),
                ]),
            ],
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \Closure
     */
    protected function nameValidateCallback(FormBuilderInterface $builder): callable
    {
        return function ($object, ExecutionContextInterface $context) use ($builder) {
            $data = $builder->getData();
            if (!$object && !$data[static::FIELD_SHOPPING_LIST_NAME]) {
                $context->buildViolation('cart.add-to-shopping-list.form.error.empty_name')
                    ->addViolation();
            }
        };
    }
}
