<?php
/**
 * Copyright © Buhmann. All rights reserved.
 */
declare(strict_types=1);

namespace Buhmann\CatalogSmile\Plugin\Controller;

use Buhmann\Catalog\Plugin\Controller\CategoryView;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use ReflectionException;
use ReflectionMethod;
use Smile\ElasticsuiteCatalog\Block\Navigation\Renderer\PriceSlider;
use Smile\ElasticsuiteCatalog\Block\Navigation\Renderer\Slider;
use Smile\ElasticsuiteCatalog\Model\Layer\Filter\Decimal;
use Smile\ElasticsuiteCatalog\Model\Layer\Filter\Price;

/**
 * Plugin to extend category filter metadata.
 *
 * Adds slider configuration for decimal and price filters
 * from Smile\ElasticsuiteCatalog module. Uses reflection to access
 * the private getConfig method of slider blocks.
 */
class ExtendFilterMetadataPlugin
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param StoreManagerInterface $storeManager Store manager for retrieving currency symbol
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Adds slider configuration to filter metadata.
     *
     * Plugin intercepts the result of extractFilterMetadata method from CategoryView
     * and adds slider configuration for Decimal and Price filter types.
     *
     * @param CategoryView $subject The plugin subject
     * @param array $result Original result of extractFilterMetadata method
     * @param FilterInterface $filter Navigation layer filter object
     * @param LayoutInterface $layout Layout object for block creation
     * @return array Modified result with added slider configuration
     * @throws LocalizedException|NoSuchEntityException|ReflectionException
     */
    public function afterExtractFilterMetadata(
        CategoryView $subject,
        array $result,
        FilterInterface $filter,
        LayoutInterface $layout
    ): array {
        $result['sliderConfig'] = null;

        if ($filter instanceof Decimal || $filter instanceof Price) {
            $result['type'] = 'slider';
            $blockClass = ($filter instanceof Price) ? PriceSlider::class : Slider::class;

            /** @var Slider $sliderBlock */
            $sliderBlock = $layout->createBlock($blockClass);

            if ($sliderBlock) {
                $sliderBlock->render($filter);

                $configMethod = new ReflectionMethod(get_class($sliderBlock), 'getConfig');
                $configMethod->setAccessible(true);
                $result['sliderConfig'] = $configMethod->invoke($sliderBlock);

                if ($filter instanceof Price) {
                    $result['sliderConfig']['currencySymbol'] = $this->storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
                }

                return $result;
            }
        }

        return $result;
    }
}
