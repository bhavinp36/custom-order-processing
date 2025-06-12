<?php
namespace Vendor\CustomOrderProcessing\Ui\Component\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Vendor\CustomOrderProcessing\Model\ResourceModel\StatusLog\CollectionFactory;

class StatusLogDataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * StatusLogDataProvider constructor.
     *
     * @param string            $name
     * @param string            $primaryFieldName
     * @param string            $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array             $meta
     * @param array             $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data for UI component.
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            
            return $this->loadedData;
        }
        
        $items = $this->collection->toArray()['items'];
        $this->loadedData = [
            'totalRecords' => $this->collection->getSize(),
            'items' => $items,
        ];

        return $this->loadedData;
    }
}
