document.addEventListener('DOMContentLoaded', function() {
    // تصفية الأصناف حسب التصنيف
    const categoryBtns = document.querySelectorAll('.category-btn');
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.dataset.category;
            filterMenuItems(category);
        });
    });

    // إضافة أصناف للفاتورة
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        // النقر مرة لإضافة صنف
        item.addEventListener('click', function() {
            addToBill(this);
        });
        
        // النقر مرتين لإضافة صنف مرتين
        item.addEventListener('dblclick', function() {
            addToBill(this);
            addToBill(this);
        });
    });

    // زر طباعة الفاتورة
    document.getElementById('print-bill').addEventListener('click', printBill);
    
    // زر فاتورة جديدة
    document.getElementById('new-bill').addEventListener('click', newBill);
});

function filterMenuItems(category) {
    const allItems = document.querySelectorAll('.menu-item');
    
    allItems.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function addToBill(itemElement) {
    const itemId = itemElement.dataset.id;
    const itemName = itemElement.querySelector('h3').textContent;
    const itemPrice = parseFloat(itemElement.dataset.price);
    
    // إنشاء عنصر الفاتورة
    const billItem = document.createElement('div');
    billItem.className = 'bill-item';
    billItem.dataset.id = itemId;
    billItem.dataset.price = itemPrice;
    billItem.innerHTML = `
        <span class="item-name">${itemName}</span>
        <span class="item-price">${itemPrice.toFixed(2)} جنيهاً</span>
        <button class="remove-item">&times;</button>
    `;
    
    // إضافة حدث لإزالة العنصر
    billItem.querySelector('.remove-item').addEventListener('click', function() {
        billItem.remove();
        updateBillTotal();
    });
    
    document.getElementById('bill-items').appendChild(billItem);
    updateBillTotal();
}

function updateBillTotal() {
    const billItems = document.querySelectorAll('.bill-item');
    let total = 0;
    
    billItems.forEach(item => {
        total += parseFloat(item.dataset.price);
    });
    
    document.getElementById('bill-total').textContent = total.toFixed(2);
}

function printBill() {
    const billItems = document.querySelectorAll('.bill-item');
    if (billItems.length === 0) {
        alert('الفاتورة فارغة!');
        return;
    }
    
    // تجهيز بيانات الفاتورة
    const billData = {
        items: [],
        total: document.getElementById('bill-total').textContent
    };
    
    billItems.forEach(item => {
        billData.items.push({
            name: item.querySelector('.item-name').textContent,
            price: item.querySelector('.item-price').textContent
        });
    });
    
    // إرسال البيانات للخادم
    fetch('print.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(billData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // فتح نافذة الطباعة
            window.open(`print.php?bill_id=${data.bill_id}`, '_blank');
        } else {
            alert('حدث خطأ أثناء حفظ الفاتورة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء الاتصال بالخادم');
    });
}

function newBill() {
    if (confirm('هل تريد بدء فاتورة جديدة؟ سيتم مسح الفاتورة الحالية.')) {
        document.getElementById('bill-items').innerHTML = '';
        document.getElementById('bill-total').textContent = '0.00';
    }
}
