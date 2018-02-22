<?php

namespace Bss\Mostview\Block;

class Mostviewproduct extends \Magento\Catalog\Block\Product\AbstractProduct
{

    const DEFAULT_COLLECTION_SORT_BY = 'name';
    const DEFAULT_COLLECTION_ORDER = 'asc';

    protected $mostViewedCollection;
    protected $wishListHelper;
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';
    protected $_objectManager;
    protected $_eventTypeFactory;
    protected $_productEntityTableName;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $mostViewedCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Reports\Model\Event\TypeFactory $eventTypeFactory,
        \Magento\Wishlist\Helper\Data $wishListHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    )
    {
        $this->_coreResource = $resource;
        $this->urlHelper = $urlHelper;
        $this->stockHelper = $stockHelper;
        $this->mostViewedCollection = $mostViewedCollectionFactory->create();
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_eventTypeFactory = $eventTypeFactory;
        $this->wishListHelper = $wishListHelper;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function getMostViewed()
    {
        return $this->addViewsCountCustom($this->getFromDate(), $this->getToDate());
    }

    public function addViewsCountCustom($from = '', $to = '')
    {
//            die($this->getData('from_date'));
        /**
         * Getting event type id for catalog_product_view event
         */
        $storeId = $this->_storeManager->getStore()->getId();

        $collection = $this->mostViewedCollection
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);

        $eventTypes = $this->_eventTypeFactory->create()->getCollection();
        foreach ($eventTypes as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = (int)$eventType->getId();
                break;
            }
        }


        if ($this->getSortBy() == 'views') {
            $collection->getSelect()->reset()->from(
                ['report_table_views' => $this->mostViewedCollection->getTable('report_event')],
                ['views' => 'COUNT(report_table_views.event_id)']
            )->join(
                ['e' => $this->mostViewedCollection->getProductEntityTableName()],
                'e.entity_id = report_table_views.object_id'
            )->where(
                'report_table_views.event_type_id = ?',
                $productViewEvent
            )->group(
                'e.entity_id'
            )->order(
                'views ' .$this->getSortOrder()
            )->having(
                'COUNT(report_table_views.event_id) > ?',
                0
            );
        } else {
            $collection->getSelect()->reset()->from(
                ['report_table_views' => $this->mostViewedCollection->getTable('report_event')],
                ['views' => 'COUNT(report_table_views.event_id)']
            )->join(
                ['e' => $this->mostViewedCollection->getProductEntityTableName()],
                'e.entity_id = report_table_views.object_id'
            )->where(
                'report_table_views.event_type_id = ?',
                $productViewEvent
            )->group(
                'e.entity_id'
            )->having(
                'COUNT(report_table_views.event_id) > ?',
                0
            );
        }

        if ($this->getSortBy() == 'name') {
            $collection->addAttributeToSort($this->getSortBy(), $this->getSortOrder());
        }

        if ($this->getSortBy() == 'price') {
            $collection->addAttributeToSort($this->getSortBy(), $this->getSortOrder());
        }


        if ($from != '' && $to != '') {
            $collection->getSelect()->where('logged_at >= ?', $from)->where('logged_at <= ?', $to);
        }

        if ($this->getCategories()) {
            $collection->addCategoriesFilter((array('in' => $this->getCategories())));
        }

        if (!$this->getOutOfStock()) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }


        return $collection;
    }

    /**
     * Get product entity table name
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getProductEntityTableName()
    {
        return $this->_productEntityTableName;
    }

    protected function _beforeToHtml()
    {

        $this->setProductCollection($this->getMostViewed());

        return parent::_beforeToHtml(); // TODO: Change the autogenerated stub
    }

    protected function getShowCart()
    {
        if (!$this->hasData('show_add_to_cart')) {
            $this->setData('show_add_to_cart', self::DEFAULT_SHOW_ADDTOCART);
        }
        return (bool)$this->getData('show_add_to_cart');
    }

//    Get Category value
    protected function getCategories()
    {

        return $this->getData('wd_category');

    }

    protected function getFromDate()
    {


        return $this->getData('from_date');
    }

    protected function getToDate()
    {
        return $this->getData('to_date');
    }


    public function getSortBy()
    {
        if (!$this->hasData('collection_sort_by')) {
            $this->setData('collection_sort_by', self::DEFAULT_COLLECTION_SORT_BY);
        }
        return $this->getData('collection_sort_by');
    }

    public function getSortOrder()
    {
        if (!$this->hasData('collection_sort_order')) {
            $this->setData('collection_sort_order', self::DEFAULT_COLLECTION_ORDER);
        }
        return $this->getData('collection_sort_order');
    }


    public function getOutOfStock()
    {
        return $this->getData('out_stock');
    }

//    public function get



    public function getUniqueSliderKey()
    {
        $key = uniqid();
        return $key;
    }


    public function getPagerHtml()
    {
        $total_limit = $this->getNoOfProduct();
        $pagination = $this->getProductsPerPage();
        $page_arr = explode(",", $pagination);
        $limit = [];
        foreach ($page_arr as $page) {
            $limit[$page] = $page;
        }

        if ($this->getProductCollection()->getSize()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Product\Widget\Html\Pager',
                    'mostviewed.pager'
                );

                $this->pager->setAvailableLimit($limit)
                    ->setLimitVarName('mv_limit')
                    ->setPageVarName('mp')
                    ->setShowPerPage(true)
                    ->setTotalLimit($total_limit)
                    ->setCollection($this->getProductCollection());
            }
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }


}