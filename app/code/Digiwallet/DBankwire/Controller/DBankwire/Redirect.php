<?php
namespace Digiwallet\DBankwire\Controller\DBankwire;

use Magento\Framework\Controller\ResultFactory;

/**
 * Digiwallet DBankwire Redirect Controller
 *
 * @method GET
 */
class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Digiwallet\DBankwire\Model\DBankwire
     */
    private $dbankwire;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Digiwallet\DBankwire\Model\DBankwire $DBankwire
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Digiwallet\DBankwire\Model\DBankwire $dbankwire,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->dbankwire = $dbankwire;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * When a customer has ordered and redirect to Digiwallet DBankwire gateway.
     *
     * @return void|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /* @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $result = $this->dbankwire->setupPayment();
            // Process to display order thankyou page
            // Show success page
            return $resultRedirect->setPath('dbankwire/dbankwire/success', ['trxid' => $result->getTransactionId()]);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
            $this->logger->critical($e);
        }
        $this->checkoutSession->restoreQuote();
        return $resultRedirect->setPath('checkout/cart');
    }
}
