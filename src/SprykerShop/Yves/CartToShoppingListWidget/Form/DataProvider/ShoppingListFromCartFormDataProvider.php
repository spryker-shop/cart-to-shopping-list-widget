<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CartToShoppingListWidget\Form\DataProvider;

use Generated\Shared\Transfer\ShoppingListFromCartRequestTransfer;
use Spryker\Yves\Kernel\PermissionAwareTrait;
use SprykerShop\Yves\CartToShoppingListWidget\Dependency\Client\CartToShoppingListWidgetToShoppingListClientInterface;
use SprykerShop\Yves\CartToShoppingListWidget\Form\ShoppingListFromCartForm;

class ShoppingListFromCartFormDataProvider
{
    use PermissionAwareTrait;

    protected const GLOSSARY_KEY_CART_ADD_TO_SHOPPING_LIST_FORM_ADD_NEW = 'cart.add-to-shopping-list.form.add_new';

    /**
     * @var \SprykerShop\Yves\CartToShoppingListWidget\Dependency\Client\CartToShoppingListWidgetToShoppingListClientInterface
     */
    protected $shoppingListClient;

    /**
     * @param \SprykerShop\Yves\CartToShoppingListWidget\Dependency\Client\CartToShoppingListWidgetToShoppingListClientInterface $shoppingListClient
     */
    public function __construct(CartToShoppingListWidgetToShoppingListClientInterface $shoppingListClient)
    {
        $this->shoppingListClient = $shoppingListClient;
    }

    /**
     * @param int|null $idQuote
     *
     * @return \Generated\Shared\Transfer\ShoppingListFromCartRequestTransfer
     */
    public function getData(?int $idQuote): ShoppingListFromCartRequestTransfer
    {
        return (new ShoppingListFromCartRequestTransfer)->setIdQuote($idQuote)->setShoppingListName(null);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'data_class' => ShoppingListFromCartRequestTransfer::class,
            ShoppingListFromCartForm::OPTION_SHOPPING_LISTS => $this->getShoppingListCollection(),
        ];
    }

    /**
     * @return array
     */
    protected function getShoppingListCollection(): array
    {
        $shoppingListCollection = [];
        $shoppingListTransferCollection = $this->shoppingListClient->getCustomerShoppingListCollection()->getShoppingLists();

        $shoppingListCollection[self::GLOSSARY_KEY_CART_ADD_TO_SHOPPING_LIST_FORM_ADD_NEW] = '';
        foreach ($shoppingListTransferCollection as $shoppingListTransfer) {
            if ($this->can('WriteShoppingListPermissionPlugin', $shoppingListTransfer->getIdShoppingList())) {
                $shoppingListCollection[$shoppingListTransfer->getName()] = $shoppingListTransfer->getName();
            }
        }

        return $shoppingListCollection;
    }
}
