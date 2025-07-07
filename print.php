<?php
require_once 'inc/config.php';
require_once 'inc/functions.php';

// معالجة طلب حفظ الفاتورة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        // بدء المعاملة
        $pdo->beginTransaction();
        
        // إضافة الفاتورة
        $stmt = $pdo->prepare("INSERT INTO bills (total) VALUES (?)");
        $stmt->execute([$data['total']]);
        $billId = $pdo->lastInsertId();
        
        // إضافة عناصر الفاتورة
        $stmt = $pdo->prepare("INSERT INTO bill_items (bill_id, item_name, item_price) VALUES (?, ?, ?)");
        
        foreach ($data['items'] as $item) {
            $price = floatval(preg_replace('/[^0-9.]/', '', $item['price']));
            $stmt->execute([$billId, $item['name'], $price]);
        }
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'bill_id' => $billId]);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// عرض الفاتورة للطباعة
if (isset($_GET['bill_id'])) {
    $billId = $_GET['bill_id'];
    
    // جلب بيانات الفاتورة
    $stmt = $pdo->prepare("SELECT * FROM bills WHERE id = ?");
    $stmt->execute([$billId]);
    $bill = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // جلب عناصر الفاتورة
    $stmt = $pdo->prepare("SELECT * FROM bill_items WHERE bill_id = ?");
    $stmt->execute([$billId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // عرض الفاتورة
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>فاتورة مطاعم الكبدة</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                padding: 20px;
                max-width: 400px;
                margin: 0 auto;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .restaurant-name {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 5px;
            }
            .bill-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
            }
            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }
            .items-table th, .items-table td {
                padding: 8px;
                border-bottom: 1px solid #ddd;
                text-align: right;
            }
            .total {
                text-align: left;
                font-size: 18px;
                font-weight: bold;
                margin-top: 15px;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 14px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="restaurant-name">مطاعم الكبدة</div>
            <div>فاتورة مبيعات</div>
        </div>
        
        <div class="bill-info">
            <div>رقم الفاتورة: #<?= $bill['id'] ?></div>
            <div>التاريخ: <?= date('Y-m-d H:i', strtotime($bill['created_at'])) ?></div>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>الصنف</th>
                    <th>السعر</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= $item['item_name'] ?></td>
                        <td><?= number_format($item['item_price'], 2) ?> جنيهاً</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="total">
            الإجمالي: <?= number_format($bill['total'], 2) ?> جنيهاً
        </div>
        
        <div class="footer">
            شكراً لزيارتكم<br>
            هاتف: 0123456789
        </div>
        
        <script>
            window.print();
            setTimeout(() => window.close(), 1000);
        </script>
    </body>
    </html>
    <?php
    exit;
}
?>
