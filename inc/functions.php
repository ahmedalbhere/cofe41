<?php
require_once 'config.php';

// جلب جميع أصناف القائمة
function getAllMenuItems() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM menu_items ORDER BY category, name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// جلب فاتورة معينة
function getBill($billId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM bills WHERE id = ?");
    $stmt->execute([$billId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// جلب عناصر الفاتورة
function getBillItems($billId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM bill_items WHERE bill_id = ?");
    $stmt->execute([$billId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
