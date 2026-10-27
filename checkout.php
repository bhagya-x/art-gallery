<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';
require_login();

$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    $_SESSION['flash_error'] = 'Your cart is empty.';
    redirect('gallery.php');
}

$errors = [];
$old = [
    'delivery_address' => current_user()['address'] ?? '',
    'customer_note' => '',
    'demo_card_name' => '',
    'demo_card_number' => '',
    'demo_card_expiry' => '',
    'demo_card_cvc' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        // සිංහල සටහන: මෙය demo card payment simulation එකක් පමණි; real payment gateway එකක් නොවේ.
        $old['delivery_address'] = trim($_POST['delivery_address'] ?? current_user()['address'] ?? '');
        $old['customer_note'] = trim($_POST['customer_note'] ?? '');
        $old['demo_card_name'] = trim($_POST['demo_card_name'] ?? '');
        $old['demo_card_number'] = preg_replace('/\D+/', '', $_POST['demo_card_number'] ?? '');
        $old['demo_card_expiry'] = trim($_POST['demo_card_expiry'] ?? '');
        $old['demo_card_cvc'] = preg_replace('/\D+/', '', $_POST['demo_card_cvc'] ?? '');

        if ($old['delivery_address'] === '') {
            $errors['delivery_address'] = 'Delivery address is required.';
        }

        if ($old['demo_card_name'] === '') {
            $errors['demo_card_name'] = 'Card holder name is required.';
        } elseif (!is_valid_person_name($old['demo_card_name'])) {
            $errors['demo_card_name'] = 'Card holder name can contain letters and spaces only.';
        }

        if (!preg_match('/^[0-9]{16}$/', $old['demo_card_number'])) {
            $errors['demo_card_number'] = 'Card number must contain 16 digits only.';
        }

        if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $old['demo_card_expiry'])) {
            $errors['demo_card_expiry'] = 'Expiry must use MM/YY format.';
        }

        if (!preg_match('/^[0-9]{3}$/', $old['demo_card_cvc'])) {
            $errors['demo_card_cvc'] = 'CVC must contain 3 digits only.';
        }

        if ($errors) {
            throw new RuntimeException('Please correct the highlighted payment fields.');
        }

        $isValidDemoPayment = $old['demo_card_number'] === '4242424242424242'
            && $old['demo_card_name'] !== ''
            && $old['demo_card_expiry'] === '12/30'
            && $old['demo_card_cvc'] === '123';

        if (!$isValidDemoPayment) {
            throw new RuntimeException('Demo payment failed. Please use the demo card details shown on the checkout page.');
        }

        $total = array_reduce($cart, fn($sum, $item) => $sum + ((float)$item['price'] * (int)$item['qty']), 0);
        $address = $old['delivery_address'];
        $note = $old['customer_note'];
        $reference = upload_reference_image('reference_image');
        $orderNumber = 'AG-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, total, status, customer_note, reference_image, delivery_address) VALUES (?, ?, ?, 'Pending', ?, ?, ?)");
        $stmt->execute([current_user()['id'], $orderNumber, $total, $note, $reference, $address]);
        $orderId = (int)$pdo->lastInsertId();

        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, artwork_id, price, quantity) VALUES (?, ?, ?, ?)");
        $statusStmt = $pdo->prepare("UPDATE artworks SET status = 'Reserved' WHERE id = ? AND status = 'Available'");
        foreach ($cart as $item) {
            $itemStmt->execute([$orderId, $item['id'], $item['price'], $item['qty']]);
            $statusStmt->execute([$item['id']]);
        }
        $pdo->commit();

        log_email(current_user()['email'], 'Order Confirmation - ' . $orderNumber, "Your order has been received. Demo payment approved. Total: " . money((float)$total));
        unset($_SESSION['cart']);
        $_SESSION['flash_success'] = 'Demo payment approved. Order placed successfully. Order number: ' . $orderNumber;
        redirect('dashboard.php');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        $_SESSION['flash_error'] = $e->getMessage();
    }
}

$pageTitle = 'Checkout';
$total = array_reduce($cart, fn($sum, $item) => $sum + ((float)$item['price'] * (int)$item['qty']), 0);
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container"><span class="eyebrow">Checkout</span><h1>Place Artwork Order</h1><p>Use the demo card details below to complete a safe payment simulation. No real card payment is processed.</p></div>
</section>
<div class="container section">
    <div class="detail-grid checkout-grid" style="padding:0">
        <form class="form-card validated-form" method="post" enctype="multipart/form-data" data-demo-payment-form novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="form-group"><label>Delivery Address / Contact Address</label><textarea class="form-control checkout-address<?= error_class($errors, 'delivery_address') ?>" name="delivery_address" rows="4" required><?= e($old['delivery_address']) ?></textarea><?= field_error($errors, 'delivery_address') ?></div>
            <div class="form-group"><label>Order Note</label><textarea class="form-control" name="customer_note" placeholder="Write any special requirement or custom request..."><?= e($old['customer_note']) ?></textarea></div>
            <div class="form-group"><label>Reference Image Optional</label><input class="form-control" type="file" name="reference_image" accept="image/png,image/jpeg,image/webp"></div>

            <div class="checkout-pay">
                <div class="demo-payment-head">
                    <div>
                        <span class="eyebrow">Demo Payment</span>
                        <h2>Card Details</h2>
                    </div>
                    <span class="badge badge-success badge-dot">Test Mode</span>
                </div>

                <!-- BIG: live card preview (mirrors the small fields below) -->
                <div class="cc-stage">
                    <div class="credit-card" data-card>
                        <div class="cc-face cc-front">
                            <div class="cc-top">
                                <span class="cc-chip"></span>
                                <span class="cc-brand">Arts<span style="opacity:.7">Pay</span></span>
                            </div>
                            <div class="cc-number" data-cc-number>•••• •••• •••• ••••</div>
                            <div class="cc-bottom">
                                <div><small>Card Holder</small><span class="cc-name" data-cc-name>YOUR NAME</span></div>
                                <div><small>Expires</small><span data-cc-expiry>MM/YY</span></div>
                            </div>
                        </div>
                        <div class="cc-face cc-back">
                            <div class="cc-magstripe"></div>
                            <div class="cc-cvc-row">
                                <small>CVC</small>
                                <div class="cc-cvc-band"><span data-cc-cvc>•••</span></div>
                            </div>
                            <span class="cc-brand">Arts<span style="opacity:.7">Pay</span></span>
                        </div>
                    </div>
                </div>

                <!-- SMALL: hint + input fields -->
                <div class="demo-card">
                    <strong>Demo Card</strong>
                    <span>4242 4242 4242 4242</span>
                    <small>Expiry 12/30 · CVC 123</small>
                </div>
                <button class="btn btn-light btn-small" type="button" data-fill-demo-card>Use Demo Card Details</button>
                <div class="form-group"><label>Name on Card</label><input class="form-control<?= error_class($errors, 'demo_card_name') ?>" type="text" name="demo_card_name" value="<?= e($old['demo_card_name']) ?>" placeholder="Demo Customer" required autocomplete="cc-name" data-validate-name data-error-message="Card holder name can contain letters and spaces only."><?= field_error($errors, 'demo_card_name') ?></div>
                <div class="form-group"><label>Card Number</label><input class="form-control<?= error_class($errors, 'demo_card_number') ?>" type="text" name="demo_card_number" value="<?= e($old['demo_card_number']) ?>" placeholder="4242 4242 4242 4242" required inputmode="numeric" autocomplete="cc-number" maxlength="19" data-validate-card-number data-error-message="Card number must contain numbers only."><?= field_error($errors, 'demo_card_number') ?></div>
                <div class="form-grid">
                    <div class="form-group"><label>Expiry</label><input class="form-control<?= error_class($errors, 'demo_card_expiry') ?>" type="text" name="demo_card_expiry" value="<?= e($old['demo_card_expiry']) ?>" placeholder="12/30" required autocomplete="cc-exp" maxlength="5" data-validate-expiry data-error-message="Expiry must use MM/YY format."><?= field_error($errors, 'demo_card_expiry') ?></div>
                    <div class="form-group"><label>CVC</label><input class="form-control<?= error_class($errors, 'demo_card_cvc') ?>" type="password" name="demo_card_cvc" value="<?= e($old['demo_card_cvc']) ?>" placeholder="123" required inputmode="numeric" autocomplete="cc-csc" maxlength="3" data-validate-cvc data-error-message="CVC must contain 3 digits only."><?= field_error($errors, 'demo_card_cvc') ?></div>
                </div>
                <p class="demo-payment-note">This is only a demo checkout process. Please do not enter real card details.</p>
                <div class="demo-processing" data-demo-processing>
                    <span class="demo-spinner"></span>
                    <strong>Processing demo payment...</strong>
                </div>
            </div>

            <button class="btn btn-dark btn-block" type="submit" data-demo-pay-button>Pay & Confirm Order</button>
        </form>
        <div class="panel order-summary">
            <h2>Order Summary</h2>
            <div class="table-wrap" style="margin-top:18px">
                <table>
                    <thead><tr><th>Artwork</th><th>Qty</th><th>Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr><td><?= e($item['title']) ?></td><td><?= (int)$item['qty'] ?></td><td><?= money((float)$item['price'] * (int)$item['qty']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <h2 style="margin-top:20px">Total: <?= money((float)$total) ?></h2>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('[data-demo-payment-form]');
    const fillButton = document.querySelector('[data-fill-demo-card]');
    const processingBox = document.querySelector('[data-demo-processing]');
    const payButton = document.querySelector('[data-demo-pay-button]');

    // --- Live card preview wiring ---
    const card = document.querySelector('[data-card]');
    const numberEl = document.querySelector('[data-cc-number]');
    const nameEl = document.querySelector('[data-cc-name]');
    const expiryEl = document.querySelector('[data-cc-expiry]');
    const cvcEl = document.querySelector('[data-cc-cvc]');

    const numberInput = form.querySelector('[name="demo_card_number"]');
    const nameInput = form.querySelector('[name="demo_card_name"]');
    const expiryInput = form.querySelector('[name="demo_card_expiry"]');
    const cvcInput = form.querySelector('[name="demo_card_cvc"]');

    function formatNumber(value) {
        const digits = value.replace(/\D+/g, '').slice(0, 16);
        const groups = digits.match(/.{1,4}/g) || [];
        // pad with bullets so the card always shows 16 slots
        const filled = groups.join(' ');
        const remaining = 19 - filled.length;
        return (filled + (remaining > 0 ? ' ' + '•'.repeat(Math.max(0, 16 - digits.length)).match(/.{1,4}/g)?.join(' ') : '')).trim() || '•••• •••• •••• ••••';
    }

    function renderNumber() {
        const digits = numberInput.value.replace(/\D+/g, '').slice(0, 16);
        let out = '';
        for (let i = 0; i < 16; i++) {
            out += (i < digits.length ? digits[i] : '•');
            if ((i + 1) % 4 === 0 && i < 15) out += ' ';
        }
        numberEl.textContent = out;
        // live-format the input itself
        numberInput.value = (digits.match(/.{1,4}/g) || []).join(' ');
    }

    if (numberInput) numberInput.addEventListener('input', renderNumber);
    if (nameInput) nameInput.addEventListener('input', function () {
        nameEl.textContent = nameInput.value.trim() ? nameInput.value.toUpperCase() : 'YOUR NAME';
    });
    if (expiryInput) expiryInput.addEventListener('input', function () {
        let v = expiryInput.value.replace(/\D+/g, '').slice(0, 4);
        if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2);
        expiryInput.value = v;
        expiryEl.textContent = v || 'MM/YY';
    });
    if (cvcInput) {
        cvcInput.addEventListener('input', function () {
            cvcEl.textContent = cvcInput.value ? cvcInput.value.replace(/./g, '•').slice(0, 3) : '•••';
        });
        // flip to back while entering CVC
        cvcInput.addEventListener('focus', function () { card.classList.add('is-flipped'); });
        cvcInput.addEventListener('blur', function () { card.classList.remove('is-flipped'); });
    }

    if (fillButton && form) {
        fillButton.addEventListener('click', function () {
            nameInput.value = 'Demo Customer';
            numberInput.value = '4242 4242 4242 4242';
            expiryInput.value = '12/30';
            cvcInput.value = '123';
            // refresh preview
            renderNumber();
            nameEl.textContent = 'DEMO CUSTOMER';
            expiryEl.textContent = '12/30';
            cvcEl.textContent = '•••';
        });
    }

    if (form && processingBox && payButton) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.processing === '1') {
                return;
            }
            if (!form.checkValidity()) {
                return;
            }
            event.preventDefault();
            form.dataset.processing = '1';
            processingBox.classList.add('is-active');
            payButton.disabled = true;
            payButton.textContent = 'Processing...';
            setTimeout(function () {
                form.submit();
            }, 1200);
        });
    }
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
