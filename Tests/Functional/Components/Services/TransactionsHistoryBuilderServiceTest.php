<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SwagPaymentPayPalUnified\Tests\Functional\Components\Services;

use SwagPaymentPayPalUnified\Components\Services\TransactionHistoryBuilderService;

class TransactionsHistoryBuilderServiceTest extends \PHPUnit_Framework_TestCase
{
    public function test_service_available()
    {
        $this->assertNotNull(Shopware()->Container()->get('paypal_unified.transaction_history_builder_service'));
    }

    public function test_getSalesHistory_maxAmount()
    {
        /** @var TransactionHistoryBuilderService $historyBuilderService */
        $historyBuilderService = Shopware()->Container()->get('paypal_unified.transaction_history_builder_service');
        $testPaymentData = $this->getTestSalePaymentDetails();

        $testHistory = $historyBuilderService->getTransactionHistory($testPaymentData);
        $this->assertEquals(16.939999999999998, $testHistory['maxRefundableAmount']);
    }

    public function test_getSalesHistory_count()
    {
        /** @var TransactionHistoryBuilderService $historyBuilderService */
        $historyBuilderService = Shopware()->Container()->get('paypal_unified.transaction_history_builder_service');
        $testPaymentData = $this->getTestSalePaymentDetails();

        $testHistory = $historyBuilderService->getTransactionHistory($testPaymentData);
        $this->assertCount(4, $testHistory);
    }

    public function test_getSalesHistory_first_entry()
    {
        /** @var TransactionHistoryBuilderService $historyBuilderService */
        $historyBuilderService = Shopware()->Container()->get('paypal_unified.transaction_history_builder_service');
        $testPaymentData = $this->getTestSalePaymentDetails();

        $testSale = $historyBuilderService->getTransactionHistory($testPaymentData)[0];
        $this->assertEquals(45.94, $testSale['amount']);
        $this->assertEquals('TEST1', $testSale['id']);
        $this->assertEquals('partially_refunded', $testSale['state']);
        $this->assertEquals('2017-01-31T09:53:36Z', $testSale['create_time']);
        $this->assertEquals('2017-01-31T13:07:06Z', $testSale['update_time']);
        $this->assertEquals('EUR', $testSale['currency']);
    }

    public function test_getAuthenticationHistory_count()
    {
        /** @var TransactionHistoryBuilderService $historyBuilderService */
        $historyBuilderService = Shopware()->Container()->get('paypal_unified.transaction_history_builder_service');
        $testPaymentData = $this->getTestAuthenticationPaymentDetails();

        $history = $historyBuilderService->getTransactionHistory($testPaymentData);
        $this->assertCount(11, $history);
    }

    public function test_getOrderHistory_count()
    {
        /** @var TransactionHistoryBuilderService $historyBuilderService */
        $historyBuilderService = Shopware()->Container()->get('paypal_unified.transaction_history_builder_service');
        $testPaymentData = $this->getTestOrderPaymentDetails();

        $history = $historyBuilderService->getTransactionHistory($testPaymentData);
        $this->assertCount(5, $history);
    }

    public function test_getTransactionHistory_exception()
    {
        /** @var TransactionHistoryBuilderService $historyBuilderService */
        $historyBuilderService = Shopware()->Container()->get('paypal_unified.transaction_history_builder_service');
        $testPaymentData = $this->getTestSalePaymentDetails();
        $testPaymentData['intent'] = 'ERROR';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not parse history from an unknown payment type');
        $historyBuilderService->getTransactionHistory($testPaymentData);
    }

    public function test_getSalesHistory_last_entry()
    {
        /** @var TransactionHistoryBuilderService $historyBuilderService */
        $historyBuilderService = Shopware()->Container()->get('paypal_unified.transaction_history_builder_service');
        $testPaymentData = $this->getTestSalePaymentDetails();

        $testSale = $historyBuilderService->getTransactionHistory($testPaymentData)[2];
        $this->assertEquals(-24.00, $testSale['amount']);
        $this->assertEquals('TEST3', $testSale['id']);
        $this->assertEquals('completed', $testSale['state']);
        $this->assertEquals('2017-01-31T13:06:44Z', $testSale['create_time']);
        $this->assertEquals('2017-01-31T13:07:06Z', $testSale['update_time']);
        $this->assertEquals('EUR', $testSale['currency']);
    }

    /**
     * @return array
     */
    private function getTestSalePaymentDetails()
    {
        return require __DIR__ . '/_fixtures/PaymentFixtureSale.php';
    }

    /**
     * @return array
     */
    private function getTestAuthenticationPaymentDetails()
    {
        return require __DIR__ . '/_fixtures/PaymentFixtureAuthentication.php';
    }

    /**
     * @return array
     */
    private function getTestOrderPaymentDetails()
    {
        return require __DIR__ . '/_fixtures/PaymentFixtureOrder.php';
    }
}
