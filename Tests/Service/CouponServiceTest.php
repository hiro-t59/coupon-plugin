<?php
/*
 * This file is part of the Coupon plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Coupon\Tests\Service;

use Eccube\Common\Constant;
use Eccube\Entity\OrderDetail;
use Eccube\Tests\EccubeTestCase;
use Plugin\Coupon\Entity\Coupon;
use Plugin\Coupon\Entity\CouponDetail;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class CouponServiceTest.
 */
class CouponServiceTest extends EccubeTestCase
{
    /**
     * testCreateCoupon.
     */
    public function testCreateCoupon()
    {
        $data = $this->getTestData();

        $this->expected = true;

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->createCoupon($data);

        $this->verify();
    }

    /**
     * testUpdateCoupon.
     */
    public function testUpdateCoupon()
    {
        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $Coupon->setCouponName('クーポン2');

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->updateCoupon($Coupon);

        $this->expected = true;

        $this->verify();
    }

    /**
     * testEnableCoupon.
     */
    public function testEnableCoupon()
    {

        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->enableCoupon($Coupon->getId());

        $this->expected = true;

        $this->verify();
    }

    /**
     * testEnableCouponNot.
     */
    public function testEnableCouponNot()
    {

        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->enableCoupon(1000);

        $this->expected = false;

        $this->verify();
    }

    /**
     * testDeleteCoupon.
     */
    public function testDeleteCoupon()
    {
        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->deleteCoupon($Coupon->getId());

        $this->expected = true;

        $this->verify();
    }

    /**
     * testDeleteCouponNot.
     */
    public function testDeleteCouponNot()
    {
        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->deleteCoupon(1111111111);

        $this->expected = false;

        $this->verify();
    }

    /**
     * testGenerateCouponCd.
     */
    public function testGenerateCouponCd()
    {
        $couponCd = $this->app['eccube.plugin.coupon.service.coupon']->generateCouponCd(20);

        $this->actual = strlen($couponCd);
        $this->expected = 20;

        $this->verify();
    }

    /**
     * testExistsCouponProduct.
     */
    public function testExistsCouponProduct()
    {

        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $Customer = $this->createCustomer();

        $Order = $this->createOrder($Customer);

        $details = $Coupon->getCouponDetails();

        /** @var CouponDetail $CouponDetail */
        $CouponDetail = $details[0];

        $Product = $CouponDetail->getProduct();

        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];

        $OrderDetail = new OrderDetail();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(); // デフォルト課税規則
        $OrderDetail->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity(1)
            ->setTaxRule($TaxRule->getCalcRule()->getId())
            ->setTaxRate($TaxRule->getTaxRate());
        $this->app['orm.em']->persist($OrderDetail);
        $OrderDetail->setOrder($Order);
        $Order->addOrderDetail($OrderDetail);

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->existsCouponProduct($Coupon, $Order);

        $this->expected = true;

        $this->verify();
    }

    /**
     * testExistsCouponProductNot.
     */
    public function testExistsCouponProductNot()
    {
        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $Customer = $this->createCustomer();

        $Order = $this->createOrder($Customer);

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->existsCouponProduct($Coupon, $Order);

        $this->expected = false;

        $this->verify();
    }

    /**
     * testExistsCouponProduct2.
     */
    public function testExistsCouponProduct2()
    {

        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon(2);

        $Customer = $this->createCustomer();

        $Order = $this->createOrder($Customer);

        $details = $Coupon->getCouponDetails();

        /** @var CouponDetail $CouponDetail */
        $CouponDetail = $details[0];

        $Product = $CouponDetail->getProduct();

        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];

        $OrderDetail = new OrderDetail();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(); // デフォルト課税規則
        $OrderDetail->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity(1)
            ->setTaxRule($TaxRule->getCalcRule()->getId())
            ->setTaxRate($TaxRule->getTaxRate());
        $this->app['orm.em']->persist($OrderDetail);
        $OrderDetail->setOrder($Order);
        $Order->addOrderDetail($OrderDetail);

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->existsCouponProduct($Coupon, $Order);

        $this->expected = true;

        $this->verify();
    }

    /**
     * testSaveCouponOrder.
     */
    public function testSaveCouponOrder()
    {

        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $Customer = $this->createCustomer();

        $Order = $this->createOrder($Customer);

        $discount = 200;

        $this->app['security.token_storage']->setToken(
            new UsernamePasswordToken(
                $Customer, null, 'customer', $Customer->getRoles()
            )
        );

        $this->app['eccube.plugin.coupon.service.coupon']->saveCouponOrder($Order, $Coupon, $Coupon->getCouponCd(), $Customer, $discount);

        /** @var \Plugin\Coupon\Entity\CouponCouponOrder $CouponOrder */
        $CouponOrder = $this->app['eccube.plugin.coupon.repository.coupon_order']->findOneBy(array('coupon_cd' => $Coupon->getCouponCd()));

        $this->actual = $discount;
        $this->expected = $CouponOrder->getDiscount();

        $this->verify();
    }

    /**
     * testRecalcOrder.
     */
    public function testRecalcOrder()
    {

        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $Customer = $this->createCustomer();

        $Order = $this->createOrder($Customer);

        $details = $Coupon->getCouponDetails();

        /** @var CouponDetail $CouponDetail */
        $CouponDetail = $details[0];

        $Product = $CouponDetail->getProduct();

        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];

        $OrderDetail = new OrderDetail();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(); // デフォルト課税規則
        $OrderDetail->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity(1)
            ->setTaxRule($TaxRule->getCalcRule()->getId())
            ->setTaxRate($TaxRule->getTaxRate());
        $this->app['orm.em']->persist($OrderDetail);
        $OrderDetail->setOrder($Order);
        $Order->addOrderDetail($OrderDetail);

        $discount = $this->app['eccube.plugin.coupon.service.coupon']->recalcOrder($Order, $Coupon);

        $this->actual = $discount;

        $this->expected = 100;

        $this->verify();
    }

    /**
     * testIsOrderInActiveCoupon.
     */
    public function testIsOrderInActiveCoupon()
    {
        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $Customer = $this->createCustomer();

        $Order = $this->createOrder($Customer);

        $details = $Coupon->getCouponDetails();

        /** @var CouponDetail $CouponDetail */
        $CouponDetail = $details[0];

        $Product = $CouponDetail->getProduct();

        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];

        $orderDetails = $Order->getOrderDetails();
        foreach ($orderDetails as $OrderDetail) {
            $Order->removeOrderDetail($OrderDetail);
        }

        $OrderDetail = new OrderDetail();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(); // デフォルト課税規則
        $OrderDetail->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity(1)
            ->setTaxRule($TaxRule->getCalcRule()->getId())
            ->setTaxRate($TaxRule->getTaxRate());
        $this->app['orm.em']->persist($OrderDetail);
        $OrderDetail->setOrder($Order);
        $Order->addOrderDetail($OrderDetail);

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->isOrderInActiveCoupon($Order);

        $this->expected = true;

        $this->verify();
    }

    /**
     * testIsOrderInActiveCouponNot.
     */
    public function testIsOrderInActiveCouponNot()
    {
        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $Customer = $this->createCustomer();

        $Order = $this->createOrder($Customer);

        $this->actual = $this->app['eccube.plugin.coupon.service.coupon']->isOrderInActiveCoupon($Order);

        $this->expected = false;

        $this->verify();
    }

    /**
     * testGetCouponOrder.
     */
    public function testGetCouponOrder()
    {

        /** @var Coupon $Coupon */
        $Coupon = $this->getCoupon();

        $Customer = $this->createCustomer();

        $Order = $this->createOrder($Customer);

        $discount = 200;

        $this->app['security.token_storage']->setToken(
            new UsernamePasswordToken(
                $Customer, null, 'customer', $Customer->getRoles()
            )
        );

        $this->app['eccube.plugin.coupon.service.coupon']->saveCouponOrder($Order, $Coupon, $Coupon->getCouponCd(), $Customer, $discount);

        $CouponOrder = $this->app['eccube.plugin.coupon.service.coupon']->getCouponOrder($Order->getPreOrderId());

        $this->actual = $CouponOrder->getDiscount();
        $this->expected = 200;

        $this->verify();
    }

    /**
     * getCoupon.
     *
     * @param int $couponType
     *
     * @return Coupon
     */
    private function getCoupon($couponType = 1)
    {
        $data = $this->getTestData($couponType);

        $this->app['eccube.plugin.coupon.service.coupon']->createCoupon($data);

        /** @var Coupon $Coupon */
        $Coupon = $this->app['eccube.plugin.coupon.repository.coupon']->findOneBy(array('coupon_cd' => 'aaaaaaaa'));

        $Product = $this->createProduct();
        $CouponDetail = new CouponDetail();

        $CouponDetail->setCoupon($Coupon);
        $CouponDetail->setCouponType($Coupon->getCouponType());
        $CouponDetail->setUpdateDate($Coupon->getUpdateDate());
        $CouponDetail->setCreateDate($Coupon->getCreateDate());
        $CouponDetail->setDelFlg(Constant::ENABLED);

        $Categories = $Product->getProductCategories();

        /** @var \Eccube\Entity\ProductCategory $Category */
        $ProductCategory = $Categories[0];

        $CouponDetail->setCategory($ProductCategory->getCategory());

        $CouponDetail->setProduct($Product);

        $Coupon->addCouponDetail($CouponDetail);

        return $Coupon;
    }

    /**
     * getTestData.
     *
     * @param int $couponType
     *
     * @return Coupon
     */
    private function getTestData($couponType = 1)
    {
        $Coupon = new Coupon();

        $date1 = new \DateTime();
        $date2 = new \DateTime();

        $Coupon->setCouponCd('aaaaaaaa');
        $Coupon->setCouponType($couponType);
        $Coupon->setCouponName('クーポン');
        $Coupon->setDiscountType(1);
        $Coupon->setCouponUseTime(1);
        $Coupon->setDiscountPrice(100);
        $Coupon->setDiscountRate(10);
        $Coupon->setEnableFlag(1);
        $d1 = $date1->setDate(2016, 1, 1);
        $Coupon->setAvailableFromDate($d1);
        $d2 = $date2->setDate(2016, 12, 31);
        $Coupon->setAvailableToDate($d2);

        return $Coupon;
    }
}
