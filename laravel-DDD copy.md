

## ディレクトリ

オニオンアーキテクチャ

- app
    - Console
        - Commands　・・・バッチ（プレゼンター）
    - Http
        - Controller ・・・API（プレゼンター）
    - Model

- package
    - UseCase
    - Domain
    - Infra

## 構造（EC）

### 受注取得

- app
    - Console
        - Commands
            - OrderGet.php
    - Http
        - Controller
    - Model
        - Order
        - OrderDetail

- package
    - UseCase
        - OrderReceiveUseCase.php
        ```
            $orderGetter = new OrderGetter();
            $orders = new OrderList();
            $orders = $orderGetter->getRecentOrders();
            $dividableOrders = new DividableOrderList();
            foreach ($orders as $order) {
                $pendingCheckOrder = new PendingCheckOrder($order);
                if ($pendingCheckOrder.isPending())
                    $order->sendPendingMail()
                    continue;
                }
                $failureCheckOrder = new FailureCheckOrder($order);
                if ($failureCheckOrder.isFailure())
                    $order->sendFailureMail()
                    continue;
                }
                $divideCheckOrder = new DivideCheckOrder($order);
                if ($divideCheckOrder.isDivideFailure())
                    $divideCheckOrder->sendDivideFailureMail()
                    continue;
                }
                if ($divideCheckOrder.isDivide())
                    $dividableOrders->add($divideOrder)
                    continue;
                }
                $receivedOrder = new ReceivedOrder($order);
                $receivedOrder = new ReceivedOrder($order);
                $receivedOrder.save();
                $receivedOrder.sendConfirmMail();
            }

            foreach ($dividableOrders as $dividableOrder) {
                if ($dividableOrder->sendDivide()) {
                    $dividedOrder = $orderGetter->getDividedOrders($divideOrder->getOrderId());
                    $divideOrder.save();
                    $divideOrder->sendDivideMail()

                } else {
                    $dividableOrder->sendDivideFailureMail()
                }
            }

        ```

    - Infra
        - AMallOrderGetter implements OrderGetterInterface
            - getRecentOrders(): OrderList
                - XML
            - getDividedOrders(string $orderId): Order
            - next(): Order

        - AMallCheckOrder implements CheckOrderInterface
            - ok(Order)
            - ng(Order)

        - AMallSendMail implements SendMailInterface
            - ok(Order)
            - ng(Order)

        - AMallCheckOrder implements CheckOrderInterface
            - sendMail()

        - AMallPendingCheckOrder implements PendingCheckOrderInterface
            - ok(Order)
            - ng(Order)
            - sendMail()
            - sendCancel()

    - Domain
        - interface OrderGetterInterface
            - getRecentOrders(): OrderList
            - getDividedOrders(string $orderId): Order
            - next(): Order
        - interface OrderDetailGetter
            - getOrderDetail(): OrderDetail
        - OrderList implement Iterator
            - add()
            - Order
                - OrderDetail
                - OrderDetailItemList implement Iterator
                    - OrderDetailItem($order)
            - OrderDetail
                - OrderDetailItemList implement Iterator
                    - OrderDetailItem
        - interface CheckOrderInterface
            - ok(Order)
            - ng(Order)

        - interface SendMailInterface
            - sendMail()

        - interface SendCancelInterface
            - sendMail()

        - interface PendingCheckOrderInterface(Order) implements CheckOrderInterface, SendMailInterface
            - ok(Order)
            - ng(Order)
            - sendMail()

        - interface FailureCheckOrderInterface(Order) implements CheckOrderInterface, SendMailInterface, SendCancelInterface
            - ok(Order)
            - ng(Order)
            - sendMail()
            - sendCancel()

        - interface StockShortageCheckOrderInterface(Order) implements CheckOrderInterface, SendMailInterface, SendCancelInterface
            - ok(Order)
            - ng(Order)
            - sendMail()
            - sendCancel()

        - DivideCheckOrderInterface(Order) implements CheckOrderInterface, SendMailInterface
            - ok(Order)
            - ng(Order)
            - sendMail()

        - DividableOrders(Order) implement Iterator
            - add()
            - next()

            - DividableOrder(Order)
                - sendDivide(): DivideOrder
                - sendDivideFailureMail()

        - DivideOrder(Order)
            - save()
            - sendDivideMail()

        - ReceivedOrder(Order)
            - save(Order)
                - Model.Order.create()
                - Model.OrderDetail.create()
            - sendConfirmMail()

        - CancelOrder(Order)
            - cancel(Order)
            - sendCancelMail()

        - ShippedOrder(Order)
            - ship(Order)
            - sendShippedMail()

        - CompleteOrder(Order)
            - complete(Order)
            - sendCompleteMail()


