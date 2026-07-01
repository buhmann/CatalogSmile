<?php
/**
 * Copyright © Buhmann. All rights reserved.
 */
namespace Buhmann\CatalogSmile\Plugin\Block;

use Smile\ElasticsuiteCatalog\Block\Navigation as SubjectBlock;
use Buhmann\Catalog\ViewModel\LayeredNavigation as CatalogViewModel;

class Navigation
{
    /**
     * @var CatalogViewModel
     */
    private CatalogViewModel $viewModel;

    /**
     * @param CatalogViewModel $viewModel
     */
    public function __construct(CatalogViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
    }

    /**
     * Replacing data with current data from ElasticsuiteCatalog to custom AJAX component if AJAX navigation is enabled
     *
     * @param SubjectBlock $subject
     * @param string $result
     * @return string
     */
    public function afterGetTemplate(SubjectBlock $subject, string $result): string
    {
        if ($this->viewModel->isAjaxNavEnabled()) {
            $subject->setData('active', $subject->getActiveFilters());
        }

        return $result;
    }
}
