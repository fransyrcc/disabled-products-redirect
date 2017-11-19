<?php
/**
 * Sysforall
 *
 * @category  Sysforall
 * @package   sysforall/module-disabledproductsredirect
 * @version   1.0.0
 * @author    Fransy
 */
namespace Sysforall\DisabledProductsRedirect\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class DisabledProductsRedirect
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryInterface;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryInterface
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryInterface,
        ManagerInterface $messageManager,
        ResponseFactory $responseFactory,
        ScopeConfigInterface $scopeConfig
    ) { 
        $this->productRepository = $productRepository;
        $this->categoryInterface = $categoryInterface;
        $this->messageManager = $messageManager;
        $this->responseFactory = $responseFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundPrepareAndRender( \Magento\Catalog\Helper\Product\View $subject, callable $proceed, Page $resultPage, $productId, $controller, $params = null )
    {
        $_product =  $this->productRepository->getById($productId);
        $message = '';
    
        if($_product->getStatus() === '2') {
            $cats = $_product->getCategoryIds();
            if($cats) {
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $message = $this->scopeConfig->getValue('sysforall/disabled_products_redirect/redirection_message', $storeScope);
                $firstCategoryId = $cats[0];
                $category = $this->categoryInterface->get($firstCategoryId);
                $category_url = $category->getUrl();
                   
                $responseRedirect = $this->responseFactory->create();
                $responseRedirect->setRedirect($category_url)->sendResponse('301');
                $this->messageManager->addNoticeMessage($message);
            }
        }
        else {
            $proceed( $resultPage, $productId, $controller, $params);
        }
    }
}