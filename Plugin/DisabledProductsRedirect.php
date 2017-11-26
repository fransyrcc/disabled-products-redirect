<?php
/**
 * Sysforall
 *
 * @category  Sysforall
 * @package   sysforall/module-disabledproductsredirect
 * @version   1.2.0
 * @author    Fransy
 */
namespace Sysforall\DisabledProductsRedirect\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use \Magento\Catalog\Controller\Product as ProductController;

class DisabledProductsRedirect
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryInterface;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $resultRedirectFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryInterface
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryInterface,
        ManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        ScopeConfigInterface $scopeConfig,
        Http $request
    ) {
        $this->productRepository = $productRepository;
        $this->categoryInterface = $categoryInterface;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }
    /**
     * @param ProductController $subject
     */
    public function aroundExecute(ProductController $subject, callable $proceed)
    {
        $productId = (int) $this->request->getParam('id');
        $product =  $this->productRepository->getById($productId);
        if ($product->isDisabled()) {
            $cats = $product->getCategoryIds();
            if ($cats) {
                $message = $this->getMessage();
                $firstCategoryId = $cats[0];
                $category = $this->categoryInterface->get($firstCategoryId);
                $categoryUrl = $category->getUrl();
                $this->messageManager->addNoticeMessage($message);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setHttpResponseCode(301);
                return $resultRedirect->setPath($categoryUrl);
            }
        } else {
            return $proceed();
        }
    }
    private function getMessage()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $message =  $this->scopeConfig->getValue(
            'sysforall/disabled_products_redirect/redirection_message',
            $storeScope
        );
        if (!$message) {
            $message = __('The product you tried to view is not available but here are some other options instead');
        }
        return $message;
    }
}
