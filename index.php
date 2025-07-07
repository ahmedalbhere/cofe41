<?php
require_once 'inc/config.php';
require_once 'inc/functions.php';

$menuItems = getAllMenuItems();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام المبيعات - مطاعم الكبدة</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>نظام المبيعات</h1>
            <div class="restaurant-name">مطاعم الكبدة</div>
        </header>

        <div class="sales-interface">
            <div class="menu-section">
                <div class="categories">
                    <button class="category-btn active" data-category="all">الكل</button>
                    <button class="category-btn" data-category="كبدة">كبدة</button>
                    <button class="category-btn" data-category="حواشي">حواشي</button>
                    <button class="category-btn" data-category="فراخ">فراخ</button>
                </div>

                <div class="menu-items">
                    <?php foreach ($menuItems as $item): ?>
                        <div class="menu-item" 
                             data-id="<?= $item['id'] ?>" 
                             data-category="<?= $item['category'] ?>"
                             data-price="<?= $item['price'] ?>">
                            <h3><?= $item['name'] ?></h3>
                            <p><?= $item['price'] ?> جنيهاً</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bill-section">
                <h2>الفاتورة الحالية</h2>
                <div class="bill-items" id="bill-items">
                    <!-- العناصر المضافة للفاتورة -->
                </div>
                <div class="bill-summary">
                    <div class="total-amount">
                        الإجمالي: <span id="bill-total">0.00</span> جنيهاً
                    </div>
                    <button id="print-bill" class="print-btn">طباعة الفاتورة</button>
                    <button id="new-bill" class="new-btn">فاتورة جديدة</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>
