<?php

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddProductPatch implements DataPatchInterface
{

    protected ModuleDataSetupInterface $setup;
    protected ProductInterfaceFactory $productInterfaceFactory;

    protected ProductRepositoryInterface $productRepository;

    protected State $appState;
    protected EavSetup $eavSetup;
    protected CategoryCollectionFactory $categoryCollectionFactory;
    protected CategoryLinkManagementInterface $categoryLink;

    public function __construct(
        ModuleDataSetupInterface $setup,
        State $appState,
        ProductInterfaceFactory $productInterfaceFactory,
        ProductRepositoryInterface $productRepository,
        EavSetup $eavSetup,
        CategoryLinkManagementInterface $categoryLink,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->setup = $setup;
        $this->appState = $appState;
        $this->productInterfaceFactory = $productInterfaceFactory;
        $this->productRepository = $productRepository;
        $this->eavSetup = $eavSetup;
        $this->categoryLink = $categoryLink;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $this->setup->getConnection()->startSetup();
        $this->appState->emulateAreaCode('adminhtml', [$this, 'execute']);
        $this->setup->getConnection()->endSetup();;
    }

    public function execute()
    {
        $product = $this->productInterfaceFactory->create();
        $attributeSetId = $this->eavSetup->getAttributeSetId(Product::ENTITY, "Default");

        $product->setTypeId(Type::TYPE_SIMPLE)
            ->setAttributeSetId($attributeSetId)
            ->setName("black shirt")
            ->setSku("black_shirt")
            ->setUrlKey('black_shirt')
            ->setPrice(200)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED);


        $product = $this->productRepository->save($product);


        $this->categoryLink->assignProductToCategories($product->getSku(), [3]);
    }
}
