<?php
/**
 * Created by PhpStorm.
 * User: manuele
 * Date: 11/09/14
 * Time: 12:47
 */

class Webgriffe_WebsiteCreator_Model_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * @param $code
     * @param $rootCategoryId
     * @param $currencyCode
     * @param $locale
     * @param $associateProducts
     */
    public function createWebsiteAndStoreAndStoreView($code,
        $rootCategoryId,
        $currencyCode,
        $locale,
        $associateProducts = false
    ) {
        if (
            !Mage::getModel('core/website')->load($code . 'website', 'code')->getId() &&
            !Mage::getModel('core/store_group')->load(strtoupper($code) . ' Website Store', 'name')->getId() &&
            !Mage::getModel('core/store')->load($code, 'code')->getId()
        ) {
            $website = Mage::getModel('core/website');
            $website
                ->setCode($code . 'website')
                ->setName(strtoupper($code) . ' Website')
                ->save();

            $storeGroup = Mage::getModel('core/store_group');
            $storeGroup
                ->setWebsiteId($website->getId())
                ->setName(strtoupper($code) . ' Website Store')
                ->setRootCategoryId($rootCategoryId)
                ->save();

            /** @var $storeView Mage_Core_Model_Store */
            $storeView = Mage::getModel('core/store');
            $storeView->setCode($code)
                ->setWebsiteId($storeGroup->getWebsiteId())
                ->setGroupId($storeGroup->getId())
                ->setName(strtoupper($code) . ' store view')
                ->setIsActive(1)
                ->save();

            $storeGroup->setDefaultStoreId($storeView->getId());

            if ($associateProducts) {
                $allProductsIds = Mage::getModel('catalog/product')->getCollection()->getAllIds();
                $otherWebsiteId = Mage::getModel('core/website')->load($code . 'website', 'code')->getId();
                Mage::getModel('catalog/product_website')->addProducts(array($otherWebsiteId), $allProductsIds);
            }

            $config = Mage::getSingleton('core/config');

            if ($currencyCode) {
                $config->saveConfig('catalog/price/scope', Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE, 'default', 0);
                $config->saveConfig('currency/options/base', $currencyCode, 'websites', $website->getId());
                $config->saveConfig('currency/options/allow', $currencyCode, 'websites', $website->getId());
                $config->saveConfig('currency/options/default', $currencyCode, 'websites', $website->getId());
            }

            if ($locale) {
                $config->saveConfig('general/locale/code', $locale, 'websites', $website->getId());
            }
        }
    }
}
